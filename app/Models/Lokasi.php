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
        'create_by',
        'last_user'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_lokasi');
    }



}
