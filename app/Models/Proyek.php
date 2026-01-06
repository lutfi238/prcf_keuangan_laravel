<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyek extends Model
{
    protected $table = 'proyek';
    protected $primaryKey = 'kode_proyek';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_proyek',
        'nama_proyek',
        'status_proyek',
        'donor',
        'nilai_anggaran',
        'periode_mulai',
        'periode_selesai',
        'rekening_khusus',
    ];

    protected function casts(): array
    {
        return [
            'status_proyek' => ProjectStatus::class,
            'nilai_anggaran' => 'decimal:2',
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
        ];
    }

    /**
     * Budget allocations for this project
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(ProjectCodeBudget::class, 'kode_proyek', 'kode_proyek');
    }

    /**
     * Proposals for this project
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'kode_proyek', 'kode_proyek');
    }

    /**
     * Bank book headers for this project
     */
    public function bankBookHeaders(): HasMany
    {
        return $this->hasMany(BukuBankHeader::class, 'kode_proyek', 'kode_proyek');
    }

    /**
     * Piutang headers for this project
     */
    public function piutangHeaders(): HasMany
    {
        return $this->hasMany(BukuPiutangHeader::class, 'kode_proyek', 'kode_proyek');
    }

    /**
     * Financial reports for this project
     */
    public function financialReports(): HasMany
    {
        return $this->hasMany(LaporanKeuanganHeader::class, 'kode_projek', 'kode_proyek');
    }

    /**
     * Donor reports for this project
     */
    public function donorReports(): HasMany
    {
        return $this->hasMany(LaporanDonor::class, 'kode_proyek', 'kode_proyek');
    }

    /**
     * Check if project is active
     */
    public function isActive(): bool
    {
        return $this->status_proyek->isActive();
    }

    /**
     * Get total budget used (sum of all used budgets)
     */
    public function getTotalUsedBudgetUsdAttribute(): float
    {
        return $this->budgets()->sum('used_usd');
    }

    public function getTotalUsedBudgetIdrAttribute(): float
    {
        return $this->budgets()->sum('used_idr');
    }

    /**
     * Get remaining budget
     */
    public function getTotalRemainingBudgetUsdAttribute(): float
    {
        return $this->budgets()->sum('budget_usd') - $this->budgets()->sum('used_usd');
    }
}