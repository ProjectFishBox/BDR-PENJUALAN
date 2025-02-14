<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
USE Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use Laravolt\Indonesia\Facade as Indonesia;


use App\Models\Lokasi;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Barang;
use App\Models\Kota;
use App\Models\PenjualanDetail;
use App\Models\SetHarga;


class PenjualanControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $title = 'List Penjualan';

        $lokasiList = Lokasi::all();

        $pelangganList = Pelanggan::all();

        if ($request->ajax()) {
            $lokasiId = $request->query('lokasi');
            $pelangganId = $request->query('pelanggan');
            $daterange = $request->query('daterange');

            $query = Penjualan::with('pelanggan', 'lokasi')->where('delete', 0)->orderBy('created_at', 'desc');

            if ($lokasiId && $lokasiId !== 'all') {
                $query->where('id_lokasi', $lokasiId);
            }


            if ($pelangganId && $lokasiId !== 'all') {
                $query->where('id_pelanggan', $pelangganId);

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


            if ($data->isEmpty()) {
                return response()->json(['data' => []]);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button class="btn btn-icon btn-primary btn-penjualan-edit" data-id="' . $row->id . '" type="button">
                            <i class="anticon anticon-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-danger btn-penjualan-delete" data-id="' . $row->id . '" type="button">
                            <i class="anticon anticon-delete"></i>
                        </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.transaksi.penjualan.penjualan', compact('title', 'lokasiList', 'pelangganList'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Penjualan';

        $pelanggan = Pelanggan::all();

        $barang = SetHarga::select('barang.id', 'barang.nama', 'barang.kode_barang', 'barang.harga', 'set_harga.merek', 'set_harga.harga_jual')
            ->join('barang', 'barang.id', '=', 'set_harga.id_barang')
            ->where('set_harga.status', 'Aktif')
            ->where('set_harga.delete', 0)
            ->get();

            // dd($barang);


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

        $validatedData['id_lokasi'] = auth()->user()->id_lokasi;
        $validatedData['create_by'] = auth()->id();
        $validatedData['last_user'] = auth()->id();

        $diskonnota = $request->diskon_nota ? preg_replace('/[Rp. ]/', '', $request->diskon_nota) : 0;
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
            $diskon = $data['diskon'] ? preg_replace('/[Rp. ]/', '', $data['diskon']) : 0;


            DB::table('penjualan_detail')->insert([
                'id_penjualan' => $penjualan->id,
                'id_barang' => $idBarang,
                'nama_barang' => $data['nama_barang'],
                'merek' => $data['merek'],
                'harga' => $harga,
                'diskon_barang' => $diskon,
                'jumlah' => $data['jumlah'],
                'create_by' => auth()->user()->id,
                'last_user' => auth()->user()->id
            ]);
        }

        Alert::success('Berhasil Menambahkan data Penjualan.');

        return redirect('/penjualan');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Penjualan';

        $pelanggan = Pelanggan::all();

        $penjualan = Penjualan::findOrFail($id);

        $penjualanDetail = PenjualanDetail::with('barang')->where('id_penjualan', $penjualan->id)->get();

        $barang = SetHarga::select('barang.id', 'barang.nama', 'barang.kode_barang', 'barang.harga', 'set_harga.merek', 'set_harga.harga_jual')
            ->join('barang', 'barang.id', '=', 'set_harga.id_barang')
            ->where('set_harga.status', 'Aktif')
            ->where('set_harga.delete', 0)
            ->get();

        $cities = Indonesia::allCities();

        foreach ($pelanggan as $p) {
            $p->nama_kota = Indonesia::findCity($p->id_kota)['name'] ?? 'Tidak Diketahui';
        }

        return view('pages.transaksi.penjualan.edit_penjualan', compact('title', 'barang', 'pelanggan', 'penjualan', 'penjualanDetail'));
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
        // dd($request->all());

        DB::beginTransaction();

        try {

            $tanggal = $request->input('tanggal');
            $noNota     = $request->input('no_nota');
            $pelanggan  = $request->input('pelanggan');
            $diskonnota = preg_replace('/[^\d]/', '', $request->input('diskon_nota'));
            $bayar      = preg_replace('/[^\d]/', '', $request->input('bayar'));
            $total = preg_replace('/[^\d]/', '', $request->input('total'));
            $sisa = preg_replace('/[^\d]/', '', $request->input('sisa'));

            $tableData  = $request->input('table_data');

            if (empty($tanggal) || empty($noNota)) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap'], 400);
            }

            $pembelian = Penjualan::findOrFail($id);

            $pembelian->update([
                'tanggal'    => $tanggal,
                'no_nota'    => $noNota,
                'id_pelanggan' => $pelanggan,
                'diskon_nota' => $diskonnota,
                'total_penjualan' => $total,
                'sisa' => $sisa,
                'bayar'      => $bayar,
                'last_user'  => auth()->user()->id
            ]);

            PenjualanDetail::where('id_penjualan', $id)->delete();

            foreach ($tableData as $key => $item) {

                if (!isset($item['id_barang'], $item['nama_barang'], $item['harga'], $item['jumlah'], $item['subtotal'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data item tidak lengkap',
                        'item' => $item
                    ], 400);
                }
                $harga = preg_replace('/[^\d]/', '', $item['harga']);
                $diskon = str_replace('.', '', $item['diskon']);

                PenjualanDetail::create([
                    'id_penjualan' => $id,
                    'id_barang'    => $item['id_barang'],
                    'nama_barang'  => $item['nama_barang'],
                    'merek'        => $item['merek'] ?? null,
                    'harga'        => $harga,
                    'jumlah'        => $item['jumlah'],
                    'diskon_barang' => $diskon,
                    'create_by'    => auth()->user()->id,
                    'last_user'    => auth()->user()->id
                ]);
            }

            DB::commit();

            Alert::success('Berhasil Merubah data Pembelian.');

            return redirect('/penjualan');


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
    public function destroy(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        $penjualan->update([
            'delete' => 1,
            'last_user' => auth()->id()
        ]);

        PenjualanDetail::where('id_penjualan', $id)->update([
            'delete' => 1,
            'last_user' => auth()->id()
        ]);

        Alert::success('Data Penjualan berhasil dihapus.');

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function modalTambahPelanggan(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $title = "Tambah Pelanggan";
        $kota = Indonesia::allCities();

        return view('components.modal.modal_tambah_pelanggan', compact('title', 'kota'));
    }

    public function tambahPelangganPenjualan(Request $request)
    {
        $validateData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required',
            'id_kota' => 'required|integer',
            'fax' => 'string|max:255',
            'kode_pos' => 'string|max:255'
        ]);

        $validateData['id_lokasi'] = auth()->user()->id_lokasi;
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

        $kota = Indonesia::findCity($detailPelanggan->id_kota, $with = null);

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

    public function downloadTamplate()
    {
        $filePath = public_path('import_tamplate/tamplate_penjualan_detail.csv');
        $fileName = 'tamplate_penjualan_detail_.csv';

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $fileName);
    }

}
