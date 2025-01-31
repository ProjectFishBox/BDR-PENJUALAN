<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StokExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;

class StokControllers extends Controller
{
    /**
     * Menampilkan laporan stok barang
     */
    public function index(Request $request)
    {
        $title = 'Laporan Stok Barang';

        $lokasi = $this->getLokasi();
        $barang = $this->getBarang();

        if ($request->ajax()) {
            $cacheKey = 'stok_data';
            $cacheDuration = now()->addMinutes(2);

            $data = Cache::remember($cacheKey, $cacheDuration, function () {
                return $this->hitungStok();
            });

            $totalMasuk = array_sum(array_column($data, 'total_masuk'));
            $totalKeluar = array_sum(array_column($data, 'total_terjual'));
            $totalStok = array_sum(array_column($data, 'stok_akhir'));

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('total_masuk', function($row) {
                    return number_format($row['total_masuk'], 0, ',', '.');
                })
                ->editColumn('total_terjual', function($row) {
                    return number_format($row['total_terjual'], 0, ',', '.');
                })
                ->editColumn('stok_akhir', function($row) {
                    return number_format($row['stok_akhir'], 0, ',', '.');
                })
                ->with([
                    'total_masuk' => number_format($totalMasuk, 0, ',', '.'),
                    'total_keluar' => number_format($totalKeluar, 0, ',', '.'),
                    'total_stok' => number_format($totalStok, 0, ',', '.')
                ])
                ->rawColumns(['action'])
                ->make(true);
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


    private function getBarangMasuk()
    {
        return PembelianDetail::query()
            ->select([
                'pembelian_detail.id_barang',
                'pembelian_detail.nama_barang',
                'pembelian_detail.merek',
                'barang.kode_barang as kode_barang'
            ])
            ->join('barang', 'pembelian_detail.id_barang', '=', 'barang.id')
            ->where('pembelian_detail.delete', 0)
            ->selectRaw('SUM(pembelian_detail.jumlah) as total_masuk')
            ->groupBy([
                'pembelian_detail.id_barang',
                'pembelian_detail.nama_barang',
                'pembelian_detail.merek',
                'barang.kode_barang'
            ])
            ->get();
    }


    private function getBarangKeluar()
    {
        return PenjualanDetail::query()
            ->select([
                'penjualan_detail.id_barang',
                'penjualan_detail.nama_barang',
                'penjualan_detail.merek',
                'barang.kode_barang as kode_barang'
            ])
            ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
            ->where('penjualan_detail.delete', 0)
            ->selectRaw('SUM(penjualan_detail.jumlah) as total_terjual')
            ->groupBy([
                'penjualan_detail.id_barang',
                'penjualan_detail.nama_barang',
                'penjualan_detail.merek',
                'barang.kode_barang'
            ])
            ->get();
    }


    private function hitungStok()
    {
        $barangMasuk = $this->getBarangMasuk();
        $barangKeluar = $this->getBarangKeluar();

        return $barangMasuk->map(function ($item) use ($barangKeluar) {
            $terjual = $barangKeluar
                ->where('id_barang', $item->id_barang)
                ->where('nama_barang', $item->nama_barang)
                ->where('merek', $item->merek)
                ->first();

            return [
                'id_barang' => $item->id_barang,
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'merek' => $item->merek,
                'total_masuk' => $item->total_masuk,
                'total_terjual' => $terjual->total_terjual ?? 0,
                'stok_akhir' => $item->total_masuk - ($terjual->total_terjual ?? 0)
            ];
        })->toArray();
    }

    public function getFilteredData(Request $request)
    {
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
            ->when($request->filled('lokasi'), function ($query) use ($request) {
                return $query->where('pembelian.id_lokasi', $request->lokasi);
            })
            ->when($request->filled('barang'), function ($query) use ($request) {
                return $query->where('pembelian_detail.id_barang', $request->barang);
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
            ->when($request->filled('lokasi'), function ($query) use ($request) {
                return $query->where('penjualan.id_lokasi', $request->lokasi);
            })
            ->when($request->filled('barang'), function ($query) use ($request) {
                return $query->where('penjualan_detail.id_barang', $request->barang);
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


        $totalMasuk = array_sum(array_column($data, 'total_masuk'));
        $totalKeluar = array_sum(array_column($data, 'total_terjual'));
        $totalStok = array_sum(array_column($data, 'stok_akhir'));



        $namaLokasi = '';
        if ($request->filled('lokasi')) {
            $namaLokasi = Lokasi::find($request->lokasi)->nama;
        }

        return view('components.modal.modal_detail_data_stok', compact('data', 'namaLokasi'));

    }

    public function printData(Request $request)
    {
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
            ->when($request->filled('lokasi'), function ($query) use ($request) {
                return $query->where('pembelian.id_lokasi', $request->lokasi);
            })
            ->when($request->filled('barang'), function ($query) use ($request) {
                return $query->where('pembelian_detail.id_barang', $request->barang);
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
            ->when($request->filled('lokasi'), function ($query) use ($request) {
                return $query->where('penjualan.id_lokasi', $request->lokasi);
            })
            ->when($request->filled('barang'), function ($query) use ($request) {
                return $query->where('penjualan_detail.id_barang', $request->barang);
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


        $totalMasuk = array_sum(array_column($data, 'total_masuk'));
        $totalKeluar = array_sum(array_column($data, 'total_terjual'));
        $totalStok = array_sum(array_column($data, 'stok_akhir'));



        $namaLokasi = '';
        if ($request->filled('lokasi')) {
            $namaLokasi = Lokasi::find($request->lokasi)->nama;
        }

        $pdf = Pdf::loadView('components.pdf.stok_pdf', compact('data', 'namaLokasi'));

        return $pdf->stream('Laporan Stok Barang -'.$namaLokasi.'.pdf');
    }

    public function exportExcel(Request $request)
    {
        try {
            $fileName = 'Laporan_Stok';

            if ($request->filled('lokasi')) {
                $namaLokasi = Lokasi::find($request->lokasi)->nama;
                $fileName .= '_' . str_replace(' ', '_', $namaLokasi);
            }

            if ($request->filled('barang')) {
                $namaBarang = Barang::find($request->barang)->nama;
                $fileName .= '_' . str_replace(' ', '_', $namaBarang);
            }

            if ($request->filled('merek')) {
                $fileName .= '_' . str_replace(' ', '_', $request->merek);
            }

            $fileName .= '_' . date('d-m-Y') . '.xlsx';

            return Excel::download(
                new StokExport($request),
                $fileName,
                \Maatwebsite\Excel\Excel::XLSX
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage()
            ], 500);
        }
    }


}
