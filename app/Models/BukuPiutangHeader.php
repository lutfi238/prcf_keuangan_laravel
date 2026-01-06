<?php

namespace App\Models;

use App\Enums\BankBookStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BukuPiutangHeader extends Model
{
    protected $table = 'buku_piutang_header';
    protected $primaryKey = 'id_piutang';

    protected $fillable = [
        'kode_proyek',
        'periode_bulan',
        'periode_tahun',
        'exrate',
        'beginning_balance_idr',
        'ending_balance_idr',
        'beginning_balance_usd',
        'ending_balance_usd',
        'created_by',
        'approved_by',
        'catatan_fm',
        'status',
        'tgl_pembuatan',
        'tgl_persetujuan',
    ];

    protected function casts(): array
    {
        return [
            'exrate' => 'decimal:2',
            'beginning_balance_idr' => 'decimal:2',
            'ending_balance_idr' => 'decimal:2',
            'beginning_balance_usd' => 'decimal:2',
            'ending_balance_usd' => 'decimal:2',
            'status' => BankBookStatus::class,
            'tgl_pembuatan' => 'date',
            'tgl_persetujuan' => 'date',
        ];
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'kode_proyek', 'kode_proyek');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BukuPiutangDetail::class, 'id_piutang', 'id_piutang');
    }

    public function unliquidated(): HasMany
    {
        return $this->hasMany(BukuPiutangUnliquidated::class, 'id_piutang', 'id_piutang');
    }

    public static function getOrCreate(string $projectCode, string $date): self
    {
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));

        $header = self::where('kode_proyek', $projectCode)
            ->where('periode_bulan', $month)
            ->where('periode_tahun', $year)
            ->first();

        if ($header) {
            return $header;
        }

        $prevMonth = date('m', strtotime("-1 month", strtotime($date)));
        $prevYear = date('Y', strtotime("-1 month", strtotime($date)));

        $prevHeader = self::where('kode_proyek', $projectCode)
            ->where('periode_bulan', $prevMonth)
            ->where('periode_tahun', $prevYear)
            ->first();

        $saldoAwalIdr = $prevHeader?->ending_balance_idr ?? 0;
        $saldoAwalUsd = $prevHeader?->ending_balance_usd ?? 0;

        return self::create([
            'kode_proyek' => $projectCode,
            'periode_bulan' => $month,
            'periode_tahun' => $year,
            'beginning_balance_idr' => $saldoAwalIdr,
            'beginning_balance_usd' => $saldoAwalUsd,
            'ending_balance_idr' => $saldoAwalIdr,
            'ending_balance_usd' => $saldoAwalUsd,
            'status' => BankBookStatus::Draft,
            'tgl_pembuatan' => now(),
        ]);
    }

    public function updateBalance(float $amountIdr, float $amountUsd, bool $isDebit = true): void
    {
        $factor = $isDebit ? 1 : -1;
        
        $this->ending_balance_idr += $amountIdr * $factor;
        $this->ending_balance_usd += $amountUsd * $factor;
        $this->save();
    }
}