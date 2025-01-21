<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akses extends Model
{
    use HasFactory;

    protected $table = 'akses';

    protected $fillable = [
        'nama',
        'create_by',
        'last_user'
    ];


    public function accessMenus()
    {
        return $this->hasMany(Access_menu::class, 'id_akses');
    }

    public function menus()
{
    return $this->belongsToMany(Menu::class, 'access_menu', 'id_akses', 'id_menu');
}
}
