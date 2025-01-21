<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = ['nama', 'deskripsi'];

    public function accessMenus()
    {
        return $this->hasMany(Access_menu::class, 'id_menu');
    }
}
