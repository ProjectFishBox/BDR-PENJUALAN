<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access_menu extends Model
{
    use HasFactory;

    protected $table = 'access_menu';

    protected $fillable = [
        'id_akses',
        'id_menu',
        'delete',
        'create_by',
        'last_user'
    ];

    public function akses()
    {
        return $this->belongsTo(Akses::class, 'id_akses');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }


}
