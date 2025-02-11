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
        $lokasi = Lokasi::all();
        $barang = Barang::all();

        if ($request->ajax()) {
            $lokasiId = $request->lokasi;
            $barangNama = $request->barang;
            $merek = $request->merek;
            $dateRange = $request->daterange;

            $totalPembelian = Pembelian::query()
                ->join('pembelian_detail', 'pembelian.id', '=', 'pembelian_detail.id_pembelian')
                ->selectRaw('SUM(pembelian_detail.subtotal) as total_pembelian')
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    return $query->whereBetween('tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->when($request->input('lokasi'), function ($query) use ($request) {
                    return $query->where('id_lokasi', $request->input('lokasi'));
                })
                ->when($request->input('barang'), function ($query) use ($request) {
                    return $query->whereHas('detail', function ($q) use ($request) {
                        $q->where('id_barang', $request->input('barang'));
                    });
                })
                ->when($request->filled('merek'), function ($query) use ($request) {
                    return $query->where('pembelian_detail.merek', $request->merek);
                })
                ->first();


            $totalPenjualan = Penjualan::query()
                ->join('penjualan_detail', 'penjualan.id', '=', 'penjualan_detail.id_penjualan')
                ->selectRaw('SUM(penjualan_detail.harga * penjualan_detail.jumlah - (penjualan_detail.diskon_barang * penjualan_detail.jumlah)) as total_penjualan')

                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    return $query->whereBetween('tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->when($request->input('lokasi'), function ($query) use ($request) {
                    return $query->where('id_lokasi', $request->input('lokasi'));
                })
                ->when($request->input('barang'), function ($query) use ($request) {
                    return $query->whereHas('detail', function ($q) use ($request) {
                        $q->where('id_barang', $request->input('barang'));
                    });
                })
                ->when($request->filled('merek'), function ($query) use ($request) {
                    return $query->where('penjualan_detail.merek', $request->merek);
                })
                ->first();


            $totalPengeluaran = Pengeluaran::query()
                ->selectRaw('SUM(total) as total_pengeluaran')

                ->when($request->input('lokasi'), function ($query) use ($request) {
                    return $query->where('pengeluaran.id_lokasi', $request->lokasi);
                })
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    return $query->whereBetween('tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->first();


            $stokMasuk = PembelianDetail::query()
                ->join('pembelian', 'pembelian.id', '=', 'pembelian_detail.id_pembelian')
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    return $query->whereBetween('pembelian.tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->when($request->input('lokasi'), function ($query) use ($request) {
                    return $query->where('pembelian.id_lokasi', $request->input('lokasi'));
                })
                ->when($request->input('barang'), function ($query) use ($request) {
                    return $query->where('pembelian_detail.id_barang', $request->input('barang'));
                })
                ->when($request->input('merek'), function ($query) use ($request) {
                    return $query->where('pembelian_detail.merek', $request->input('merek'));
                })
                ->where('pembelian.delete', 0)
                ->where('pembelian_detail.delete', 0)
                ->sum('pembelian_detail.jumlah');

            $stokKeluar = PenjualanDetail::query()
                ->join('penjualan', 'penjualan.id', '=', 'penjualan_detail.id_penjualan')
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    return $query->whereBetween('penjualan.tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->when($request->input('lokasi'), function ($query) use ($request) {
                    return $query->where('penjualan.id_lokasi', $request->input('lokasi'));
                })
                ->when($request->input('barang'), function ($query) use ($request) {
                    return $query->where('penjualan_detail.id_barang', $request->input('barang'));
                })
                ->when($request->input('merek'), function ($query) use ($request) {
                    return $query->where('penjualan_detail.merek', $request->input('merek'));
                })
                ->where('penjualan.delete', 0)
                ->where('penjualan_detail.delete', 0)
                ->sum('penjualan_detail.jumlah');

            $sisaStok = $stokMasuk - $stokKeluar;

            return response()->json([
                'total_pembelian' => (float) ($totalPembelian->total_pembelian ?? 0),
                'total_penjualan' => (float) ($totalPenjualan->total_penjualan ?? 0),
                'total_pengeluaran' => (float) ($totalPengeluaran->total_pengeluaran ?? 0),
                'stok_masuk' => (float) ($stokMasuk) ?? 0,
                'stok_keluar' => (float) ($stokKeluar) ?? 0,
                'sisa_stok' => (float) ($sisaStok) ?? 0

            ]);
        }

        return view('pages.dashboard.index', compact('title', 'lokasi', 'barang'));
    }

}
