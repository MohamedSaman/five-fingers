<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_stage_id',
        'production_product_id',
        'expense_type',
        'description',
        'amount',
        'recorded_by',
        'expense_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProductionStage::class, 'production_stage_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductionProduct::class, 'production_product_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
