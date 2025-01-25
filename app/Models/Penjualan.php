<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'id_lokasi',
        'tanggal',
        'no_nota',
        'id_pelanggan',
        'total_penjualan',
        'diskon_nota',
        'bayar',
        'delete',
        'create_by',
        'last_user'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }


}
