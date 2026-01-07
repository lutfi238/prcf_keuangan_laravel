<?php

namespace App\Services;

use App\Models\BukuPiutangUnliquidated;
use App\Models\BukuBankDetail;
use Illuminate\Support\Facades\DB;

/**
 * VoucherService - Single Source of Truth untuk Voucher Number Generation
 * 
 * Format standar: YYYY/MM/PROJECT_CODE/SEQUENCE
 * Contoh: 2026/01/RC01/001
 * 
 * Ini menggantikan dua format inkonsisten di PHP native:
 * - review_proposal_fm.php: "BB-BD-xxx-PROPxxx-xx" (non-standard)
 * - finance_functions.php: "YYYY/MM/PROJ/001" (standard - yang kita ikuti)
 * 
 * @see includes/finance_functions.php:8-33 di PHP native
 */
class VoucherService
{
    /**
     * Generate voucher number untuk piutang/receivable
     * 
     * @param string $projectCode Kode proyek (e.g., 'RC01', 'TEST-001')
     * @param string|null $date Date untuk bulan/tahun (default: now)
     * @return string Voucher number dalam format YYYY/MM/PROJ/001
     */
    public function generatePiutangVoucher(string $projectCode, ?string $date = null): string
    {
        return $this->generate($projectCode, $date, 'piutang');
    }

    /**
     * Generate voucher number untuk bank book
     * 
     * @param string $projectCode Kode proyek
     * @param string|null $date Date untuk bulan/tahun
     * @return string Voucher number dalam format YYYY/MM/PROJ/001
     */
    public function generateBankVoucher(string $projectCode, ?string $date = null): string
    {
        return $this->generate($projectCode, $date, 'bank');
    }

    /**
     * Generate voucher number dengan type tertentu
     * 
     * @param string $projectCode Kode proyek
     * @param string|null $date Date untuk bulan/tahun
     * @param string $type Type: 'piutang' atau 'bank'
     * @return string Voucher number
     */
    public function generate(string $projectCode, ?string $date = null, string $type = 'piutang'): string
    {
        $date = $date ?? now()->format('Y-m-d');
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        
        $prefix = "{$year}/{$month}/{$projectCode}/";
        
        // Get next sequence number based on type
        $nextNum = $this->getNextSequence($prefix, $type);
        
        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get next sequence number untuk prefix tertentu
     * 
     * @param string $prefix Prefix voucher (e.g., "2026/01/RC01/")
     * @param string $type Type: 'piutang' atau 'bank'
     * @return int Next sequence number
     */
    protected function getNextSequence(string $prefix, string $type): int
    {
        // Query based on type
        if ($type === 'bank') {
            $lastVoucher = BukuBankDetail::where('reff', 'like', $prefix . '%')
                ->orderBy('reff', 'desc')
                ->first();
            $column = 'reff';
        } else {
            // Default: piutang unliquidated (sesuai PHP native)
            $lastVoucher = BukuPiutangUnliquidated::where('voucher_no', 'like', $prefix . '%')
                ->orderBy('voucher_no', 'desc')
                ->first();
            $column = 'voucher_no';
        }

        if (!$lastVoucher) {
            return 1;
        }

        $lastNo = $lastVoucher->$column;
        $numPart = substr($lastNo, strlen($prefix));
        
        if (is_numeric($numPart)) {
            return intval($numPart) + 1;
        }

        return 1;
    }

    /**
     * Validate voucher format
     * 
     * @param string $voucherNo Voucher number to validate
     * @return bool True jika format valid
     */
    public function isValidFormat(string $voucherNo): bool
    {
        // Format: YYYY/MM/PROJ/NNN
        return (bool) preg_match('/^\d{4}\/\d{2}\/[A-Z0-9-]+\/\d{3}$/', $voucherNo);
    }

    /**
     * Parse voucher number ke komponen
     * 
     * @param string $voucherNo Voucher number
     * @return array|null Array dengan keys: year, month, project_code, sequence
     */
    public function parse(string $voucherNo): ?array
    {
        if (!$this->isValidFormat($voucherNo)) {
            return null;
        }

        $parts = explode('/', $voucherNo);
        
        return [
            'year' => $parts[0],
            'month' => $parts[1],
            'project_code' => $parts[2],
            'sequence' => (int) $parts[3],
        ];
    }

    /**
     * Get all vouchers untuk project dan periode tertentu
     * 
     * @param string $projectCode Kode proyek
     * @param int $month Bulan (1-12)
     * @param int $year Tahun
     * @return array Daftar voucher numbers
     */
    public function getVouchersForPeriod(string $projectCode, int $month, int $year): array
    {
        $prefix = sprintf('%04d/%02d/%s/', $year, $month, $projectCode);
        
        return BukuPiutangUnliquidated::where('voucher_no', 'like', $prefix . '%')
            ->orderBy('voucher_no')
            ->pluck('voucher_no')
            ->toArray();
    }
}