<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class AuthControllers extends Controller
{
    public function index()
    {

        return view('pages.auth.login');
    }

    public function register()
    {

        return view('pages.auth.register');
    }

    public function register_action(Request $request) {


        $validatedData = $request->validate([
            'nama' => 'required|max:25',
            'username' => 'required|max:25',
            'password' => 'required|min:3',
            'jabatan' => 'required',
            'id_lokasi' => 'required|integer',
            'id_akses' => 'required|integer',
            'create_by' => 'required|integer',
            'last_user' => 'required|integer'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);
        // toast('You\'ve Successfully Registered','success');
        return redirect('/');
    }

    public function login_action(Request $request)
    {

        $login = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($login)) {
            $request->session()->regenerate();
            // toast('You\'ve Successfully Login','success');
            return redirect()->intended('/dashboard');
        }

        return back()->with('loginError', 'Login Failed');
    }
}
