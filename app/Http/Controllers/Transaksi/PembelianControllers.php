<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;

use App\Models\Pembelian;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\PembelianDetail;


class PembelianControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Pembelian';

        $cacheKey = 'pembelian_data';
        $cacheDuration = now()->addMinutes(3);

        if ($request->ajax()) {

            $data = Cache::remember($cacheKey, $cacheDuration, function () {
                return Pembelian::with('lokasi')->where('delete', 0)->get();
            });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =
                        '
                            <button class="btn btn-icon btn-primary btn-pembelian-edit" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-edit"></i>
                            </button>

                            <button class="btn btn-icon btn-danger btn-pembelian-delete" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-delete"></i>
                            </button>
                            ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.transaksi.pembelian.pembelian', compact('title'));
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

        // dd($request->all());

        $validatedData = $request->validate([
            'tanggal' => 'required',
            'no_nota' => 'required',
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
                'last_user' => auth()->id()
            ]);
        }

        Cache::forget('pembelian_data');

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

        $detailPembelian = PembelianDetail::with('barang')->where('id_pembelian', $pembelian->id)->get();

        $bayar = $pembelian->bayar;


        return view('pages.transaksi.pembelian.edit_pembelian', compact('title', 'barang', 'pembelian', 'detailPembelian', 'bayar'));
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


        DB::beginTransaction();

        try {

            $tanggal = $request->input('tanggal');
            $noNota     = $request->input('no_nota');
            $kontainer  = $request->input('kontainer');
            $bayar      = $request->input('bayar');
            $tableData  = $request->input('table_data');

            if (empty($tanggal) || empty($noNota)) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap'], 400);
            }

            $pembelian = Pembelian::findOrFail($id);

            $pembelian->update([
                'tanggal'    => $tanggal,
                'no_nota'    => $noNota,
                'kontainer'  => $kontainer,
                'bayar'      => $bayar,
                'last_user'  => auth()->user()->id
            ]);

            PembelianDetail::where('id_pembelian', $id)->delete();

            foreach ($tableData as $key => $item) {

                if (!isset($item['id_barang'], $item['nama_barang'], $item['harga'], $item['jumlah'], $item['subtotal'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data item tidak lengkap',
                        'item' => $item
                    ], 400);
                }

                PembelianDetail::create([
                    'id_pembelian' => $id,
                    'id_barang'    => $item['id_barang'],
                    'nama_barang'  => $item['nama_barang'],
                    'merek'        => $item['merek'] ?? null,
                    'harga'        => $item['harga'],
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $item['subtotal'],
                    'create_by'    => auth()->user()->id,
                    'last_user'    => auth()->user()->id
                ]);
            }

            DB::commit();

            Cache::forget('pembelian_data');

            Alert::success('Berhasil Merubah data SET Harga.');

            return redirect('/pembelian');


        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate data',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */


    public function modalDetail(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $title = "Detail Pembelian";

        $id = $request->input('id');


        $pembelian = Pembelian::findOrFail($id);

        // $detailPembelian = PembelianDetail::where('id_pembelian', $id)->get();

        $detailPembelian = PembelianDetail::with('barang')->where('id_pembelian', $pembelian->id)->get();

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

    public function destroy(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $pembelian->update([
            'delete' => 1,
            'last_user' => auth()->id()
        ]);

        Cache::forget('pembelian_data');

        Alert::success('Data Pembelian berhasil dihapus.');

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }




}
