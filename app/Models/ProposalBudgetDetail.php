<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalBudgetDetail extends Model
{
    protected $primaryKey = 'id_detail';
    public $timestamps = false;
    
    protected $fillable = [
        'id_proposal',
        'id_village',
        'exp_code',
        'place_code',
        'requested_usd',
        'requested_idr',
        'currency',
        'exrate',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'requested_usd' => 'decimal:2',
            'requested_idr' => 'decimal:2',
            'exrate' => 'decimal:4',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Proposal this detail belongs to
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'id_proposal', 'id_proposal');
    }

    /**
     * Village this budget is for
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'id_village', 'id_village');
    }
}