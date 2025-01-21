<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use RealRashid\SweetAlert\Facades\Alert;


class PelangganControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = "List Pelanggan";

        $search = $request->get('search');
        $lokasiId = $request->get('lokasi');

        $lokasiList = Lokasi::all();

        $data = Pelanggan::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%$search%");
        })
        ->when($lokasiId, function ($query, $lokasiId) {
            return $query->where('id_lokasi', $lokasiId);
        })
        ->with('lokasi')
        ->get();


        return view('pages.master.pelanggan.pelanggan', compact('title', 'data', 'lokasiList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Pelanggan';

        $lokasi = Lokasi::all();

        return view('pages.master.pelanggan.tambah_pelanggan', compact('title', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kode_pos' => 'required|string|max:20',
            'telepon' => 'required',
            'fax' => 'required',
            'id_kota' => 'required|integer',
            'id_lokasi' => 'required|integer',
        ]);

        $validateData['create_by'] = auth()->id();
        $validateData['last_user'] = auth()->id();

        Pelanggan::create($validateData);

        Alert::success('Berhasil Menambahkan data Pelanggan.');
        return redirect('/pelanggan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Pelanggan';

        $pelanggan = Pelanggan::findOrFail($id);
        $lokasi = Lokasi::all();

        return view('pages.master.pelanggan.edit_pelanggan', compact('title', 'pelanggan', 'lokasi'));
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
        $validateData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kode_pos' => 'required|string|max:20',
            'telepon' => 'required',
            'fax' => 'required',
            'id_kota' => 'required|integer',
            'id_lokasi' => 'required|integer',
        ]);

        $validateData['last_user'] = auth()->id();

        $pelanggan = Pelanggan::findOrFail($id);

        $pelanggan->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'kode_pos' => $request->kode_pos,
            'telepon' => $request->telepon,
            'fax' => $request->fax,
            'id_kota' => $request->id_kota,
            'id_lokasi' => $request->id_lokasi,
            'last_user' => auth()->id(),
        ]);

        Alert::success('Berhasil Merubah data Pelanggan.');

        return redirect('/pelanggan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $pelanggan->delete();

        Alert::success('Data Pelanggan berhasil dihapus.');

        return redirect('/pelanggan');
    }
}
