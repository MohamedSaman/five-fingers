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

    // Right-panel form inputs
    public $assigned_user_id;
    public $metal_input_weight_g = 0;
    public $metal_output_weight_g = 0;
    public $metal_scrap_g = 0;
    public $metal_dust_g = 0;
    public $labor_cost = 0;
    public $consumables_cost = 0;
    public $stage_notes = '';
    public $checkpoints = [];

    // Stones set during this step
    public $selectedStoneIds = [];

    public function mount($id)
    {
        $this->productId = $id;
        $product = ProductionProduct::with(['stages', 'stones', 'expenses', 'metalLogs'])->findOrFail($id);

        // Select the active in_progress stage, or the first stage
        $activeStage = $product->stages->firstWhere('status', 'in_progress') ?? $product->stages->first();
        if ($activeStage) {
            $this->selectStage($activeStage->id);
        }
    }

    public function selectStage($stageId)
    {
        $this->selectedStageId = $stageId;
        $stage = ProductionStage::with(['expenses', 'metalLogs'])->findOrFail($stageId);

        $this->assigned_user_id = $stage->assigned_user_id;
        $this->stage_notes = $stage->notes ?? '';
        $this->checkpoints = $stage->checkpoints_passed ?? [];

        // Pre-fill metal logs if already entered
        $latestMetalLog = $stage->metalLogs()->latest()->first();
        if ($latestMetalLog) {
            $this->metal_input_weight_g = (float) $latestMetalLog->input_weight_g;
            $this->metal_output_weight_g = (float) $latestMetalLog->output_weight_g;
            $this->metal_scrap_g = (float) $latestMetalLog->scrap_recovered_g;
            $this->metal_dust_g = (float) $latestMetalLog->dust_recovered_g;
        } else {
            // Default input weight based on product target weight for casting/filing
            $product = ProductionProduct::find($this->productId);
            $this->metal_input_weight_g = (float) ($product->target_metal_weight ?? 0);
            $this->metal_output_weight_g = (float) ($product->target_metal_weight ?? 0);
            $this->metal_scrap_g = 0;
            $this->metal_dust_g = 0;
        }

        // Pre-fill expenses
        $this->labor_cost = (float) $stage->expenses()->where('expense_type', 'labor')->sum('amount');
        $this->consumables_cost = (float) $stage->expenses()->where('expense_type', '!=', 'labor')->sum('amount');

        // Stones selection
        $product = ProductionProduct::with('stones')->find($this->productId);
        $this->selectedStoneIds = $product->stones->where('is_set', true)->pluck('id')->toArray();
    }

    public function getNetLossProperty()
    {
        $input = (float) $this->metal_input_weight_g;
        $output = (float) $this->metal_output_weight_g;
        $scrap = (float) $this->metal_scrap_g;
        $dust = (float) $this->metal_dust_g;

        return max(0, $input - ($output + $scrap + $dust));
    }

    public function getLossPercentageProperty()
    {
        $input = (float) $this->metal_input_weight_g;
        if ($input > 0) {
            return round(($this->netLoss / $input) * 100, 2);
        }
        return 0;
    }

    public function getLossCostProperty()
    {
        $product = ProductionProduct::find($this->productId);
        $spotPrice = (float) ($product->metal_unit_price_per_g ?? 0);
        return round($this->netLoss * $spotPrice, 2);
    }

    public function completeAndAdvanceStage()
    {
        $stage = ProductionStage::findOrFail($this->selectedStageId);
        $product = ProductionProduct::with('stages')->findOrFail($this->productId);

        DB::transaction(function () use ($stage, $product) {
            // 1. Update current stage record
            $stage->update([
                'assigned_user_id' => $this->assigned_user_id,
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $this->stage_notes,
                'checkpoints_passed' => $this->checkpoints,
            ]);

            // 2. Record or update Stage Expenses
            if ($this->labor_cost > 0) {
                StageExpense::updateOrCreate(
                    [
                        'production_stage_id' => $stage->id,
                        'expense_type' => 'labor',
                    ],
                    [
                        'production_product_id' => $product->id,
                        'description' => "Artisan Labor: " . $stage->step_name,
                        'amount' => $this->labor_cost,
                        'recorded_by' => auth()->id(),
                        'expense_date' => now(),
                    ]
                );
            }

            if ($this->consumables_cost > 0) {
                StageExpense::updateOrCreate(
                    [
                        'production_stage_id' => $stage->id,
                        'expense_type' => 'consumable',
                    ],
                    [
                        'production_product_id' => $product->id,
                        'description' => "Consumables & Materials: " . $stage->step_name,
                        'amount' => $this->consumables_cost,
                        'recorded_by' => auth()->id(),
                        'expense_date' => now(),
                    ]
                );
            }

            // 3. Record Metal Tracking Log if metal weights were provided
            if ($this->metal_input_weight_g > 0) {
                $spotPrice = (float) ($product->metal_unit_price_per_g ?? 0);
                StageMetalLog::updateOrCreate(
                    [
                        'production_stage_id' => $stage->id,
                    ],
                    [
                        'production_product_id' => $product->id,
                        'metal_type' => $product->metal_type,
                        'input_weight_g' => $this->metal_input_weight_g,
                        'output_weight_g' => $this->metal_output_weight_g,
                        'scrap_recovered_g' => $this->metal_scrap_g,
                        'dust_recovered_g' => $this->metal_dust_g,
                        'spot_price_per_g' => $spotPrice,
                        'recorded_by' => auth()->id(),
                    ]
                );
            }

            // 4. Update stones if in setting stage
            if (in_array($stage->step_number, [4, 12])) {
                ProductionStone::where('production_product_id', $product->id)
                    ->whereIn('id', $this->selectedStoneIds)
                    ->update(['is_set' => true]);
            }

            // 5. Find next pending stage in order
            $nextStage = $product->stages
                ->where('step_number', '>', $stage->step_number)
                ->where('status', 'pending')
                ->first();

            if ($nextStage) {
                $nextStage->update([
                    'status' => 'in_progress',
                    'started_at' => now(),
                ]);
                $product->update(['current_stage_number' => $nextStage->step_number]);
                $this->selectStage($nextStage->id);
            } else {
                // All stages complete! Move product to completed status
                $product->update([
                    'status' => 'completed',
                    'current_stage_number' => $stage->step_number,
                ]);
            }

            // 6. Recalculate total product costs
            $product->fresh()->recalculateCosts();
        });

        session()->flash('success', "Stage {$stage->step_number} ({$stage->step_name}) completed successfully!");
    }

    public function markDefect($reason = 'Casting porosity or defect detected')
    {
        $stage = ProductionStage::findOrFail($this->selectedStageId);
        $stage->update([
            'status' => 'failed',
            'notes' => $this->stage_notes . "\n[DEFECT REPORTED]: " . $reason,
        ]);

        $product = ProductionProduct::findOrFail($this->productId);
        $product->update(['status' => 'qc_review']);

        session()->flash('error', "Defect flagged for Stage {$stage->step_number}. QC Review required.");
    }

    public function render()
    {
        $product = ProductionProduct::with(['stages.assignedUser', 'stages.expenses', 'stages.metalLogs', 'stones'])->findOrFail($this->productId);
        $selectedStage = ProductionStage::with(['assignedUser', 'expenses', 'metalLogs'])->findOrFail($this->selectedStageId);
        $artisans = User::where('role', 'staff')->orWhere('role', 'admin')->get();

        return view('livewire.production.admin.product-lifecycle', [
            'product' => $product,
            'selectedStage' => $selectedStage,
            'artisans' => $artisans,
            'standardStages' => ProductionProduct::standardStages(),
        ])->layout('components.layouts.production', [
            'title' => $product->sku . ' - Lifecycle Workspace'
        ]);
    }
}
