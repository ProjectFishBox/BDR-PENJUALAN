<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';


    protected $fillable = [
        'nama',
        'alamat',
        'kode_pos',
        'telepon',
        'fax',
        'id_kota',
        'id_lokasi',
        'create_by',
        'last_user',
        'delete'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function kota()
    {
        return $this->belongsTo(Kota::class, 'id_kota', 'id');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_pelanggan', 'id');
    }

}
