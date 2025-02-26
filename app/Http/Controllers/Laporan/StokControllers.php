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
        return Lokasi::where('delete', 0)->get();
    }


    private function getBarang()
    {
        return Barang::where('delete', 0)->get();
    }


    public function getData(Request $request)
    {
        $lokasiId = $request->lokasi;
        $barangId = $request->barang;

        $barangList = Barang::query()
            ->select([
                'barang.id as id_barang',
                'barang.kode_barang',
                'barang.nama as nama_barang',
                'barang.merek'
            ])
            ->where('barang.delete', 0)
            ->when($request->filled('barang') && $barangId !== 'all', function ($query) use ($barangId) {
                return $query->where('barang.id', $barangId);
            })
            ->when($request->filled('merek'), function ($query) use ($request) {
                return $query->where('barang.merek', $request->merek);
            })
            ->get();

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

        $data = $barangList->map(function ($barang) use ($barangMasuk, $barangKeluar) {
            $masuk = $barangMasuk->where('id_barang', $barang->id_barang)->first();
            $keluar = $barangKeluar->where('id_barang', $barang->id_barang)->first();

            return [
                'id_barang' => $barang->id_barang,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'merek' => $barang->merek,
                'id_lokasi' => $masuk->id_lokasi ?? $keluar->id_lokasi ?? null,
                'nama_lokasi' => $masuk->nama ?? $keluar->nama ?? null,
                'total_masuk' => $masuk->total_masuk ?? 0,
                'total_terjual' => $keluar->total_terjual ?? 0,
                'stok_akhir' => ($masuk->total_masuk ?? 0) - ($keluar->total_terjual ?? 0)
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
