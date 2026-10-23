<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionProduct;
use App\Models\ProductionStage;
use App\Models\ProductionStone;
use App\Models\StageExpense;
use App\Models\StageMetalLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductLifecycle extends Component
{
    public $productId;
    public $selectedStageId;

    // Right-panel inputs
    public $assigned_user_id = '';
    public $metal_input_weight_g = 0;
    public $metal_output_weight_g = 0;
    public $metal_scrap_g = 0;
    public $metal_dust_g = 0;
    public $hours_spent = 0;
    public $labor_cost = 0;
    public $consumables_cost = 0;
    public $stage_notes = '';
    public $checkpoints = [];
    public $stageData = [];
    public $selectedStoneIds = [];

    public function mount($id)
    {
        $this->productId = $id;
        $product = ProductionProduct::with('stages')->findOrFail($id);

        $active = $product->stages->firstWhere('status', 'in_progress')
            ?? $product->stages->firstWhere('status', 'failed')
            ?? $product->stages->last();

        if ($active) {
            $this->selectStage($active->id);
        }
    }

    public function selectStage($stageId)
    {
        $this->resetErrorBag();
        $stage = ProductionStage::with(['expenses', 'metalLogs'])
            ->where('production_product_id', $this->productId)
            ->findOrFail($stageId);
        $product = ProductionProduct::with('stones')->findOrFail($this->productId);

        $this->selectedStageId = $stage->id;
        $this->assigned_user_id = $stage->assigned_user_id ?? '';
        $this->stage_notes = $stage->notes ?? '';
        $this->checkpoints = $stage->checkpoints_passed ?? [];
        $this->stageData = $stage->stage_data ?? [];
        $this->hours_spent = (float) ($this->stageData['hours_spent'] ?? 0);

        // Metal: use saved log, otherwise carry forward the previous stage's output (chain of custody)
        $log = $stage->metalLogs->sortByDesc('id')->first();
        if ($log) {
            $this->metal_input_weight_g = (float) $log->input_weight_g;
            $this->metal_output_weight_g = (float) $log->output_weight_g;
            $this->metal_scrap_g = (float) $log->scrap_recovered_g;
            $this->metal_dust_g = (float) $log->dust_recovered_g;
        } else {
            $prev = StageMetalLog::where('production_product_id', $this->productId)
                ->whereHas('stage', fn ($q) => $q->where('step_number', '<', $stage->step_number))
                ->orderByDesc('id')
                ->first();
            $carry = $prev ? (float) $prev->output_weight_g : (float) $product->target_metal_weight;
            $this->metal_input_weight_g = $carry;
            $this->metal_output_weight_g = $carry;
            $this->metal_scrap_g = 0;
            $this->metal_dust_g = 0;
        }

        $this->labor_cost = (float) $stage->expenses->where('expense_type', 'labor')->sum('amount');
        $this->consumables_cost = (float) $stage->expenses->where('expense_type', '!=', 'labor')->sum('amount');
        $this->selectedStoneIds = $product->stones->where('is_set', true)->pluck('id')->map(fn ($v) => (string) $v)->toArray();
    }

    /* ---------- Live computed values ---------- */

    public function getNetLossProperty(): float
    {
        return max(0, (float) $this->metal_input_weight_g - ((float) $this->metal_output_weight_g + (float) $this->metal_scrap_g + (float) $this->metal_dust_g));
    }

    public function getLossPercentageProperty(): float
    {
        $in = (float) $this->metal_input_weight_g;
        return $in > 0 ? round($this->netLoss / $in * 100, 2) : 0;
    }

    public function getLossCostProperty(): float
    {
        $rate = (float) ProductionProduct::whereKey($this->productId)->value('metal_unit_price_per_g');
        return round($this->netLoss * $rate, 2);
    }

    /* ---------- Persistence ---------- */

    protected function persistStage(ProductionStage $stage, ProductionProduct $product): void
    {
        $data = array_filter($this->stageData, fn ($v) => $v !== '' && $v !== null);
        $data['hours_spent'] = (float) $this->hours_spent;

        $stage->update([
            'assigned_user_id' => $this->assigned_user_id ?: null,
            'notes' => $this->stage_notes,
            'checkpoints_passed' => $this->checkpoints,
            'stage_data' => $data,
        ]);

        foreach (['labor' => [$this->labor_cost, 'Artisan labour'], 'consumable' => [$this->consumables_cost, 'Consumables']] as $type => [$amount, $label]) {
            if ((float) $amount > 0) {
                StageExpense::updateOrCreate(
                    ['production_stage_id' => $stage->id, 'expense_type' => $type],
                    [
                        'production_product_id' => $product->id,
                        'description' => "{$label} · {$stage->step_name}",
                        'amount' => (float) $amount,
                        'recorded_by' => auth()->id(),
                        'expense_date' => now(),
                    ]
                );
            } else {
                StageExpense::where('production_stage_id', $stage->id)->where('expense_type', $type)->delete();
            }
        }

        if (in_array($stage->step_number, ProductionProduct::metalTrackingStages()) && (float) $this->metal_input_weight_g > 0) {
            StageMetalLog::updateOrCreate(
                ['production_stage_id' => $stage->id],
                [
                    'production_product_id' => $product->id,
                    'metal_type' => $product->metal_type,
                    'input_weight_g' => (float) $this->metal_input_weight_g,
                    'output_weight_g' => (float) $this->metal_output_weight_g,
                    'scrap_recovered_g' => (float) $this->metal_scrap_g,
                    'dust_recovered_g' => (float) $this->metal_dust_g,
                    'spot_price_per_g' => (float) $product->metal_unit_price_per_g,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        if (in_array($stage->step_number, ProductionProduct::stoneSettingStages())) {
            ProductionStone::where('production_product_id', $product->id)
                ->whereIn('id', $this->selectedStoneIds)
                ->update(['is_set' => true]);
        }
    }

    protected function validateStage(): void
    {
        $this->validate([
            'metal_input_weight_g' => 'nullable|numeric|min:0',
            'metal_output_weight_g' => 'nullable|numeric|min:0',
            'metal_scrap_g' => 'nullable|numeric|min:0',
            'metal_dust_g' => 'nullable|numeric|min:0',
            'hours_spent' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'consumables_cost' => 'nullable|numeric|min:0',
        ]);
    }

    public function saveStage()
    {
        $this->validateStage();
        $stage = ProductionStage::findOrFail($this->selectedStageId);
        $product = ProductionProduct::findOrFail($this->productId);

        DB::transaction(function () use ($stage, $product) {
            $this->persistStage($stage, $product);
            $product->fresh()->recalculateCosts();
        });

        $this->dispatch('toast', type: 'success', message: "Saved · {$stage->step_name}");
    }

    public function completeAndAdvanceStage()
    {
        $this->validateStage();
        if (!$this->assigned_user_id) {
            $this->addError('assigned_user_id', 'Assign the artisan who did this work.');
            return;
        }

        $stage = ProductionStage::findOrFail($this->selectedStageId);
        $product = ProductionProduct::with('stages')->findOrFail($this->productId);
        $next = null;

        DB::transaction(function () use ($stage, $product, &$next) {
            $this->persistStage($stage, $product);
            $stage->update([
                'status' => 'completed',
                'started_at' => $stage->started_at ?? now(),
                'completed_at' => now(),
            ]);

            $next = $product->stages
                ->where('step_number', '>', $stage->step_number)
                ->whereIn('status', ['pending'])
                ->sortBy('step_number')
                ->first();

            if ($next) {
                $next->update(['status' => 'in_progress', 'started_at' => now()]);
                $product->update(['current_stage_number' => $next->step_number, 'status' => 'in_production']);
            } else {
                $product->update(['status' => 'completed', 'current_stage_number' => $stage->step_number]);
            }

            $product->fresh()->recalculateCosts();
        });

        $this->dispatch('toast', type: 'success', message: $next
            ? "Stage {$stage->step_number} complete → {$next->step_name}"
            : 'All stages complete — piece moved to vault');

        if ($next) {
            $this->selectStage($next->id);
        }
    }

    public function markDefect()
    {
        $stage = ProductionStage::findOrFail($this->selectedStageId);
        $product = ProductionProduct::findOrFail($this->productId);

        DB::transaction(function () use ($stage, $product) {
            $this->persistStage($stage, $product);
            $stage->update([
                'status' => 'failed',
                'notes' => trim($this->stage_notes . "\n[Defect reported " . now()->format('d M H:i') . ']'),
            ]);
            $product->update(['status' => 'qc_review']);
        });

        $this->stage_notes = $stage->fresh()->notes;
        $this->dispatch('toast', type: 'error', message: "Defect flagged on {$stage->step_name} — job moved to QC review");
    }

    public function resumeStage()
    {
        $stage = ProductionStage::findOrFail($this->selectedStageId);
        $stage->update(['status' => 'in_progress', 'started_at' => $stage->started_at ?? now()]);
        ProductionProduct::whereKey($this->productId)->update(['status' => 'in_production', 'current_stage_number' => $stage->step_number]);
        $this->dispatch('toast', type: 'success', message: "Rework started on {$stage->step_name}");
    }

    public function render()
    {
        $product = ProductionProduct::with(['stages.assignedUser', 'stages.expenses', 'stages.metalLogs', 'stones'])->findOrFail($this->productId);
        $selectedStage = $product->stages->firstWhere('id', $this->selectedStageId) ?? $product->stages->first();

        $totalHours = $product->stages->sum(fn ($s) => (float) ($s->stage_data['hours_spent'] ?? 0));
        $done = $product->stages->where('status', 'completed')->count();

        return view('livewire.production.admin.product-lifecycle', [
            'product' => $product,
            'selectedStage' => $selectedStage,
            'artisans' => User::whereIn('role', ['staff', 'admin'])->orderBy('name')->get(['id', 'name', 'role']),
            'standardStages' => ProductionProduct::standardStages(),
            'stageFields' => ProductionProduct::stageFields()[$selectedStage->step_number] ?? [],
            'stageChecks' => ProductionProduct::stageChecks()[$selectedStage->step_number] ?? [],
            'stageIcons' => ProductionProduct::stageIcons(),
            'tracksMetal' => in_array($selectedStage->step_number, ProductionProduct::metalTrackingStages()),
            'setsStones' => in_array($selectedStage->step_number, ProductionProduct::stoneSettingStages()),
            'totalHours' => $totalHours,
            'progress' => $product->stages->count() ? round($done / $product->stages->count() * 100) : 0,
            'doneCount' => $done,
        ])->layout('components.layouts.production', [
            'title' => $product->sku . ' · ' . $product->title,
            'fullWidth' => true,
        ]);
    }
}
