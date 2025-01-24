<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\Barang;
use App\Models\PembelianDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class PembelianControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Pembelian';

        $pembelian = Pembelian::all();
        $search = trim($request->get('search'));


        $data = Pembelian::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('no_nota', 'like', "%$search%")
                ->orWhere('kontainer', 'like', "%$search%");
            });
        })
        ->with('lokasi')
        ->get();

        return view('pages.transaksi.pembelian.pembelian', compact('title','pembelian', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Pembelian';

        $barang = Barang::all();

        return view('pages.transaksi.pembelian.tambah_pembelian', compact('title', 'barang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'tanggal' => 'required|max:25',
            'no_nota' => 'required|max:25',
            'kontainer' => 'required',
            'bayar' => 'nullable',
        ]);

        $validatedData['tanggal'] = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');

        $validatedData['id_lokasi'] = auth()->user()->id_lokasi;
        $validatedData['create_by'] = auth()->id();
        $validatedData['last_user'] = auth()->id();

        $pembelian = Pembelian::create($validatedData);

        $tableData = $request->input('table_data');



        foreach ($tableData as $data) {
            DB::table('pembelian_detail')->insert([
                'id_pembelian' => $pembelian->id,
                'id_barang' => $data['id_barang'],
                'nama_barang' => $data['nama_barang'],
                'merek' => $data['merek'],
                'harga' => $data['harga'],
                'jumlah' => $data['jumlah'],
                'subtotal' => $data['subtotal'],
                'create_by' => auth()->id(),
                'last_user' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        Alert::success('Berhasil Menambahkan data Pembelian.');

        return redirect('/pembelian');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Pembelian';

        $barang = Barang::all();

        $pembelian = Pembelian::findOrFail($id);

        $detailPembelian = PembelianDetail::where('id_pembelian', $pembelian->id)->get();

        return view('pages.transaksi.pembelian.edit_pembelian', compact('title', 'barang', 'pembelian', 'detailPembelian'));
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

    public function modalDetail(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $id = $request->input('id');

        $pembelian = Pembelian::findOrFail($id);
        $detailPembelian = PembelianDetail::where('id_pembelian', $id)->first()->get();

        $totalPembelian = $detailPembelian->sum('subtotal');

        $title = "Detail Pembelian";

        return view('components.modal.modal_detail_data_pembelian', compact('title', 'pembelian', 'detailPembelian', 'totalPembelian'));
    }
}
