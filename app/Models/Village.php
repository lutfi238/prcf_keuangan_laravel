<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $primaryKey = 'id_village';

    protected $fillable = [
        'village_code',
        'village_name',
        'village_abbr',
        'description',
    ];

    /**
     * Budget allocations for this village
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(ProjectCodeBudget::class, 'id_village', 'id_village');
    }

    /**
     * Proposal budget details for this village
     */
    public function proposalBudgetDetails(): HasMany
    {
        return $this->hasMany(ProposalBudgetDetail::class, 'id_village', 'id_village');
    }

    /**
     * Generate place code for this village
     */
    public function generatePlaceCode(string $expCode, int $sequence = 1): string
    {
        return sprintf('%s-%s-%02d', $expCode, $this->village_abbr, $sequence);
    }
}