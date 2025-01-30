<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GabungkanDetail extends Model
{
    use HasFactory;

    protected $table = 'gabungkan_detail';


    protected $fillable = [
        'id_gabungkan',
        'kode_barang',
        'merek',
        'jumlah',
        'delete',
        'create_by',
        'last_user'
    ];
}
