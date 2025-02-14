<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StokExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;


use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;

class StokControllers extends Controller
{
    public function index(Request $request)
    {
        $title = 'Laporan Stok Barang';

        $lokasi = $this->getLokasi();
        $barang = $this->getBarang();

        if ($request->ajax()) {
            $lokasiId = $request->lokasi;
            $barangId = $request->barang;

            $data = $this->getData($request);

            $totalMasuk = array_sum(array_column($data, 'total_masuk'));
            $totalKeluar = array_sum(array_column($data, 'total_terjual'));
            $totalStok = array_sum(array_column($data, 'stok_akhir'));

            $namaLokasi = '';
            if ($request->filled('lokasi') && $lokasiId !== 'all') {
                $lokasiData = Lokasi::find($lokasiId);
                $namaLokasi = $lokasiData ? $lokasiData->nama : '';
            }

            return response()->json($data);
        }

        return view('pages.laporan.stok.stok', compact('title', 'barang', 'lokasi'));
    }

    private function getLokasi()
    {
        return Lokasi::all();
    }


    private function getBarang()
    {
        return Barang::all();
    }


    public function getData(Request $request)
    {
        $lokasiId = $request->lokasi;
        $barangId = $request->barang;

        $barangMasuk = PembelianDetail::query()
            ->select([
                'pembelian_detail.id_barang',
                'pembelian_detail.nama_barang',
                'pembelian_detail.merek',
                'barang.kode_barang as kode_barang',
                'pembelian.id_lokasi',
                'lokasi.nama'
            ])
            ->join('barang', 'pembelian_detail.id_barang', '=', 'barang.id')
            ->join('pembelian', 'pembelian_detail.id_pembelian', '=', 'pembelian.id')
            ->join('lokasi', 'pembelian.id_lokasi', '=', 'lokasi.id')
            ->where('pembelian_detail.delete', 0)
            ->when($request->filled('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                return $query->where('pembelian.id_lokasi', $lokasiId);
            })
            ->when($request->filled('barang') && $barangId !== 'all', function ($query) use ($barangId) {
                return $query->where('pembelian_detail.id_barang', $barangId);
            })
            ->when($request->filled('merek'), function ($query) use ($request) {
                return $query->where('pembelian_detail.merek', $request->merek);
            })
            ->selectRaw('SUM(pembelian_detail.jumlah) as total_masuk')
            ->groupBy([
                'pembelian_detail.id_barang',
                'pembelian_detail.nama_barang',
                'pembelian_detail.merek',
                'barang.kode_barang',
                'pembelian.id_lokasi',
                'lokasi.nama'
            ])
            ->get();

        $barangKeluar = PenjualanDetail::query()
            ->select([
                'penjualan_detail.id_barang',
                'penjualan_detail.nama_barang',
                'penjualan_detail.merek',
                'barang.kode_barang as kode_barang',
                'penjualan.id_lokasi',
                'lokasi.nama'
            ])
            ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id')
            ->join('lokasi', 'penjualan.id_lokasi', '=', 'lokasi.id')
            ->where('penjualan_detail.delete', 0)
            ->when($request->filled('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                return $query->where('penjualan.id_lokasi', $lokasiId);
            })
            ->when($request->filled('barang') && $barangId !== 'all', function ($query) use ($barangId) {
                return $query->where('penjualan_detail.id_barang', $barangId);
            })
            ->when($request->filled('merek'), function ($query) use ($request) {
                return $query->where('penjualan_detail.merek', $request->merek);
            })
            ->selectRaw('SUM(penjualan_detail.jumlah) as total_terjual')
            ->groupBy([
                'penjualan_detail.id_barang',
                'penjualan_detail.nama_barang',
                'penjualan_detail.merek',
                'barang.kode_barang',
                'penjualan.id_lokasi',
                'lokasi.nama'
            ])
            ->get();

        $data = $barangMasuk->map(function ($item) use ($barangKeluar) {
            $terjual = $barangKeluar
                ->where('id_barang', $item->id_barang)
                ->where('nama_barang', $item->nama_barang)
                ->where('merek', $item->merek)
                ->where('id_lokasi', $item->id_lokasi)
                ->first();

            return [
                'id_barang' => $item->id_barang,
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'merek' => $item->merek,
                'id_lokasi' => $item->id_lokasi,
                'nama_lokasi' => $item->nama,
                'total_masuk' => $item->total_masuk,
                'total_terjual' => $terjual->total_terjual ?? 0,
                'stok_akhir' => $item->total_masuk - ($terjual->total_terjual ?? 0)
            ];
        })->toArray();

        return $data;
    }


    public function printData(Request $request)
    {

        $lokasiId = $request->lokasi;
        $barangId = $request->barang;

        $data = $this->getData($request);

        $totalMasuk = array_sum(array_column($data, 'total_masuk'));
        $totalKeluar = array_sum(array_column($data, 'total_terjual'));
        $totalStok = array_sum(array_column($data, 'stok_akhir'));

        $namaLokasi = '';
        if ($request->filled('lokasi') && $lokasiId !== 'all') {
            $lokasiData = Lokasi::find($lokasiId);
            $namaLokasi = $lokasiData ? $lokasiData->nama : '';
        }

        $pdf = Pdf::loadView('components.pdf.stok_pdf', compact('data', 'namaLokasi'))
        ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan Stok Barang -'.$namaLokasi.'.pdf');
    }

    public function exportExcel(Request $request)
    {
        Excel::store(new StokExport($request), 'temp.xlsx');

        try {
            $filePath = session('export_file');

            if (!$filePath || !Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengexport data. File tidak ditemukan.'
                ]);
            }

            $fileName = 'Laporan_Stok_' . date('d-m-Y') . '.xlsx';

            return Storage::download($filePath, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage()
            ], 500);
        }
    }
}
