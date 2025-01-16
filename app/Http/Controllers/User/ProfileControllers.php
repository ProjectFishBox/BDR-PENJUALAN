<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        return view('pages.users.profile', compact('title', 'lokasi'));
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
        // Validasi data
        $validatedData = $request->validate([
            'nama' => 'required|max:25',
            'username' => 'required|max:25',
            'jabatan' => 'required|integer',
            'id_lokasi' => 'required|integer',
            'id_akses' => 'required|integer',
            'password' => 'required',
            'new_password' => 'nullable|min:8',
            'create_by' => 'required|integer',
        ]);

        dd($validatedData);

        $user = User::find(auth()->id());

        if (!Hash::check($validatedData['password'], $user->password)) {
            return back()->withErrors(['password' => 'Password lama tidak sesuai.'])->withInput();
        }

        $updateData = [
            'nama' => $validatedData['nama'],
            'username' => $validatedData['username'],
            'jabatan' => $validatedData['jabatan'],
            'id_lokasi' => $validatedData['id_lokasi'],
            'id_akses' => $validatedData['id_akses'],
            'create_by' => $validatedData['create_by'],
        ];


        if (!empty($validatedData['new_password'])) {
            $updateData['password'] = bcrypt($validatedData['new_password']);
        }

        $user->update($updateData);

        return redirect('/dashboard')->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
