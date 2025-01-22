<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetHarga extends Model
{
    use HasFactory;

    protected $table = 'set_harga';

    protected $fillable = [
        'id_lokasi',
        'id_barang',
        'nama_barang',
        'kode_barang',
        'merek',
        'harga',
        'untung',
        'harga_jual',
        'status',
        'create_by',
        'last_user'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
}
