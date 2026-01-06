<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ReportStatus;

class LaporanDonor extends Model
{
    protected $table = 'laporan_donor';
    protected $primaryKey = 'id_laporan_donor';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_laporan_donor',
        'kode_proyek',
        'periode_bulan',
        'periode_tahun',
        'judul_laporan',
        'file_path',
        'catatan',
        'status',
        'dibuat_oleh',
        'tanggal_submit',
        'direview_oleh',
        'tanggal_review',
        'catatan_review',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'tanggal_submit' => 'datetime',
            'tanggal_review' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (LaporanDonor $model) {
            if (empty($model->id_laporan_donor)) {
                $model->id_laporan_donor = self::generateId();
            }
        });
    }

    public static function generateId(): string
    {
        $prefix = 'LD';
        $date = date('Ymd');
        $time = date('His');
        $unique = substr(uniqid(), -4);
        
        return "{$prefix}-{$date}-{$time}-{$unique}";
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'kode_proyek', 'kode_proyek');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'direview_oleh');
    }

    public function submit(): bool
    {
        $this->status = ReportStatus::Submitted;
        $this->tanggal_submit = now();
        return $this->save();
    }

    public function approve(int $reviewerId, ?string $notes = null): bool
    {
        $this->status = ReportStatus::Approved;
        $this->direview_oleh = $reviewerId;
        $this->tanggal_review = now();
        $this->catatan_review = $notes;
        return $this->save();
    }

    public function reject(int $reviewerId, string $reason): bool
    {
        $this->status = ReportStatus::Rejected;
        $this->direview_oleh = $reviewerId;
        $this->tanggal_review = now();
        $this->catatan_review = $reason;
        return $this->save();
    }

    public function getPeriodeAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $monthName = $months[$this->periode_bulan] ?? '';
        return "{$monthName} {$this->periode_tahun}";
    }
}