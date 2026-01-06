<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKeuanganDetail extends Model
{
    protected $table = 'laporan_keuangan_detail';
    protected $primaryKey = 'id_detail_keu';

    protected $fillable = [
        'id_laporan_keu',
        'invoice_no',
        'invoice_date',
        'item_desc',
        'recipient',
        'place_code',
        'exp_code',
        'unit_total',
        'unit_cost',
        'requested',
        'actual',
        'balance',
        'explanation',
        'file_nota',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'unit_cost' => 'decimal:2',
            'requested' => 'decimal:2',
            'actual' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Header this detail belongs to
     */
    public function header(): BelongsTo
    {
        return $this->belongsTo(LaporanKeuanganHeader::class, 'id_laporan_keu', 'id_laporan_keu');
    }

    /**
     * Get file nota URL
     */
    public function getFileNotaUrlAttribute(): ?string
    {
        return $this->file_nota ? asset('storage/' . $this->file_nota) : null;
    }

    /**
     * Calculate total cost
     */
    public function getTotalCostAttribute(): float
    {
        return ($this->unit_total ?? 0) * ($this->unit_cost ?? 0);
    }
}