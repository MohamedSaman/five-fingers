<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionProduct;
use App\Models\ProductionStage;
use App\Models\ProductionStone;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $metalFilter = '';
    public $stageFilter = '';

    // Modal state
    public $showCreateModal = false;

    // Create Job Form fields
    public $title = '';
    public $sku = '';
    public $category = 'Ring';
    public $metal_type = '18K Yellow Gold';
    public $target_metal_weight = 8.50;
    public $metal_unit_price_per_g = 18500;
    public $markup_percentage = 35.0;
    public $is_master_piece_required = false;
    public $description = '';

    // Dynamic Gemstone rows
    public $stones = [];

    // Stage Selection Checkboxes (keyed by step_number: 1..15)
    public $selectedStages = [];

    public function mount()
    {
        $this->statusFilter = request()->get('status', '');
        $this->stageFilter = request()->get('stage', '');

        $this->resetCreationForm();
    }

    public function resetCreationForm()
    {
        // Generate automatic SKU
        $nextId = (ProductionProduct::max('id') ?? 0) + 1;
        $this->sku = 'FF-JOB-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $this->title = '';
        $this->category = 'Ring';
        $this->metal_type = '18K Yellow Gold';
        $this->target_metal_weight = 7.50;
        $this->metal_unit_price_per_g = 18500;
        $this->markup_percentage = 35.0;
        $this->is_master_piece_required = false;
        $this->description = '';

        // Default 1 gemstone row
        $this->stones = [
            [
                'stone_type' => 'Blue Sapphire',
                'cut_shape' => 'Oval',
                'dimensions_mm' => '7x5 mm',
                'quantity' => 1,
                'carat_weight' => 2.15,
                'setting_method' => 'hand_prong',
                'stone_unit_cost' => 125000,
            ]
        ];

        // Initialize default stages based on master dictionary
        $this->selectedStages = [];
        foreach (ProductionProduct::standardStages() as $stepNum => $stage) {
            $this->selectedStages[$stepNum] = $stage['default_required'];
        }
    }

    public function updatedIsMasterPieceRequired($value)
    {
        // If master piece is checked, automatically enable Step 2 (Master & Mold)
        $this->selectedStages[2] = (bool) $value;
    }

    public function updatedMetalType($value)
    {
        // If White Gold or Silver, automatically enable Step 14 (Rhodium Plating)
        if (str_contains($value, 'White') || str_contains($value, 'Silver')) {
            $this->selectedStages[14] = true;
        }
    }

    public function addStoneRow()
    {
        $this->stones[] = [
            'stone_type' => 'Diamond',
            'cut_shape' => 'Round Brilliant',
            'dimensions_mm' => '1.8 mm',
            'quantity' => 10,
            'carat_weight' => 0.25,
            'setting_method' => 'hand_prong',
            'stone_unit_cost' => 45000,
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

    public function createProduct()
    {
        $this->validate([
            'sku' => 'required|string|unique:production_products,sku',
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'metal_type' => 'required|string',
            'target_metal_weight' => 'required|numeric|min:0.1',
            'metal_unit_price_per_g' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () {
            // 1. Create Product
            $product = ProductionProduct::create([
                'sku' => $this->sku,
                'title' => $this->title,
                'description' => $this->description,
                'category' => $this->category,
                'metal_type' => $this->metal_type,
                'target_metal_weight' => $this->target_metal_weight,
                'metal_unit_price_per_g' => $this->metal_unit_price_per_g,
                'is_master_piece_required' => $this->is_master_piece_required,
                'markup_percentage' => $this->markup_percentage,
                'status' => 'in_production',
                'current_stage_number' => 1,
                'created_by' => auth()->id(),
            ]);

            // 2. Create Stones
            foreach ($this->stones as $stone) {
                if (!empty($stone['stone_type'])) {
                    ProductionStone::create([
                        'production_product_id' => $product->id,
                        'stone_type' => $stone['stone_type'],
                        'cut_shape' => $stone['cut_shape'] ?? 'Round Brilliant',
                        'dimensions_mm' => $stone['dimensions_mm'] ?? '',
                        'quantity' => (int) ($stone['quantity'] ?? 1),
                        'carat_weight' => (float) ($stone['carat_weight'] ?? 0),
                        'setting_method' => $stone['setting_method'] ?? 'hand_prong',
                        'stone_unit_cost' => (float) ($stone['stone_unit_cost'] ?? 0),
                        'total_stone_cost' => (float) ($stone['stone_unit_cost'] ?? 0),
                        'is_set' => false,
                    ]);
                }
            }

            // 3. Create only Selected Lifecycle Stages
            $allStages = ProductionProduct::standardStages();
            $firstStepActivated = false;

            foreach ($allStages as $stepNum => $stageMeta) {
                $isRequired = !empty($this->selectedStages[$stepNum]);
                if ($isRequired) {
                    $status = 'pending';
                    $startedAt = null;

                    // First active stage begins in_progress immediately
                    if (!$firstStepActivated) {
                        $status = 'in_progress';
                        $startedAt = now();
                        $firstStepActivated = true;
                        $product->update(['current_stage_number' => $stepNum]);
                    }

                    ProductionStage::create([
                        'production_product_id' => $product->id,
                        'step_number' => $stepNum,
                        'step_name' => $stageMeta['name'],
                        'is_required' => true,
                        'status' => $status,
                        'started_at' => $startedAt,
                    ]);
                }
            }

            // 4. Initial cost recalculation
            $product->recalculateCosts();

            // Redirect to 3-column lifecycle workspace
            return redirect()->route('production.lifecycle', $product->id);
        });
    }

    public function render()
    {
        $query = ProductionProduct::with(['stages', 'stones', 'expenses', 'metalLogs']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->metalFilter)) {
            $query->where('metal_type', $this->metalFilter);
        }

        if (!empty($this->stageFilter)) {
            $query->where('current_stage_number', $this->stageFilter);
        }

        $products = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.production.admin.product-list', [
            'products' => $products,
            'standardStages' => ProductionProduct::standardStages(),
        ])->layout('components.layouts.production', [
            'title' => 'Jewelry Manufacturing Jobs'
        ]);
    }
}
