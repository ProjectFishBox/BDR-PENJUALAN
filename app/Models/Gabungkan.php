<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gabungkan extends Model
{
    use HasFactory;

    protected $table = 'gabungkan';

    protected $fillable = [
        'id_lokasi',
        'total_ball',
        'create_by',
        'last_user',
        'delete'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'create_by');
    }
}
