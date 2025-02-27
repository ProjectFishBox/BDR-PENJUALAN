<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    protected $fillable = [
        'tanggal',
        'no_nota',
        'kontainer',
        'bayar',
        'id_lokasi',
        'create_by',
        'last_user',
        'delete'
    ];


    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function detail()
    {
        return $this->hasMany(PembelianDetail::class, 'id_pembelian');
    }
}
