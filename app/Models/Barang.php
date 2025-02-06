<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama',
        'merek',
        'harga',
        'create_by',
        'last_user',
        'delete'
    ];

    public function setharga()
    {
        return $this->hasMany(SetHarga::class, 'id_barang');
    }

}
