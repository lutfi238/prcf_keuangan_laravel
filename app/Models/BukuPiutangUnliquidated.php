<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BukuPiutangUnliquidated extends Model
{
    protected $table = 'buku_piutang_unliquidated';
    protected $primaryKey = 'id_unliquidate';

    protected $fillable = [
        'id_piutang',
        'tgl',
        'voucher_no',
        'name',
        'description',
        'nilai_idr',
        'nilai_usd',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl' => 'date',
            'nilai_idr' => 'decimal:2',
            'nilai_usd' => 'decimal:2',
        ];
    }

    public function header(): BelongsTo
    {
        return $this->belongsTo(BukuPiutangHeader::class, 'id_piutang', 'id_piutang');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function liquidate(): bool
    {
        $this->status = 'liquidated';
        return $this->save();
    }

    public function cancel(): bool
    {
        $this->status = 'cancelled';
        return $this->save();
    }
}