<?php

namespace App\Models;

use App\Enums\BankBookStatus;
use App\Services\FinanceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BukuBankHeader extends Model
{
    protected $table = 'buku_bank_header';
    protected $primaryKey = 'id_bank_header';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_bank_header',
        'kode_proyek',
        'account_name',
        'bank_name',
        'account_number',
        'exrate',
        'currency',
        'periode_bulan',
        'periode_tahun',
        'saldo_awal_idr',
        'saldo_awal_usd',
        'current_period_change_idr',
        'current_period_change_usd',
        'saldo_akhir_idr',
        'saldo_akhir_usd',
        'prepared_by',
        'approved_by',
        'status_laporan',
        'tanggal_pembuatan',
        'tanggal_persetujuan',
    ];

    protected function casts(): array
    {
        return [
            'exrate' => 'decimal:2',
            'saldo_awal_idr' => 'decimal:2',
            'saldo_awal_usd' => 'decimal:2',
            'current_period_change_idr' => 'decimal:2',
            'current_period_change_usd' => 'decimal:2',
            'saldo_akhir_idr' => 'decimal:2',
            'saldo_akhir_usd' => 'decimal:2',
            'status_laporan' => BankBookStatus::class,
            'tanggal_pembuatan' => 'date',
            'tanggal_persetujuan' => 'date',
        ];
    }

    /**
     * Project this bank book belongs to
     */
    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'kode_proyek', 'kode_proyek');
    }

    /**
     * Detail transactions in this bank book
     */
    public function details(): HasMany
    {
        return $this->hasMany(BukuBankDetail::class, 'id_bank_header', 'id_bank_header');
    }

    /**
     * Generate new ID
     */
    public static function generateId(): string
    {
        return 'BH-' . date('Ymd-His') . '-' . substr(uniqid(), -4);
    }

    /**
     * Get or create header for project and period
     */
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

        // Get previous period balance
        $prevMonth = date('m', strtotime("-1 month", strtotime($date)));
        $prevYear = date('Y', strtotime("-1 month", strtotime($date)));

        $prevHeader = self::where('kode_proyek', $projectCode)
            ->where('periode_bulan', $prevMonth)
            ->where('periode_tahun', $prevYear)
            ->first();

        $saldoAwalIdr = $prevHeader?->saldo_akhir_idr ?? 0;
        $saldoAwalUsd = $prevHeader?->saldo_akhir_usd ?? 0;

        return self::create([
            'id_bank_header' => self::generateId(),
            'kode_proyek' => $projectCode,
            'periode_bulan' => $month,
            'periode_tahun' => $year,
            'saldo_awal_idr' => $saldoAwalIdr,
            'saldo_awal_usd' => $saldoAwalUsd,
            'saldo_akhir_idr' => $saldoAwalIdr,
            'saldo_akhir_usd' => $saldoAwalUsd,
            'status_laporan' => BankBookStatus::Draft,
            'tanggal_pembuatan' => now(),
        ]);
    }

    /**
     * Update balance after transaction
     */
    public function updateBalance(float $amountIdr, float $amountUsd, bool $isCredit = true): void
    {
        $factor = $isCredit ? -1 : 1;
        
        $this->current_period_change_idr += $amountIdr * $factor;
        $this->current_period_change_usd += $amountUsd * $factor;
        $this->saldo_akhir_idr += $amountIdr * $factor;
        $this->saldo_akhir_usd += $amountUsd * $factor;
        $this->save();
    }
}