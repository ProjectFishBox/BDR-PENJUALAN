<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\PendapatanExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\Lokasi;
use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;

class LaporanPendapatanControllers extends Controller
{

    public function index(Request $request)
    {
        $title = 'Laporan Pendapatan';

        $lokasi = Lokasi::all();

        if ($request->ajax()) {
        $lokasiId = $request->lokasi;

        $penjualan = PenjualanDetail::query()
            ->select([
                'penjualan_detail.id_barang',
                'penjualan_detail.nama_barang',
                'penjualan_detail.merek',
                'penjualan_detail.harga',
                'penjualan_detail.diskon_barang',
                'penjualan_detail.jumlah',
                'penjualan_detail.id_penjualan',
                'barang.kode_barang as kode_barang',
                'penjualan.id_lokasi',
                'penjualan.tanggal',
                'penjualan.diskon_nota',
                'lokasi.nama',
                DB::raw('(SELECT pembelian_detail.harga
                        FROM pembelian_detail
                        JOIN pembelian ON pembelian_detail.id_pembelian = pembelian.id
                        WHERE pembelian.tanggal = penjualan.tanggal
                        AND pembelian_detail.id_barang = penjualan_detail.id_barang
                        ORDER BY pembelian.id DESC
                        LIMIT 1) as harga_pembelian'),
                DB::raw('(SELECT SUM(pengeluaran.total)
                        FROM pengeluaran
                        WHERE pengeluaran.tanggal = penjualan.tanggal) as total_pengeluaran')
            ])
            ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id')
            ->join('lokasi', 'penjualan.id_lokasi', '=', 'lokasi.id')
            ->where('penjualan_detail.delete', 0)
            ->when($request->filled('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                return $query->where('penjualan.id_lokasi', $lokasiId);
            })
            ->when($request->filled('daterange'), function ($query) use ($request) {
                $dates = explode(' - ', $request->input('daterange'));
                $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal . '-' . $item->id_barang . '-' . $item->kode_barang . '-' . $item->merek . '-' . $item->harga;
            })
            ->map(function ($group) {
                $firstItem = $group->first();

                return [
                    'id_penjualan' => $firstItem->id_penjualan,
                    'id_barang' => $firstItem->id_barang,
                    'nama_barang' => $firstItem->nama_barang,
                    'merek' => $firstItem->merek,
                    'harga' => $firstItem->harga,
                    'diskon_barang' => $firstItem->diskon_barang,
                    'kode_barang' => $firstItem->kode_barang,
                    'tanggal' => $firstItem->tanggal,
                    'total_jual' => $group->sum(function ($item) {
                        return $item->harga * $item->jumlah;
                    }),
                    'total_diskon_barang' => $group->sum(function ($item) {
                        return $item->diskon_barang * $item->jumlah;
                    }),
                    'total_jumlah' => $group->sum('jumlah'),
                    'diskon_nota' => $firstItem->diskon_nota,
                    'harga_pembelian' => $firstItem->harga_pembelian ?? DB::table('barang')->where('id', $firstItem->id_barang)->value('harga'),
                    'total_pengeluaran' => $firstItem->total_pengeluaran ?? 0,

                ];
            })
        ->values();
        return response()->json($penjualan);


        }

        return view('pages.laporan.laporan_pendapatan.laporan_pendapatan', compact('title', 'lokasi'));
    }


