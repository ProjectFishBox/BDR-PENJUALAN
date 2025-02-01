<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;



use App\Models\Barang;
use App\Models\Lokasi;
use App\Exports\LaporanPenjualanExport;


class LaporanPembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $title = 'Laporan Pembelian';
        $barang = Barang::all();
        $lokasi = Lokasi::all();

        if ($request->ajax()) {
            $cacheKey = 'laporan_pembelian_data';
            $cacheDuration = now()->addMinutes(2);

            $data = Cache::remember($cacheKey, $cacheDuration, function () {
                return $this->getLaporanPenjualan();
            });


            $flattenedData = collect($data)->flatMap(function ($item) {
                return collect($item['detail'])->map(function ($detail) use ($item) {
                    return [
                        'id' => $item['id'],
                        'tanggal' => $item['tanggal'],
                        'no_nota' => $item['no_nota'],
                        'total' => $item['total'],
                        'kode_barang' => $detail['kode_barang'],
                        'nama_barang' => $detail['nama_barang'],
                        'merek' => $detail['merek'],
                        'jumlah' => $detail['jumlah'],
                        'harga' => $detail['harga'],
                        'total_item' => number_format($detail['total_item'],0, ',', '.' )
                    ];
                });
            })->values();

            $totalPenjualan = array_sum(array_column($data, 'total'));

            return DataTables::of($flattenedData)
                ->addIndexColumn()
                ->editColumn('harga', function($row) {
                    return number_format($row['harga'], 0, ',', '.');
                })
                ->editColumn('jumlah', function($row) {
                    return number_format($row['jumlah'], 0, ',', '.');
                })
                ->editColumn('total', function($row) {
                    return number_format($row['total'], 0, ',', '.');
                })
                ->with([
                    'total_penjualan' => number_format($totalPenjualan, 0, ',', '.')
                ])
                ->make(true);
        }

        return view('pages.laporan.laporan_pembelian.laporan_pembelian', compact('title', 'barang', 'lokasi'));
    }

    public function getLaporanPenjualan()
    {
        try {
            $query = DB::table('penjualan as p')
                ->join('penjualan_detail as dp', 'p.id', '=', 'dp.id_penjualan')
                ->join('barang as b', 'b.id', '=', 'dp.id_barang')
                ->select(
                    'p.id',
                    'p.tanggal',
                    'p.no_nota',
                    'dp.id_barang',
                    'b.nama',
                    'dp.jumlah',
                    'dp.harga',
                    'dp.diskon_barang',
                    'dp.merek',
                    'b.kode_barang',
                    DB::raw('((dp.harga - dp.diskon_barang) * dp.jumlah) as total_item')
                );

            $data = $query->orderBy('p.tanggal', 'desc')
                        ->orderBy('p.id', 'desc')
                        ->get();

            $result = [];
            foreach ($data as $row) {
                if (!isset($result[$row->id])) {
                    $result[$row->id] = [
                        'id' => $row->id,
                        'tanggal' => $row->tanggal,
                        'no_nota' => $row->no_nota,
                        'total' => 0,
                        'detail' => []
                    ];
                }

                $result[$row->id]['total'] += $row->total_item;


                $result[$row->id]['detail'][] = [
                    'id_barang' => $row->id_barang,
                    'kode_barang' => $row->kode_barang,
                    'nama_barang' => $row->nama,
                    'merek' => $row->merek,
                    'jumlah' => $row->jumlah,
                    'harga' => $row->harga,
                    'diskon_barang' => $row->diskon_barang,
                    'total_item' => $row->total_item
                ];
            }

            foreach ($result as &$item) {
                $item['total_penjualan'] = $item['total'];
            }

            return array_values($result);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getFilteredData(Request $request)
    {

        try {
            $query = DB::table('penjualan as p')
                ->join('penjualan_detail as dp', 'p.id', '=', 'dp.id_penjualan')
                ->join('barang as b', 'b.id', '=', 'dp.id_barang')
                ->select(
                    'p.id',
                    'p.tanggal',
                    'p.no_nota',
                    'dp.id_barang',
                    'b.nama',
                    'dp.jumlah',
                    'dp.harga',
                    'dp.diskon_barang',
                    'dp.merek',
                    'b.kode_barang',
                    DB::raw('((dp.harga - dp.diskon_barang) * dp.jumlah) as total_item')
                );

            if ($request->filled('daterange')) {
                $dates = explode(' - ', $request->daterange);
                $startDate = $dates[0];
                $endDate = $dates[1];
                $query->whereBetween('p.tanggal', [$startDate, $endDate]);
            }

            if ($request->filled('lokasi')) {
                $query->where('p.id_lokasi', $request->lokasi);
            }

            if ($request->filled('merek')) {
                $query->where('dp.merek', $request->merek);
            }

            $data = $query->orderBy('p.tanggal', 'desc')
                        ->orderBy('p.id', 'desc')
                        ->get();

            $result = [];
            foreach ($data as $row) {
                if (!isset($result[$row->id])) {
                    $result[$row->id] = [
                        'id' => $row->id,
                        'tanggal' => $row->tanggal,
                        'no_nota' => $row->no_nota,
                        'total' => 0,
                        'detail' => []
                    ];
                }

                $result[$row->id]['total'] += $row->total_item;

                $result[$row->id]['detail'][] = [
                    'id_barang' => $row->id_barang,
                    'kode_barang' => $row->kode_barang,
                    'nama_barang' => $row->nama,
                    'merek' => $row->merek,
                    'jumlah' => $row->jumlah,
                    'harga' => $row->harga,
                    'diskon_barang' => $row->diskon_barang,
                    'total_item' => $row->total_item
                ];
            }

            $data = array_values($result);

            $namaLokasi = '';
            if ($request->filled('lokasi')) {
                $namaLokasi = Lokasi::find($request->lokasi)->nama;
            }

            $tanggalRequest = $request->daterange;

            return view('components.modal.modal_detail_data_laporan_penjualan', compact('data', 'namaLokasi', 'tanggalRequest'));

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printData(Request $request)
    {

        try {
            $query = DB::table('penjualan as p')
                ->join('penjualan_detail as dp', 'p.id', '=', 'dp.id_penjualan')
                ->join('barang as b', 'b.id', '=', 'dp.id_barang')
                ->select(
                    'p.id',
                    'p.tanggal',
                    'p.no_nota',
                    'dp.id_barang',
                    'b.nama',
                    'dp.jumlah',
                    'dp.harga',
                    'dp.diskon_barang',
                    'dp.merek',
                    'b.kode_barang',
                    DB::raw('((dp.harga - dp.diskon_barang) * dp.jumlah) as total_item')
                );

            if ($request->filled('daterange')) {
                $dates = explode(' - ', $request->daterange);
                $startDate = $dates[0];
                $endDate = $dates[1];
                $query->whereBetween('p.tanggal', [$startDate, $endDate]);
            }

            if ($request->filled('lokasi')) {
                $query->where('p.id_lokasi', $request->lokasi);
            }

            if ($request->filled('merek')) {
                $query->where('dp.merek', $request->merek);
            }

            $data = $query->orderBy('p.tanggal', 'desc')
                        ->orderBy('p.id', 'desc')
                        ->get();

            $result = [];
            foreach ($data as $row) {
                if (!isset($result[$row->id])) {
                    $result[$row->id] = [
                        'id' => $row->id,
                        'tanggal' => $row->tanggal,
                        'no_nota' => $row->no_nota,
                        'total' => 0,
                        'detail' => []
                    ];
                }

                $result[$row->id]['total'] += $row->total_item;

                $result[$row->id]['detail'][] = [
                    'id_barang' => $row->id_barang,
                    'kode_barang' => $row->kode_barang,
                    'nama_barang' => $row->nama,
                    'merek' => $row->merek,
                    'jumlah' => $row->jumlah,
                    'harga' => $row->harga,
                    'diskon_barang' => $row->diskon_barang,
                    'total_item' => $row->total_item
                ];
            }

            $data = array_values($result);

            $namaLokasi = '';
            if ($request->filled('lokasi')) {
                $namaLokasi = Lokasi::find($request->lokasi)->nama;
            }

            $tanggalRequest = $request->daterange;

            $pdf = Pdf::loadView('components.pdf.laporan_pembelian_pdf', compact('data', 'namaLokasi', 'tanggalRequest'));

            return $pdf->stream('Laporan Pembelian -'.$namaLokasi.'.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $fileName = 'Laporan_Penjualan';

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
                new LaporanPenjualanExport($request),
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
