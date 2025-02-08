<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravolt\Indonesia\Facade as Indonesia;

use App\Models\Lokasi;
use App\Models\User;

class ProfileControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Profile';

        $lokasi = Lokasi::all();
        $user = Auth::user();
        $kota = Indonesia::allCities();

        return view('pages.users.profile', compact('title', 'lokasi', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|max:25',
            'username' => 'required|max:25',
            'jabatan' => 'required|max:25',
            'id_lokasi' => 'required|integer',
            'id_akses' => 'required|integer',
            'password' => 'nullable|min:3',
            'new_password' => 'nullable|min:3',
            'create_by' => 'required|integer',
            'last_user' => 'required|integer'
        ]);

        $user = User::find(auth()->id());

        $updateData = [
            'nama' => $validatedData['nama'],
            'username' => $validatedData['username'],
            'jabatan' => $validatedData['jabatan'],
            'id_lokasi' => $validatedData['id_lokasi'],
            'id_akses' => $validatedData['id_akses'],
            'create_by' => $validatedData['create_by'],
            'last_user' => $validatedData['last_user']
        ];

        if (!empty($validatedData['password']) || !empty($validatedData['new_password'])) {
            if (empty($validatedData['password']) || !Hash::check($validatedData['password'], $user->password)) {
                return back()->withErrors(['password' => 'Password lama tidak sesuai atau kosong.'])->withInput();
            }

            if (!empty($validatedData['new_password'])) {
                $updateData['password'] = bcrypt($validatedData['new_password']);
            }
        }

        $user->update($updateData);
        toast('Profile berhasil di update','success');
        return redirect('/profile')->with('success', 'Profil berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
