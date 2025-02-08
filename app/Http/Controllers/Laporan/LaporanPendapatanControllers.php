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
            ->when($request->filled('lokasi'), function ($query) use ($request) {
                return $query->where('penjualan.id_lokasi', $request->lokasi);
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
                    // 'total_pembelian_detail_barang' => $group->sum(function ($item) {
                    //     return $item->harga  * $item->jumlah;
                    // }),
                    'total_jual' => $group->sum(function ($item) {
                        return $item->harga * $item->jumlah;
                    }),
                    'total_diskon_barang' => $group->sum(function ($item) {
                        return $item->diskon_barang * $item->jumlah;
                    }),
                    'total_jumlah' => $group->sum('jumlah'),
                    'diskon_nota' => $firstItem->diskon_nota,
                    'harga_pembelian' => $firstItem->harga_pembelian ?? DB::table('barang')->where('id', $firstItem->id_barang)->value('harga'),
                    'total_pengeluaran' => $firstItem->total_pengeluaran ?? 0 // Tambahan total pengeluaran unik
                ];
            })
        ->values();


        // dd($penjualan);

        return response()->json($penjualan);


        }

        return view('pages.laporan.laporan_pendapatan.laporan_pendapatan', compact('title', 'lokasi'));
    }


    public function printData(Request $request)
    {
        try {

            $Pembelian = PembelianDetail::query()
                ->select([
                    'pembelian_detail.id_barang',
                    'pembelian_detail.nama_barang',
                    'pembelian_detail.merek',
                    'pembelian_detail.harga',
                    'pembelian_detail.jumlah',
                    'pembelian_detail.subtotal',
                    'pembelian.id_lokasi',
                    'barang.kode_barang as kode_barang',
                    'lokasi.nama'
                ])
                ->join('barang', 'pembelian_detail.id_barang', '=', 'barang.id')
                ->join('pembelian', 'pembelian_detail.id_pembelian', '=', 'pembelian.id')
                ->join('lokasi', 'pembelian.id_lokasi', '=', 'lokasi.id')
                ->where('pembelian_detail.delete', 0)
                ->when($request->filled('lokasi'), function ($query) use ($request) {
                    return $query->where('pembelian.id_lokasi', $request->lokasi);
                })
                ->selectRaw('SUM(pembelian_detail.jumlah) as total_masuk')
                ->selectRaw('SUM(pembelian_detail.subtotal) as total_pembelian')
                ->groupBy([
                    'pembelian_detail.id_barang',
                    'pembelian_detail.nama_barang',
                    'pembelian_detail.merek',
                    'pembelian_detail.harga',
                    'pembelian_detail.jumlah',
                    'pembelian_detail.subtotal',
                    'barang.kode_barang',
                    'pembelian.id_lokasi',
                    'lokasi.nama'
                ])
                ->get();


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
                    'lokasi.nama'
                ])
                ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
                ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id')
                ->join('lokasi', 'penjualan.id_lokasi', '=', 'lokasi.id')
                ->where('penjualan_detail.delete', 0)
                ->when($request->filled('lokasi'), function ($query) use ($request) {
                    return $query->where('penjualan.id_lokasi', $request->lokasi);
                })
                ->selectRaw('SUM(penjualan_detail.jumlah) as total_terjual')
                ->selectRaw('SUM((penjualan_detail.harga - penjualan_detail.diskon_barang) * penjualan_detail.jumlah) as total_penjualan')
                ->selectRaw('SUM(penjualan.diskon_nota) as total_diskon_nota')
                ->groupBy([
                    'penjualan_detail.id_barang',
                    'penjualan_detail.nama_barang',
                    'penjualan_detail.merek',
                    'penjualan_detail.harga',
                    'penjualan_detail.diskon_barang',
                    'penjualan_detail.jumlah',
                    'penjualan_detail.id_penjualan',
                    'barang.kode_barang',
                    'penjualan.id_lokasi',
                    'penjualan.tanggal',
                    'penjualan.diskon_nota',
                    'lokasi.nama'
                ])
                ->get();

            $data = $Pembelian->flatMap(function ($item) use ($penjualan) {
                $terjual = $penjualan
                    ->where('id_barang', $item->id_barang)
                    ->where('nama_barang', $item->nama_barang)
                    ->where('merek', $item->merek)
                    ->where('id_lokasi', $item->id_lokasi);

                return $terjual->map(function ($jual) use ($item) {
                    return [
                        'id_barang' => $item->id_barang,
                        'kode_barang' => $item->kode_barang,
                        'nama_barang' => $item->nama_barang,
                        'merek' => $item->merek,
                        'diskon_barang' => $jual->diskon_barang,
                        'diskon_nota' => $jual->diskon_nota,
                        'id_lokasi' => $item->id_lokasi,
                        'tanggal' => $jual->tanggal,
                        'nama_lokasi' => $item->nama,
                        'total_masuk' => $item->total_masuk,
                        'harga_pembelian' => $item->harga,
                        'jumlah_pembelian' => $item->jumlah,
                        'harga_penjualan' => $jual->harga,
                        'total_terjual' => $jual->total_terjual ?? 0,
                        'stok_akhir' => $item->total_masuk - ($jual->total_terjual ?? 0),
                        'total_pembelian' => $item->total_pembelian,
                        'total_penjualan' => $jual->total_penjualan,
                        'total_diskon_nota' => $jual->total_diskon_nota,
                        'id_penjualan' => $jual->id_penjualan
                    ];
                });
            })->toArray();

            $totalTerjual = array_sum(array_column($data, 'total_terjual'));
            $totalPenjualan = array_sum(array_column($data, 'total_penjualan'));
            $totalDiskonProduk = array_sum(array_column($data, 'diskon_barang'));
            $totalPembelian = array_sum(array_column($data, 'total_pembelian'));

            $uniqueDiskonNota = [];
            foreach ($data as $item) {
                if (!isset($uniqueDiskonNota[$item['id_penjualan']])) {
                    $uniqueDiskonNota[$item['id_penjualan']] = $item['diskon_nota'];
                }
            }
            $totalDiskonNota = array_sum($uniqueDiskonNota);



            if (empty($data)) {
                throw new \Exception("Data tidak ditemukan");
            }

            $pdf = Pdf::loadView('components.pdf.pendapatan_pdf', compact('data', 'totalTerjual', 'totalPenjualan', 'totalDiskonProduk', 'totalPembelian', 'totalDiskonNota'))
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
