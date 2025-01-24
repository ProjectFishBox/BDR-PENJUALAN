<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\Barang;
use App\Models\Lokasi;
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

        $title = "Detail Pembelian";

        $id = $request->input('id');


        $pembelian = Pembelian::findOrFail($id);
        $detailPembelian = PembelianDetail::where('id_pembelian', $id)->get();

        $totalPembelian = $detailPembelian->sum('subtotal');

        $lokasi = Lokasi::findOrFail($pembelian->id_lokasi);


        return view('components.modal.modal_detail_data_pembelian', compact('title', 'pembelian', 'detailPembelian', 'totalPembelian', 'lokasi'));
    }

    public function modalImport(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/dashboard');
        }

        $title = "Import Pembelian Detail";

        $action = "import-detailpembelian";

        $type = 'detailPembelian';

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

                $validatedData[] = [
                    'id_barang' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama,
                    'merek' => $barang->merek,
                    'harga' => $barang->harga,
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $barang->harga * $item['jumlah'],
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




}
