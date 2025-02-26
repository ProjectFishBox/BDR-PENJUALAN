<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

use App\Models\Lokasi;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Pengeluaran;

class DashboardControllers extends Controller
{

    public function index(Request $request)
    {
        $title = "Dashboard";
        $lokasi = Lokasi::where('delete', 0)->get();
        $barang = Barang::where('delete', 0)->get();
        if ($request->ajax()) {
            $lokasiId = $request->lokasi;
            $barangNama = $request->barang;
            $merek = $request->merek;
            $dateRange = $request->daterange;

            $totalPembelian = Pembelian::query()
                ->join('pembelian_detail', 'pembelian.id', '=', 'pembelian_detail.id_pembelian')
                ->selectRaw('SUM(pembelian_detail.subtotal) as total_pembelian')
                ->where('pembelian.delete', 0)
                ->where('pembelian_detail.delete', 0)
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->when($request->input('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('id_lokasi', $lokasiId);
                })
                ->when($request->input('barang')  && $barangNama !== 'all', function ($query) use ($barangNama) {
                    return $query->whereHas('detail', function ($q) use ($barangNama) {
                        $q->where('id_barang', $barangNama);
                    });
                })
                ->when($request->filled('merek'), function ($query) use ($request) {
                    return $query->where('pembelian_detail.merek', $request->merek);
                })
                ->first();


            $totalPenjualan = Penjualan::query()
                ->join('penjualan_detail', 'penjualan.id', '=', 'penjualan_detail.id_penjualan')
                ->selectRaw('
                    SUM(
                        (penjualan_detail.harga * penjualan_detail.jumlah) -
                        (penjualan_detail.diskon_barang * penjualan_detail.jumlah)
                    ) - SUM(DISTINCT penjualan.diskon_nota) as total_penjualan
                ')
                ->where('penjualan.delete', 0)
                ->where('penjualan_detail.delete', 0)
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->when($request->input('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('id_lokasi', $lokasiId);
                })
                ->when($request->input('barang') && $barangNama !== 'all', function ($query) use ($barangNama) {
                    return $query->whereHas('detail', function ($q) use ($barangNama) {
                        $q->where('id_barang', $barangNama);
                    });
                })
                ->when($request->filled('merek'), function ($query) use ($request) {
                    return $query->where('penjualan_detail.merek', $request->merek);
                })
                ->first();


            $totalPengeluaran = Pengeluaran::query()
                ->selectRaw('SUM(total) as total_pengeluaran')
                ->where('pengeluaran.delete', 0)
                ->when($request->input('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('pengeluaran.id_lokasi', $lokasiId);
                })
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->first();


            $stokMasuk = PembelianDetail::query()
                ->join('pembelian', 'pembelian.id', '=', 'pembelian_detail.id_pembelian')
                ->where('pembelian_detail.delete', 0)
                ->where('pembelian.delete', 0)
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('pembelian.tanggal', [$startDate, $endDate]);
                })
                ->when($request->input('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('pembelian.id_lokasi', $lokasiId);
                })
                ->when($request->input('barang') && $barangNama !== 'all', function ($query) use ($barangNama) {
                    return $query->where('pembelian_detail.id_barang', $barangNama);
                })
                ->when($request->input('merek'), function ($query) use ($request) {
                    return $query->where('pembelian_detail.merek', $request->input('merek'));
                })
                ->where('pembelian.delete', 0)
                ->where('pembelian_detail.delete', 0)
                ->sum('pembelian_detail.jumlah');

            $stokKeluar = PenjualanDetail::query()
                ->join('penjualan', 'penjualan.id', '=', 'penjualan_detail.id_penjualan')
                ->where('penjualan_detail.delete', 0)
                ->where('penjualan.delete', 0)
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('penjualan.tanggal', [$startDate, $endDate]);
                })
                ->when($request->input('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('penjualan.id_lokasi', $lokasiId);
                })
                ->when($request->input('barang')  && $barangNama !== 'all', function ($query) use ($barangNama) {
                    return $query->where('penjualan_detail.id_barang', $barangNama);
                })
                ->when($request->input('merek'), function ($query) use ($request) {
                    return $query->where('penjualan_detail.merek', $request->input('merek'));
                })
                ->where('penjualan.delete', 0)
                ->where('penjualan_detail.delete', 0)
                ->sum('penjualan_detail.jumlah');

            $totalPenjualanNominal = PenjualanDetail::query()
                ->join('penjualan', 'penjualan.id', '=', 'penjualan_detail.id_penjualan')
                ->selectRaw('
                    SUM(
                        (penjualan_detail.harga * penjualan_detail.jumlah) -
                        (penjualan_detail.diskon_barang * penjualan_detail.jumlah)
                    ) - SUM(DISTINCT penjualan.diskon_nota) as total_penjualan_nominal
                ')
                ->where('penjualan_detail.delete', 0)
                ->where('penjualan.delete', 0)
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('penjualan.tanggal', [$startDate, $endDate]);
                })
                ->when($request->input('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('penjualan.id_lokasi', $lokasiId);
                })
                ->when($request->input('barang') && $barangNama !== 'all', function ($query) use ($barangNama) {
                    return $query->where('penjualan_detail.id_barang', $barangNama);
                })
                ->when($request->input('merek'), function ($query) use ($request) {
                    return $query->where('penjualan_detail.merek', $request->input('merek'));
                })
                ->first();


            $sisaStok = $stokMasuk - $stokKeluar;

            return response()->json([
                'total_pembelian' => (float) ($totalPembelian->total_pembelian ?? 0),
                'total_penjualan' => (float) ($totalPenjualan->total_penjualan ?? 0),
                'total_pengeluaran' => (float) ($totalPengeluaran->total_pengeluaran ?? 0),
                'stok_masuk' => (float) ($stokMasuk) ?? 0,
                'stok_keluar' => (float) ($stokKeluar) ?? 0,
                'sisa_stok' => (float) ($sisaStok) ?? 0,
                'total_nilai_jual' => (float) ($totalPenjualanNominal->total_penjualan_nominal ?? 0),
            ]);
        }

        return view('pages.dashboard.index', compact('title', 'lokasi', 'barang'));
    }

}
