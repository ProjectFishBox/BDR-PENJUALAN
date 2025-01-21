<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class PenggunaControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Pengguna';

        $search = $request->get('search');

        $data = User::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%$search%");
        })
        ->with('lokasi')
        ->get();

        return view('pages.master.pengguna.pengguna', compact('title', 'data'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Pengguna';

        $lokasi = Lokasi::all();

        return view('pages.master.pengguna.tambah_pengguna', compact('title', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|max:25',
            'username' => 'required|max:25',
            'password' => 'required|min:3',
            'id_lokasi' => 'required|integer',
            'id_akses' => 'required|integer'
        ]);


        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['create_by'] = auth()->id();
        $validatedData['last_user'] = auth()->id();

        User::create($validatedData);

        Alert::success('Berhasil Menambahkan data Pengguna.');

        return redirect('/pengguna');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Pengguna';

        $pengguna = User::findOrFail($id);
        $lokasi = Lokasi::all();


        return view('pages.master.pengguna.edit_pengguna', compact('pengguna', 'title', 'lokasi'));
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
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'nama' => 'required|max:25',
            'username' => 'required|max:25',
            'id_lokasi' => 'required|integer',
            'id_akses' => 'required|integer',
            'password' => 'nullable|min:3'
        ]);

        $validatedData['last_user'] = auth()->id();

        $user = User::findOrFail($id);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            // Jika password kosong, kita tidak perlu update password
            unset($validatedData['password']);
        }

        $user->update([
            'nama' => $request->nama,
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'id_lokasi' => $request->id_lokasi,
            'id_akses' => $request->id_akses,
            'password' => isset($validatedData['password']) ? $validatedData['password'] : $user->password,
            'last_user' => auth()->id(),
        ]);

        Alert::success('Berhasil Merubah data Pengguna.');

        return redirect('/pengguna');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengguna = User::findOrFail($id);

        $pengguna->delete();

        Alert::success('Data Pengguna berhasil dihapus.');

        return redirect('/pengguna');
    }
}
