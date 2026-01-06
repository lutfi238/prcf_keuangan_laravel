<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $primaryKey = 'id_donor';
    
    protected $fillable = [
        'kode_donor',
        'nama_donor',
        'alamat',
        'email',
        'telepon',
        'negara',
    ];

    public function proyeks()
    {
        return $this->hasMany(Proyek::class, 'kode_donor', 'kode_donor');
    }
}
