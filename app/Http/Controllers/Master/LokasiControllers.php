<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lokasi;
use RealRashid\SweetAlert\Facades\Alert;

class LokasiControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Lokasi';

        $search = $request->get('search');

        $data = Lokasi::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%$search%");
        })->get();

        return view('pages.master.lokasi.lokasi', compact('title', 'data'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Lokasi';

        $formRoute = 'store-lokasi';

        return view('pages.master.lokasi.add_lokasi', compact('title', 'formRoute'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $validateData['create_by'] = auth()->id();
        $validateData['last_user'] = auth()->id();

        Lokasi::create($validateData);

        Alert::success('Berhasil Menambahkan data lokasi.');
        return redirect('/lokasi');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $title = 'Edit Lokasi';

        $lokasi = Lokasi::findOrFail($id);

        return view('pages.master.lokasi.edit_lokasi', compact('lokasi', 'title'));
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $lokasi = Lokasi::findOrFail($id);

        $lokasi->update([
            'nama' => $request->nama,
            'last_user' => auth()->id(),
        ]);

        Alert::success('Berhasil Merubah data lokasi.');

        return redirect('/lokasi');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lokasi = Lokasi::findOrFail($id);

        $lokasi->delete();

        Alert::success('Data Lokasi berhasil dihapus.');

        return redirect('/lokasi');

    }
}
