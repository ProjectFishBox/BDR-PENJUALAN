<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'penjualan_detail';

    protected $fillable = [
        'id_penjualan',
        'id_barang',
        'nama_barang',
        'merek',
        'harga',
        'diskon_barang',
        'jumlah',
        'create_by',
        'last_user',
        'delete'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
