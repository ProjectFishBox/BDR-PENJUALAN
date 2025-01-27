<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
USE Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

use App\Models\Lokasi;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Barang;
use App\Models\Kota;
use App\Models\PenjualanDetail;

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

        $query = Penjualan::with(['pelanggan', 'lokasi']);

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
            $query->where('no_nota', 'like', '%' . $request->search . '%');
        }

        $pengeluaran = $query->paginate(10);

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

        // dd($request->all());

        $validatedData = $request->validate([
            'tanggal' => 'required',
            'no_nota' => 'required',
            'pelanggan' => 'required',
            'diskon_nota' => 'nullable',
            'bayar' => 'nullable',
            'total' => 'nullable',
            'sisa' => 'nullable'
        ]);

        $validatedData['tanggal'] = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');

        $validatedData['id_lokasi'] = auth()->user()->id_lokasi;
        $validatedData['create_by'] = auth()->id();
        $validatedData['last_user'] = auth()->id();

        $diskonnota = preg_replace('/[Rp. ]/', '', $request->diskon_nota);
        $bayar = preg_replace('/[Rp. ]/', '', $request->bayar);
        $total = preg_replace('/[Rp. ]/', '', $request->total);
        $sisa = preg_replace('/[Rp. ]/', '', $request->sisa);

        $penjualan = Penjualan::create([
            'tanggal' =>  $validatedData['tanggal'],
            'no_nota' => $request->no_nota,
            'id_pelanggan' => $request->pelanggan,
            'diskon_nota' => $diskonnota,
            'bayar' => $bayar,
            'total_penjualan' => $total,
            'sisa' =>$sisa,
            'id_lokasi' => auth()->user()->id_lokasi,
            'create_by' => auth()->user()->id,
            'last_user' => auth()->user()->id
        ]);

        $tableData = $request->input('table_data');
        foreach ($tableData as $data) {

            $idBarang = preg_replace('/\D/', '', $data['id_barang']);
            $harga = str_replace('.', '', $data['harga']);

            DB::table('penjualan_detail')->insert([
                'id_penjualan' => $penjualan->id,
                'id_barang' => $idBarang,
                'nama_barang' => $data['nama_barang'],
                'merek' => $data['merek'],
                'harga' => $harga,
                'diskon_barang' => $data['diskon'],
                'jumlah' => $data['jumlah'],
                'create_by' => auth()->user()->id,
                'last_user' => auth()->user()->id
            ]);
        }

        // Cache::forget('pembelian_data');

        Alert::success('Berhasil Menambahkan data Penjualan.');

        return redirect('/penjualan');

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

    public function modalImport(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/dashboard');
        }

        $title = "Import Penjualan Detail";

        $action = "import-detailpenjualan";

        $type = 'detailPenjualan';

        return view('components.modal.modal_import_data', compact('title', 'action', 'type'));
    }


    public function validationDetail(Request $request)
    {

        $items = $request->input('items');

        if (empty($items)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data yang dikirim.'
            ], 400);
        }

        $validatedData = [];
        $missingItems = [];

        foreach ($items as $item) {

            if (empty($item['kode_barang'])) {
                continue;
            }

            $barang = Barang::where('kode_barang', $item['kode_barang'])->where('merek', $item['merek'])->first();

            if ($barang) {
                $diskon = isset($item['diskon']) ? $item['diskon'] : 0;
                $hargaSetelahDiskon = $barang->harga - $diskon;
                $subtotal = $hargaSetelahDiskon * $item['jumlah'];


                $validatedData[] = [
                    'id_barang' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama,
                    'merek' => $barang->merek,
                    'harga' => $barang->harga,
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $subtotal,
                ];
            } else {
                $missingItems[] = $item['kode_barang'];
            }
        }

        if (!empty($missingItems)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data barang berikut tidak ditemukan: ' . implode(', ', $missingItems),
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $validatedData,
        ]);
    }


    public function modalDetail(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $title = "Detail Penjualan";

        $id = $request->input('id');

        $penjualan = Penjualan::findOrFail($id);

        $detailPelanggan = Pelanggan::findOrFail($penjualan->id_pelanggan);

        $kota = Kota::findOrFail($detailPelanggan->id_kota);

        $detailpenjualan = PenjualanDetail::with('barang')->where('id_penjualan', $penjualan->id)->get();

        $totalPenjualan = 0;
        foreach ($detailpenjualan as $detail) {
            $hargaSetelahDiskon = $detail->harga - $detail->diskon_barang;
            $subtotalItem = $hargaSetelahDiskon * $detail->jumlah;
            $totalPenjualan += $subtotalItem;


        }

        $lokasi = Lokasi::findOrFail($penjualan->id_lokasi);


        return view('components.modal.modal_detail_data_penjualan', compact('title', 'penjualan', 'detailpenjualan', 'lokasi', 'detailPelanggan', 'totalPenjualan', 'subtotalItem', 'kota'));
    }

}
