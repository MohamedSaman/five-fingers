<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionProduct;
use App\Models\ProductionStage;
use App\Models\ProductionStone;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProductList extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Design Image
    public $design_image;
    public $image_url = '';

    // Filters
    #[Url(as: 'q')]
    public $search = '';

    #[Url(as: 'status')]
    public $statusFilter = '';

    #[Url(as: 'metal')]
    public $metalFilter = '';

    #[Url(as: 'stage')]
    public $stageFilter = '';

    // Modal state
    public $showCreateModal = false;

    // Create Job Form fields
    public $title = '';
    public $sku = '';
    public $category = 'Ring';
    public $metal_type = '18K Yellow Gold';
    public $target_metal_weight = 7.5;
    public $metal_unit_price_per_g = 18500;
    public $markup_percentage = 35;
    public $is_master_piece_required = false;
    public $description = '';

    // Dynamic gemstone rows
    public $stones = [];

    // Stage selection checkboxes (keyed by step number 1..15)
    public $selectedStages = [];

    public function mount()
    {
        $this->resetCreationForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setStatus($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function resetCreationForm()
    {
        $this->resetErrorBag();
        $nextId = (ProductionProduct::max('id') ?? 0) + 1;
        $this->sku = 'FF-JOB-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $this->title = '';
        $this->category = 'Ring';
        $this->metal_type = '18K Yellow Gold';
        $this->target_metal_weight = 7.5;
        $this->metal_unit_price_per_g = 18500;
        $this->markup_percentage = 35;
        $this->is_master_piece_required = false;
        $this->description = '';
        $this->design_image = null;
        $this->image_url = '';

        $this->stones = [[
            'stone_type' => 'Blue Sapphire',
            'cut_shape' => 'Oval',
            'dimensions_mm' => '7x5',
            'quantity' => 1,
            'carat_weight' => 2.15,
            'setting_method' => 'hand_prong',
            'stone_unit_cost' => 125000,
        ]];


        $this->selectedStages = [];
        foreach (ProductionProduct::standardStages() as $num => $stage) {
            $this->selectedStages[$num] = $stage['default_required'];
        }
    }

    public function removeDesignImage()
    {
        $this->design_image = null;
        $this->image_url = '';
    }

    public function updatedIsMasterPieceRequired($value)
    {
        // Master piece => mold step + wax injection
        $this->selectedStages[2] = (bool) $value;
        if ($value) {
            $this->selectedStages[3] = true;
        }
    }

    public function updatedMetalType($value)
    {
        // White gold / silver => rhodium plating
        $this->selectedStages[14] = str_contains($value, 'White') || str_contains($value, 'Silver');
    }

    public function updatedStones()
    {
        // Wax-set stones => enable wax setting step automatically
        $hasWax = collect($this->stones)->contains(fn ($s) => ($s['setting_method'] ?? '') === 'wax_cast');
        if ($hasWax) {
            $this->selectedStages[4] = true;
        }
    }

    public function addStoneRow()
    {
        $this->stones[] = [
            'stone_type' => 'Diamond',
            'cut_shape' => 'Round Brilliant',
            'dimensions_mm' => '1.5',
            'quantity' => 10,
            'carat_weight' => 0.2,
            'setting_method' => 'pave',
            'stone_unit_cost' => 4500,
        ];
    }

    public function removeStoneRow($index)
    {
        unset($this->stones[$index]);
        $this->stones = array_values($this->stones);
    }

    public function openModal()
    {
        $this->resetCreationForm();
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
    }

    /**
     * Live estimate shown in the creation dialog footer.
     */
    public function getEstimateProperty(): array
    {
        $metal = (float) $this->target_metal_weight * (float) $this->metal_unit_price_per_g;
        $stones = collect($this->stones)->sum(fn ($s) => (float) ($s['stone_unit_cost'] ?? 0) * (int) ($s['quantity'] ?? 1));
        $base = $metal + $stones;

        return [
            'metal' => $metal,
            'stones' => $stones,
            'base' => $base,
            'msrp' => $base * (1 + ((float) $this->markup_percentage / 100)),
        ];
    }

    public function createProduct()
    {
        $this->validate([
            'sku' => 'required|string|unique:production_products,sku',
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'metal_type' => 'required|string',
            'target_metal_weight' => 'required|numeric|min:0.001',
            'metal_unit_price_per_g' => 'required|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|min:0',
            'design_image' => 'nullable|image|max:8192',
        ], [], [
            'title' => 'piece title',
            'target_metal_weight' => 'target weight',
            'metal_unit_price_per_g' => 'gold rate',
            'design_image' => 'design image',
        ]);

        if (count(array_filter($this->selectedStages)) === 0) {
            $this->addError('selectedStages', 'Select at least one stage.');
            return;
        }

        // Store design image if uploaded
        $finalImageUrl = null;
        if ($this->design_image) {
            $path = $this->design_image->store('production/designs', 'public');
            $finalImageUrl = '/storage/' . $path;
        } elseif (!empty($this->image_url)) {
            $finalImageUrl = $this->image_url;
        }

        $product = DB::transaction(function () use ($finalImageUrl) {
            $product = ProductionProduct::create([
                'sku' => $this->sku,
                'title' => $this->title,
                'description' => $this->description,
                'category' => $this->category,
                'metal_type' => $this->metal_type,
                'target_metal_weight' => $this->target_metal_weight,
                'metal_unit_price_per_g' => $this->metal_unit_price_per_g,
                'is_master_piece_required' => $this->is_master_piece_required,
                'image_url' => $finalImageUrl,
                'markup_percentage' => $this->markup_percentage ?: 0,
                'status' => 'in_production',
                'current_stage_number' => 1,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->stones as $stone) {
                if (empty($stone['stone_type'])) {
                    continue;
                }
                $qty = max(1, (int) ($stone['quantity'] ?? 1));
                $unit = (float) ($stone['stone_unit_cost'] ?? 0);
                ProductionStone::create([
                    'production_product_id' => $product->id,
                    'stone_type' => $stone['stone_type'],
                    'cut_shape' => $stone['cut_shape'] ?? 'Round Brilliant',
                    'dimensions_mm' => $stone['dimensions_mm'] ?? '',
                    'quantity' => $qty,
                    'carat_weight' => (float) ($stone['carat_weight'] ?? 0),
                    'setting_method' => $stone['setting_method'] ?? 'hand_prong',
                    'stone_unit_cost' => $unit,
                    'total_stone_cost' => $unit * $qty,
                    'is_set' => false,
                ]);
            }

            $first = true;
            foreach (ProductionProduct::standardStages() as $num => $meta) {
                if (empty($this->selectedStages[$num])) {
                    continue;
                }
                ProductionStage::create([
                    'production_product_id' => $product->id,
                    'step_number' => $num,
                    'step_name' => $meta['name'],
                    'is_required' => true,
                    'status' => $first ? 'in_progress' : 'pending',
                    'started_at' => $first ? now() : null,
                ]);
                if ($first) {
                    $product->update(['current_stage_number' => $num]);
                    $first = false;
                }
            }

            $product->recalculateCosts();

            return $product;
        });

        return $this->redirectRoute('production.lifecycle', ['id' => $product->id], navigate: false);
    }

    public function render()
    {
        $query = ProductionProduct::with(['stages', 'stones']);

        if ($this->search !== '') {
            $s = '%' . $this->search . '%';
            $query->where(fn ($q) => $q->where('title', 'like', $s)
                ->orWhere('sku', 'like', $s)
                ->orWhere('category', 'like', $s));
        }
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }
        if ($this->metalFilter !== '') {
            $query->where('metal_type', $this->metalFilter);
        }
        if ($this->stageFilter !== '') {
            $query->where('current_stage_number', $this->stageFilter)->where('status', 'in_production');
        }

        $active = ProductionProduct::where('status', 'in_production');

        return view('livewire.production.admin.product-list', [
            'products' => $query->orderByDesc('id')->paginate(10),
            'standardStages' => ProductionProduct::standardStages(),
            'kpi' => [
                'active' => (clone $active)->count(),
                'metal' => (float) (clone $active)->sum('target_metal_weight'),
                'wip' => (float) (clone $active)->sum('total_manufacturing_cost'),
                'qc' => ProductionProduct::where('status', 'qc_review')->count(),
                'done' => ProductionProduct::where('status', 'completed')->count(),
                'value' => (float) ProductionProduct::sum('recommended_selling_price'),
            ],
        ])->layout('components.layouts.production', ['title' => 'Jewellery Jobs']);
    }
}
