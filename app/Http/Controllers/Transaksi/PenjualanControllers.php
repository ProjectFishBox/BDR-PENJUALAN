<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
USE Illuminate\Support\Carbon;

use App\Models\Lokasi;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Barang;

class PenjualanControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Penjualan';

        $lokasi = Lokasi::all();
        $pelanggan = Pelanggan::all();


        $query = Penjualan::query();

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('tanggal', [
                Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                Carbon::createFromFormat('d/m/Y', $request->end)->format('Y-m-d')
            ]);
        }

        if ($request->filled('pelanggan')) {
            $query->where('id_pelanggan', $request->pelanggan);
        }

        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        if ($request->filled('search')) {
            $query->where('uraian', 'like', '%' . $request->search . '%');
        }

        $pengeluaran = $query->paginate(10); //entar malam task

        return view('pages.transaksi.penjualan.penjualan', compact('pengeluaran', 'lokasi', 'title', 'pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Penjualan';

        $pelanggan = Pelanggan::all();

        $barang = Barang::all();

        return view('pages.transaksi.penjualan.tambah_penjualan', compact('title', 'pelanggan', 'barang'));
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function modalTambahPelanggan(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $title = "Tambah Pelanggan";

        $lokasi = Lokasi::all();

        return view('components.modal.modal_tambah_pelanggan', compact('title', 'lokasi'));
    }

    public function tambahPelangganPenjualan(Request $request)
    {
        $validateData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required',
            'id_kota' => 'required|integer',
            'id_lokasi' => 'required|integer',
            'fax' => 'string|max:255',
            'kode_pos' => 'string|max:255'
        ]);

        $validateData['create_by'] = auth()->id();
        $validateData['last_user'] = auth()->id();

        $pelanggan = Pelanggan::create($validateData);

        session()->flash('new_pelanggan', [
            'id' => $pelanggan->id,
            'nama' => $pelanggan->nama,
            'alamat' => $pelanggan->alamat,
            'kota' => $pelanggan->kota->nama ?? '',
            'telepon' => $pelanggan->telepon,
        ]);

        toast('Data Pelanggan berhasil ditambahkan', 'success');

        return redirect()->route('tambah-penjualan');
    }

    public function PelanggalDetail($id)
    {
        $pelanggan = Pelanggan::with('kota')->find($id);

        if ($pelanggan) {
            return response()->json([
                'alamat' => $pelanggan->alamat,
                'kota' => $pelanggan->kota->nama ?? '',
                'telepon' => $pelanggan->telepon,
            ]);
        }

        return response()->json(['message' => 'Data pelanggan tidak ditemukan.'], 404);
    }

}
