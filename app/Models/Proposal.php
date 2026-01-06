<?php

namespace App\Models;

use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    protected $primaryKey = 'id_proposal';

    protected $fillable = [
        'judul_proposal',
        'pj',
        'date',
        'pemohon',
        'status',
        'approved_by_fm',
        'fm_approval_date',
        'kode_proyek',
        'tor',
        'file_budget',
        'total_budget_usd',
        'total_budget_idr',
        'currency',
        'exrate_at_submission',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'fm_approval_date' => 'datetime',
            'status' => ProposalStatus::class,
            'total_budget_usd' => 'decimal:2',
            'total_budget_idr' => 'decimal:2',
            'exrate_at_submission' => 'decimal:4',
        ];
    }

    /**
     * Project this proposal belongs to
     */
    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'kode_proyek', 'kode_proyek');
    }

    /**
     * FM who approved this proposal
     */
    public function approvedByFm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_fm', 'id_user');
    }

    /**
     * Budget details for this proposal
     */
    public function budgetDetails(): HasMany
    {
        return $this->hasMany(ProposalBudgetDetail::class, 'id_proposal', 'id_proposal');
    }

    /**
     * Check if proposal is editable
     */
    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }

    /**
     * Check if proposal is final (approved or rejected)
     */
    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    /**
     * Submit proposal for approval
     */
    public function submit(): bool
    {
        if ($this->status !== ProposalStatus::Draft) {
            return false;
        }

        $this->status = ProposalStatus::Submitted;
        return $this->save();
    }

    /**
     * Approve proposal by FM
     */
    public function approveFm(User $fm): bool
    {
        if ($this->status !== ProposalStatus::Submitted) {
            return false;
        }

        $this->status = ProposalStatus::ApprovedFM;
        $this->approved_by_fm = $fm->id_user;
        $this->fm_approval_date = now();
        return $this->save();
    }

    /**
     * Reject proposal
     */
    public function reject(): bool
    {
        if ($this->status !== ProposalStatus::Submitted) {
            return false;
        }

        $this->status = ProposalStatus::Rejected;
        return $this->save();
    }

    /**
     * Get TOR file URL
     */
    public function getTorUrlAttribute(): ?string
    {
        return $this->tor ? asset('storage/' . $this->tor) : null;
    }

    /**
     * Get Budget file URL
     */
    public function getBudgetFileUrlAttribute(): ?string
    {
        return $this->file_budget ? asset('storage/' . $this->file_budget) : null;
    }
}