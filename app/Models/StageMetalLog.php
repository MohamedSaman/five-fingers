<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageMetalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_stage_id',
        'production_product_id',
        'metal_type',
        'input_weight_g',
        'output_weight_g',
        'scrap_recovered_g',
        'dust_recovered_g',
        'net_loss_g',
        'loss_percentage',
        'spot_price_per_g',
        'loss_monetary_value',
        'recorded_by',
    ];

    protected $casts = [
        'input_weight_g' => 'decimal:3',
        'output_weight_g' => 'decimal:3',
        'scrap_recovered_g' => 'decimal:3',
        'dust_recovered_g' => 'decimal:3',
        'net_loss_g' => 'decimal:3',
        'loss_percentage' => 'decimal:2',
        'spot_price_per_g' => 'decimal:2',
        'loss_monetary_value' => 'decimal:2',
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

    /**
     * Compute net loss and monetary value automatically before saving
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($log) {
            $input = (float) $log->input_weight_g;
            $output = (float) $log->output_weight_g;
            $scrap = (float) $log->scrap_recovered_g;
            $dust = (float) $log->dust_recovered_g;

            // Net Loss = Input - (Output + Scrap + Dust)
            $netLoss = max(0, $input - ($output + $scrap + $dust));
            $log->net_loss_g = $netLoss;

            if ($input > 0) {
                $log->loss_percentage = round(($netLoss / $input) * 100, 2);
            } else {
                $log->loss_percentage = 0;
            }

            $spotPrice = (float) $log->spot_price_per_g;
            $log->loss_monetary_value = round($netLoss * $spotPrice, 2);
        });
    }
}
