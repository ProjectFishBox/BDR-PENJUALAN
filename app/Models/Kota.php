<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    use HasFactory;

    protected $table = 'kota';

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'id_kota', 'id');
    }
}
