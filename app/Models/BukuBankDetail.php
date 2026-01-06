<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BukuBankDetail extends Model
{
    protected $table = 'buku_bank_detail';
    protected $primaryKey = 'id_detail_bank';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_detail_bank',
        'id_bank_header',
        'tanggal',
        'reff',
        'title_activity',
        'cost_description',
        'recipient',
        'place_code',
        'exp_code',
        'nominal_code',
        'exrate',
        'cost_curr',
        'debit_idr',
        'debit_usd',
        'credit_idr',
        'credit_usd',
        'balance_idr',
        'balance_usd',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'exrate' => 'decimal:2',
            'debit_idr' => 'decimal:2',
            'debit_usd' => 'decimal:2',
            'credit_idr' => 'decimal:2',
            'credit_usd' => 'decimal:2',
            'balance_idr' => 'decimal:2',
            'balance_usd' => 'decimal:2',
        ];
    }

    /**
     * Header this detail belongs to
     */
    public function header(): BelongsTo
    {
        return $this->belongsTo(BukuBankHeader::class, 'id_bank_header', 'id_bank_header');
    }

    /**
     * Generate new ID
     */
    public static function generateId(): string
    {
        return 'BD-' . date('Ymd-His') . '-' . substr(uniqid(), -4);
    }

    /**
     * Check if this is a credit transaction
     */
    public function isCredit(): bool
    {
        return $this->credit_idr > 0 || $this->credit_usd > 0;
    }

    /**
     * Check if this is a debit transaction
     */
    public function isDebit(): bool
    {
        return $this->debit_idr > 0 || $this->debit_usd > 0;
    }
}