<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'title',
        'description',
        'category',
        'metal_type',
        'target_metal_weight',
        'is_master_piece_required',
        'mold_id',
        'image_url',
        'status',
        'current_stage_number',
        'metal_unit_price_per_g',
        'total_metal_cost',
        'total_stone_cost',
        'total_labor_cost',
        'total_expenses_cost',
        'total_metal_loss_cost',
        'total_manufacturing_cost',
        'markup_percentage',
        'recommended_selling_price',
        'created_by',
    ];

    protected $casts = [
        'target_metal_weight' => 'decimal:3',
        'is_master_piece_required' => 'boolean',
        'metal_unit_price_per_g' => 'decimal:2',
        'total_metal_cost' => 'decimal:2',
        'total_stone_cost' => 'decimal:2',
        'total_labor_cost' => 'decimal:2',
        'total_expenses_cost' => 'decimal:2',
        'total_metal_loss_cost' => 'decimal:2',
        'total_manufacturing_cost' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'recommended_selling_price' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(ProductionStage::class, 'production_product_id')->orderBy('step_number');
    }

    public function stones(): HasMany
    {
        return $this->hasMany(ProductionStone::class, 'production_product_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(StageExpense::class, 'production_product_id');
    }

    public function metalLogs(): HasMany
    {
        return $this->hasMany(StageMetalLog::class, 'production_product_id');
    }

    /**
     * Recalculate and persist all costs for this jewelry item
     */
    public function recalculateCosts(): void
    {
        $stoneCost = (float) $this->stones()->sum('total_stone_cost');
        $laborCost = (float) $this->expenses()->where('expense_type', 'labor')->sum('amount');
        $otherExpenses = (float) $this->expenses()->where('expense_type', '!=', 'labor')->sum('amount');
        $metalLossCost = (float) $this->metalLogs()->sum('loss_monetary_value');

        // Metal cost = target metal weight * unit price per gram (or first casting weight)
        $rawMetalCost = (float) ($this->target_metal_weight * $this->metal_unit_price_per_g);

        $totalManufacturing = $rawMetalCost + $stoneCost + $laborCost + $otherExpenses + $metalLossCost;
        $markup = (float) $this->markup_percentage;
        $sellingPrice = $totalManufacturing * (1 + ($markup / 100));

        $this->update([
            'total_metal_cost' => $rawMetalCost,
            'total_stone_cost' => $stoneCost,
            'total_labor_cost' => $laborCost,
            'total_expenses_cost' => $otherExpenses,
            'total_metal_loss_cost' => $metalLossCost,
            'total_manufacturing_cost' => $totalManufacturing,
            'recommended_selling_price' => $sellingPrice,
        ]);
    }

    /**
     * Standard 15 stages master dictionary
     */
    public static function standardStages(): array
    {
        return [
            1 => [
                'name' => '3D CAD Design',
                'description' => 'Computer-aided 3D modeling, prongs, clearances, stone layout, and render approval.',
                'worker_role' => 'CAD Designer',
                'default_required' => true,
            ],
            2 => [
                'name' => '3D Print Master & Vulcanized Mold',
                'description' => 'High-resolution wax/resin print, vulcanized rubber mold for repeat casting.',
                'worker_role' => 'Mold Maker',
                'default_required' => false, // Only if master piece requested
            ],
            3 => [
                'name' => 'Wax Injection Replicas',
                'description' => 'Injecting molten wax into rubber mold under vacuum pressure.',
                'worker_role' => 'Wax Worker',
                'default_required' => true,
            ],
            4 => [
                'name' => 'Cast-in-Place Wax Stone Setting',
                'description' => 'Pre-setting diamonds and hard sapphires directly into wax pattern.',
                'worker_role' => 'Stone Setter',
                'default_required' => false, // Hand set by default
            ],
            5 => [
                'name' => 'Wax Tree Sprueing & Flask Mounting',
                'description' => 'Soldering wax models to central sprue trunk inside stainless steel flask.',
                'worker_role' => 'Caster Assistant',
                'default_required' => true,
            ],
            6 => [
                'name' => 'Flask Investing (Plaster Mixing)',
                'description' => 'Mixing gypsum powder with deionized water, vacuum debubbling, flask filling.',
                'worker_role' => 'Casting Technician',
                'default_required' => true,
            ],
            7 => [
                'name' => 'Kiln Burnout (10-12 hr Profile)',
                'description' => 'Stepped kiln ramp from 150°C to 730°C to eliminate all wax without residue.',
                'worker_role' => 'Kiln Operator',
                'default_required' => true,
            ],
            8 => [
                'name' => 'Metal Casting (Gold/Platinum)',
                'description' => 'Induction vacuum casting or centrifugal pour into preheated investment flask.',
                'worker_role' => 'Master Caster',
                'default_required' => true,
            ],
            9 => [
                'name' => 'Quench, Clean & Acid Pickle',
                'description' => 'Water blast quenching, plaster removal, and ultrasonic acid pickling.',
                'worker_role' => 'Caster Assistant',
                'default_required' => true,
            ],
            10 => [
                'name' => 'Tree Cutting & Casting QC',
                'description' => 'Separating raw pieces from central trunk, checking for shrinkage porosity or voids.',
                'worker_role' => 'QC Auditor',
                'default_required' => true,
            ],
            11 => [
                'name' => 'Filing, Assembly & Bench Goldsmithing',
                'description' => 'Hand filing sprue stubs, ring sizing, welding components, and magnetic pin tumbling.',
                'worker_role' => 'Bench Jeweler',
                'default_required' => true,
            ],
            12 => [
                'name' => 'Traditional Hand Stone Setting',
                'description' => 'Microscope prong, bezel, channel, or pavé setting for precious gems and diamonds.',
                'worker_role' => 'Master Stone Setter',
                'default_required' => true,
            ],
            13 => [
                'name' => 'Polishing & Ultrasonic Cleaning',
                'description' => 'Multi-step buffing with Tripoli, White Rouge, and final ultrasonic steam wash.',
                'worker_role' => 'Master Polisher',
                'default_required' => true,
            ],
            14 => [
                'name' => 'Electro-Rhodium Plating',
                'description' => 'Electrolytic micro-coating of rhodium for mirror-bright white gold or silver finish.',
                'worker_role' => 'Plating Technician',
                'default_required' => false, // Default false unless white gold / silver
            ],
            15 => [
                'name' => 'Final QC, Hallmarking & Vault Stock',
                'description' => 'Microscopic inspection, laser hallmark stamping (Au 750 / 925), certification, and vault tag.',
                'worker_role' => 'Chief Gemologist / Manager',
                'default_required' => true,
            ],
        ];
    }
}
