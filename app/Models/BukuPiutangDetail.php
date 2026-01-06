<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BukuPiutangDetail extends Model
{
    protected $table = 'buku_piutang_detail';
    protected $primaryKey = 'id_detail_piutang';

    protected $fillable = [
        'id_piutang',
        'tgl_trx',
        'reff',
        'description',
        'recipient',
        'p_code',
        'exp_code',
        'nominal_code',
        'exrate',
        'debit_idr',
        'debit_usd',
        'credit_idr',
        'credit_usd',
        'balance_idr',
        'balance_usd',
    ];

    protected function casts(): array
    {
        return [
            'tgl_trx' => 'date',
            'exrate' => 'decimal:4',
            'debit_idr' => 'decimal:2',
            'debit_usd' => 'decimal:2',
            'credit_idr' => 'decimal:2',
            'credit_usd' => 'decimal:2',
            'balance_idr' => 'decimal:2',
            'balance_usd' => 'decimal:2',
        ];
    }

    public function header(): BelongsTo
    {
        return $this->belongsTo(BukuPiutangHeader::class, 'id_piutang', 'id_piutang');
    }
}