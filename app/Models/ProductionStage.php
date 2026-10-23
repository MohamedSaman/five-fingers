<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_product_id',
        'step_number',
        'step_name',
        'is_required',
        'status',
        'assigned_user_id',
        'started_at',
        'completed_at',
        'notes',
        'checkpoints_passed',
        'stage_data',
    ];

    protected $casts = [
        'step_number' => 'integer',
        'is_required' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'checkpoints_passed' => 'array',
        'stage_data' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductionProduct::class, 'production_product_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(StageExpense::class, 'production_stage_id');
    }

    public function metalLogs(): HasMany
    {
        return $this->hasMany(StageMetalLog::class, 'production_stage_id');
    }
}
