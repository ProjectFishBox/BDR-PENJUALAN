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
        'last_user'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
}
