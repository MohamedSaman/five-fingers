<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionStone extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_product_id',
        'stone_type',
        'cut_shape',
        'dimensions_mm',
        'quantity',
        'carat_weight',
        'setting_method',
        'stone_unit_cost',
        'total_stone_cost',
        'is_set',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'carat_weight' => 'decimal:3',
        'stone_unit_cost' => 'decimal:2',
        'total_stone_cost' => 'decimal:2',
        'is_set' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductionProduct::class, 'production_product_id');
    }
}
