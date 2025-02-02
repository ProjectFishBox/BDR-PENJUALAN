<?php

namespace App\Helpers;

use App\Models\Access_menu;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;

class AksesMenuHelper
{
    public static function hasAccess($menuUrl)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return Access_menu::where('id_akses', $user->id_akses)
            ->whereHas('menu', function ($query) use ($menuUrl) {
                $query->where('url', $menuUrl);
            })
            ->exists();
    }
}
