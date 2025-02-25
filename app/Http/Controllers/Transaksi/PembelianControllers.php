<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

use App\Models\Pembelian;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\PembelianDetail;
use App\Models\SetHarga;


class PembelianControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Pembelian';
        $lokasi = Lokasi::where('delete', 0)->get();


        if ($request->ajax()) {

            $lokasiId = $request->query('lokasi');
            $daterange = $request->query('daterange');

            $query = Pembelian::with('lokasi')->where('delete', 0)->orderBy('id', 'desc');

            if ($lokasiId && $lokasiId !== 'all') {
                $query->where('id_lokasi', $lokasiId);
            }

            if ($daterange) {
                $dates = explode(' - ', $daterange);

                if (count($dates) === 2) {
                    $startDate = date('Y-m-d', strtotime($dates[0]));
                    $endDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
            }


            $data = $query->get();

            $data->transform(function ($item) {
                $item->total = PembelianDetail::where('id_pembelian', $item->id)->sum('subtotal');
                return $item;
            });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return date('d-m-Y', strtotime($row->tanggal));
                })
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

        return view('pages.transaksi.pembelian.pembelian', compact('title', 'lokasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Pembelian';

        $barang = Barang::select(
            'barang.id',
            'barang.kode_barang',
            'barang.nama',
            'barang.merek',
            'barang.harga',
            'set_harga.harga_jual'
        )
        ->join('set_harga', function ($join) {
            $join->on('barang.id', '=', 'set_harga.id_barang')
                 ->on('barang.merek', '=', 'set_harga.merek')
                 ->on('barang.kode_barang', '=', 'set_harga.kode_barang');
        })
        ->where('barang.delete', 0)
        ->where('set_harga.delete', 0)
        ->where('set_harga.status', 'Aktif')
        ->get();

        return view('pages.transaksi.pembelian.tambah_pembelian', compact('title', 'barang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required',
            'no_nota' => 'required',
            'kontainer' => 'required',
            'bayar' => 'nullable',
        ]);

        $validatedData['bayar'] = preg_replace('/[^\d]/', '', $request->bayar);
        $validatedData['tanggal'] = date('Y-m-d', strtotime($request->tanggal));
        $validatedData['id_lokasi'] = auth()->user()->id_lokasi;
        $validatedData['create_by'] = auth()->id();
        $validatedData['last_user'] = auth()->id();

        $pembelian = Pembelian::create($validatedData);

        $tableData = $request->input('table_data');
        foreach ($tableData as $data) {
            $harga = preg_replace('/[^\d]/', '', $data['harga']);

            DB::table('pembelian_detail')->insert([
                'id_pembelian' => $pembelian->id,
                'id_barang' => $data['id_barang'],
                'nama_barang' => $data['nama_barang'],
                'merek' => $data['merek'],
                'harga' => $harga,
                'jumlah' => $data['jumlah'],
                'subtotal' => $data['subtotal'],
                'create_by' => auth()->id(),
                'last_user' => auth()->id()
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

        $barang = SetHarga::select('barang.id', 'barang.nama', 'barang.kode_barang', 'barang.harga', 'set_harga.merek')
            ->join('barang', 'barang.id', '=', 'set_harga.id_barang')
            ->where('set_harga.status', 'Aktif')
            ->where('set_harga.delete', 0)
            ->where('set_harga.merek', 0)
            ->whereNotNull('set_harga.merek')
            ->get();


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
            $tanggal = date('Y-m-d', strtotime($request->input('tanggal')));
            $noNota = $request->input('no_nota');
            $kontainer = $request->input('kontainer');
            $bayar = preg_replace('/[^\d]/', '', $request->input('bayar'));

            $tableData = $request->input('table_data');

            if (empty($tanggal) || empty($noNota)) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap'], 400);
            }

            $pembelian = Pembelian::findOrFail($id);

            $pembelian->update([
                'tanggal' => $tanggal,
                'no_nota' => $noNota,
                'kontainer' => $kontainer,
                'bayar' => $bayar,
                'last_user' => auth()->user()->id
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
                $harga = preg_replace('/[^\d]/', '', $item['harga']);

                PembelianDetail::create([
                    'id_pembelian' => $id,
                    'id_barang' => $item['id_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'merek' => $item['merek'] ?? null,
                    'harga' => $harga,
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal'],
                    'create_by' => auth()->user()->id,
                    'last_user' => auth()->user()->id
                ]);
            }

            DB::commit();

            Alert::success('Berhasil Merubah data Pembelian.');

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
                    'harga' => $item['harga'],
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

        PembelianDetail::where('id_pembelian', $id)->update([
            'delete' => 1,
            'last_user' => auth()->id()
        ]);

        Alert::success('Data Pembelian berhasil dihapus.');

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function downloadTamplate()
    {
        $filePath = public_path('import_tamplate/tamplate_pembelian_detail.csv');
        $fileName = 'tamplate_pembelian_detail_.csv';

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $fileName);
    }

    public function searchBarang($kode)
    {


        $barang = Barang::select(
            'barang.id',
            'barang.kode_barang',
            'barang.nama',
            'barang.merek',
            'barang.harga',
            'set_harga.harga_jual'
        )
        ->join('set_harga', function ($join) {
            $join->on('barang.id', '=', 'set_harga.id_barang')
                 ->on('barang.merek', '=', 'set_harga.merek')
                 ->on('barang.kode_barang', '=', 'set_harga.kode_barang');
        })
        ->where('barang.kode_barang', $kode)
        ->where('barang.delete', 0)
        ->where('set_harga.kode_barang', $kode)
        ->where('set_harga.delete', 0)
        ->where('set_harga.status', 'Aktif')
        ->get();

        if ($barang) {
            return response()->json(['success' => true, 'barang' => $barang]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
