<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardControllers extends Controller
{

    public function index(){
        return view('pages.dashboard.index');
    }
}