    public function printData(Request $request)
    {
        try {

            $penjualan = PenjualanDetail::query()
            ->select([
                'penjualan_detail.id_barang',
                'penjualan_detail.nama_barang',
                'penjualan_detail.merek',
                'penjualan_detail.harga',
                'penjualan_detail.diskon_barang',
                'penjualan_detail.jumlah',
                'penjualan_detail.id_penjualan',
                'barang.kode_barang as kode_barang',
                'penjualan.id_lokasi',
                'penjualan.tanggal',
                'penjualan.diskon_nota',
                'lokasi.nama',
                DB::raw('(SELECT pembelian_detail.harga
                        FROM pembelian_detail
                        JOIN pembelian ON pembelian_detail.id_pembelian = pembelian.id
                        WHERE pembelian.tanggal = penjualan.tanggal
                        AND pembelian_detail.id_barang = penjualan_detail.id_barang
                        ORDER BY pembelian.id DESC
                        LIMIT 1) as harga_pembelian'),
                DB::raw('(SELECT SUM(pengeluaran.total)
                        FROM pengeluaran
                        WHERE pengeluaran.tanggal = penjualan.tanggal) as total_pengeluaran')
            ])
            ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id')
            ->join('lokasi', 'penjualan.id_lokasi', '=', 'lokasi.id')
            ->where('penjualan_detail.delete', 0)
            ->when($request->filled('lokasi') && $request->lokasi != 'all', function ($query) use ($request) {
                return $query->where('penjualan.id_lokasi', $request->lokasi);
            })
            ->when($request->filled('daterange'), function ($query) use ($request) {
                $dates = explode(' - ', $request->input('daterange'));
                $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal . '-' . $item->id_barang . '-' . $item->kode_barang . '-' . $item->merek . '-' . $item->harga;
            })
            ->map(function ($group) {
                $firstItem = $group->first();

                $totalPenjualanItem = $group->sum('jumlah');
                $totalPembelianItem = $firstItem->harga_pembelian ?? DB::table('barang')->where('id', $firstItem->id_barang)->value('harga');


                return [
                    'id_penjualan' => $firstItem->id_penjualan,
                    'id_barang' => $firstItem->id_barang,
                    'nama_barang' => $firstItem->nama_barang,
                    'merek' => $firstItem->merek,
                    'harga' => $firstItem->harga,
                    'diskon_barang' => $firstItem->diskon_barang,
                    'kode_barang' => $firstItem->kode_barang,
                    'tanggal' => $firstItem->tanggal,
                    'total_jual' => $group->sum(function ($item) {
                        return $item->harga * $item->jumlah;
                    }),
                    'total_diskon_barang' => $group->sum(function ($item) {
                        return $item->diskon_barang * $item->jumlah;
                    }),
                    'total_jumlah' => $group->sum('jumlah'),
                    'diskon_nota' => $firstItem->diskon_nota,
                    'harga_pembelian' => $firstItem->harga_pembelian ?? DB::table('barang')->where('id', $firstItem->id_barang)->value('harga'),
                    'total_pengeluaran' => $firstItem->total_pengeluaran ?? 0,
                    'modal_usaha' => $totalPenjualanItem * $totalPembelianItem,

                ];
            })
            ->values();

            $totalPenjualan = $penjualan->sum('total_jumlah');
            $totalTerjual = $penjualan->sum('total_jual');
            $totalDiskonProduk = $penjualan->sum('total_diskon_barang');
            $totalPembelian = $penjualan->sum('harga_pembelian');
            $totalPengeluaran = $penjualan->sum('total_pengeluaran');
            $modalUsaha = $penjualan->sum('modal_usaha');



            $uniqueDiskonNota = [];

            foreach ($penjualan as $item) {
                if (!isset($uniqueDiskonNota[$item['id_penjualan']])) {
                    $uniqueDiskonNota[$item['id_penjualan']] = $item['diskon_nota'];
                }

            }
            $totalDiskonNota = array_sum($uniqueDiskonNota);

            $totalTransfer = ($totalTerjual - ($totalPengeluaran + $totalDiskonNota + $totalDiskonProduk));
            $labaBersih = $totalTransfer - $modalUsaha;

            $tanggal = $request->daterange;
            $lokasi = 'SEMUA LOKASI';
            if ($request->lokasi !== 'all') {
                $lokasiObj = Lokasi::find($request->lokasi);
                $lokasi = $lokasiObj ? $lokasiObj->nama : 'SEMUA LOKASI';
            }


            if (empty($penjualan)) {
                throw new \Exception("Data tidak ditemukan");
            }



            $pdf = Pdf::loadView('components.pdf.pendapatan_pdf', compact('penjualan','lokasi','tanggal','modalUsaha', 'labaBersih', 'totalTransfer', 'totalPengeluaran','totalTerjual', 'totalPenjualan', 'totalDiskonProduk', 'totalPembelian', 'totalDiskonNota'))
                ->setPaper('a4', 'landscape');
            return $pdf->stream('Laporan_Pendapatan.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {


        Excel::store(new PendapatanExport($request), 'temp.xlsx');

        try {

            $filePath = session('export_file');

            if (!$filePath || !Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengexport data. File tidak ditemukan.'
                ]);
            }

            $fileName = 'Laporan_Pendapatan';

            $fileName .= '_' . date('d-m-Y') . '.xlsx';

            return Storage::download($filePath, $fileName);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage()
            ], 500);
        }
    }





}
