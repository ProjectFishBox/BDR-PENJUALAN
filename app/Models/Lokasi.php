<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'nama',
        'delete',
        'create_by',
        'last_user'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_lokasi');
    }

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'id_lokasi');
    }

    public function setharga()
    {
        return $this->hasMany(SetHarga::class, 'id_lokasi');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'id_lokasi');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_lokasi');
    }

    public function gabungkan()
    {
        return $this->hasMany(Gabungkan::class, 'id_lokasi');
    }

}
