<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Access_menu;


class CheckAksesMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        $userAccessId = $user->id_akses;
        $currentRoute = $request->path();

        $allowedMenus = Access_menu::where('id_akses', $userAccessId)
            ->join('menu', 'access_menu.id_menu', '=', 'menu.id')
            ->pluck('menu.url')
            ->toArray();

        if (!in_array($currentRoute, $allowedMenus)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
