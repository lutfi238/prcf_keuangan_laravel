<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCodeBudget extends Model
{
    protected $primaryKey = 'id_budget';

    protected $fillable = [
        'kode_proyek',
        'id_village',
        'exp_code',
        'place_code',
        'budget_usd',
        'budget_idr',
        'used_usd',
        'used_idr',
        'exrate',
    ];

    protected function casts(): array
    {
        return [
            'budget_usd' => 'decimal:2',
            'budget_idr' => 'decimal:2',
            'used_usd' => 'decimal:2',
            'used_idr' => 'decimal:2',
            'exrate' => 'decimal:4',
        ];
    }

    /**
     * Project this budget belongs to
     */
    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'kode_proyek', 'kode_proyek');
    }

    /**
     * Village this budget is allocated to
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'id_village', 'id_village');
    }

    /**
     * Get remaining USD budget
     */
    public function getRemainingUsdAttribute(): float
    {
        return $this->budget_usd - $this->used_usd;
    }

    /**
     * Get remaining IDR budget
     */
    public function getRemainingIdrAttribute(): float
    {
        return $this->budget_idr - $this->used_idr;
    }

    /**
     * Check if budget is sufficient for given amount
     */
    public function hasSufficientBudget(float $amountUsd, float $amountIdr): bool
    {
        return $this->remaining_usd >= $amountUsd && $this->remaining_idr >= $amountIdr;
    }

    /**
     * Use budget (increase used amount)
     */
    public function useBudget(float $amountUsd, float $amountIdr): bool
    {
        if (!$this->hasSufficientBudget($amountUsd, $amountIdr)) {
            return false;
        }

        $this->used_usd += $amountUsd;
        $this->used_idr += $amountIdr;
        return $this->save();
    }

    /**
     * Release budget (decrease used amount)
     */
    public function releaseBudget(float $amountUsd, float $amountIdr): bool
    {
        $this->used_usd = max(0, $this->used_usd - $amountUsd);
        $this->used_idr = max(0, $this->used_idr - $amountIdr);
        return $this->save();
    }
}