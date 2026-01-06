<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanKeuanganHeader extends Model
{
    protected $table = 'laporan_keuangan_header';
    protected $primaryKey = 'id_laporan_keu';

    protected $fillable = [
        'kode_projek',
        'nama_projek',
        'nama_kegiatan',
        'pelaksana',
        'tanggal_pelaksanaan',
        'tanggal_laporan',
        'mata_uang',
        'exrate',
        'created_by',
        'verified_by',
        'approved_by',
        'status_lap',
        'catatan_finance',
        'notes_sa',
        'notes_fm',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pelaksanaan' => 'date',
            'tanggal_laporan' => 'date',
            'exrate' => 'decimal:4',
            'status_lap' => ReportStatus::class,
        ];
    }

    /**
     * Project this report belongs to
     */
    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'kode_projek', 'kode_proyek');
    }

    /**
     * User who created this report (PM)
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * User who verified this report (SA)
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'id_user');
    }

    /**
     * User who approved this report (FM)
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }

    /**
     * Detail items for this report
     */
    public function details(): HasMany
    {
        return $this->hasMany(LaporanKeuanganDetail::class, 'id_laporan_keu', 'id_laporan_keu');
    }

    /**
     * Check if report is editable
     */
    public function isEditable(): bool
    {
        return $this->status_lap->isEditable();
    }

    /**
     * Submit report for SA verification
     */
    public function submit(): bool
    {
        if ($this->status_lap !== ReportStatus::Draft && $this->status_lap !== ReportStatus::RevisionRequested) {
            return false;
        }

        $this->status_lap = ReportStatus::Submitted;
        return $this->save();
    }

    /**
     * Verify report by SA
     */
    public function verify(User $sa): bool
    {
        if ($this->status_lap !== ReportStatus::Submitted) {
            return false;
        }

        $this->status_lap = ReportStatus::Verified;
        $this->verified_by = $sa->id_user;
        return $this->save();
    }

    /**
     * Approve report by FM
     */
    public function approve(User $fm): bool
    {
        if ($this->status_lap !== ReportStatus::Verified) {
            return false;
        }

        $this->status_lap = ReportStatus::Approved;
        $this->approved_by = $fm->id_user;
        return $this->save();
    }

    /**
     * Reject report
     */
    public function reject(string $reason = null): bool
    {
        $this->status_lap = ReportStatus::Rejected;
        if ($reason) {
            $this->catatan_finance = $reason;
        }
        return $this->save();
    }

    /**
     * Request revision
     */
    public function requestRevision(string $reason): bool
    {
        $this->status_lap = ReportStatus::RevisionRequested;
        $this->catatan_finance = $reason;
        return $this->save();
    }

    /**
     * Get total actual amount
     */
    public function getTotalActualAttribute(): float
    {
        return $this->details()->sum('actual') ?? 0;
    }

    /**
     * Get total requested amount
     */
    public function getTotalRequestedAttribute(): float
    {
        return $this->details()->sum('requested') ?? 0;
    }
}