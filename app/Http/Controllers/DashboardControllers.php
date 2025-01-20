<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardControllers extends Controller
{

    public function index(){

        $title = "Dashboard";

        return view('pages.dashboard.index', compact('title'));
    }
}
