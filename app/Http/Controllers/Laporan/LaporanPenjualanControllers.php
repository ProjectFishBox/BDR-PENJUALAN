<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\LaporanPenjualanExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;


use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Pelanggan;
use App\Models\Penjualan;

class LaporanPenjualanControllers extends Controller
{

    public function index(Request $request)
    {
        $title = 'Laporan Penjualan';

        $pelanggan = Pelanggan::all();
        $lokasi = Lokasi::where('delete', 0)->get();
        $barang = Barang::all();
        $noNota = Penjualan::select('no_nota')->distinct()->get();


        if ($request->ajax()) {
            $lokasiId = $request->lokasi;
            $pelangganId = $request->pelanggan;
            $barangId = $request->barang;
            $notaId = $request->no_nota;

            $query = Penjualan::with(['detail.barang', 'pelanggan'])
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->when($request->input('pelanggan') && $pelangganId !== 'all', function ($query) use ($pelangganId) {
                    return $query->where('id_pelanggan', $pelangganId);
                })
                ->when($request->input('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('id_lokasi', $lokasiId);
                })
                ->when($request->input('barang') && $barangId !== 'all', function ($query) use ($barangId) {
                    return $query->whereHas('detail', function ($q) use ($barangId) {
                        $q->where('id_barang', $barangId);
                    });
                })
                ->when($request->input('no_nota') && $notaId !== 'all', function ($query) use ($notaId) {
                    return $query->where('no_nota', $notaId);
                });

            $data = $query->get()->map(function ($item) {

                // dd($item);

                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal,
                    'no_nota' => $item->no_nota,
                    'nama_pelanggan' => $item->pelanggan->nama,
                    'diskon_nota' => $item->diskon_nota,
                    'bayar' => $item->bayar,
                    'detail' => $item->detail->map(function ($detail) {
                        return [
                            'id_barang' => $detail->id_barang,
                            'kode_barang' => $detail->barang->kode_barang,
                            'nama_barang' => $detail->nama_barang,
                            'merek' => $detail->merek,
                            'harga' => $detail->harga,
                            'diskon_barang' => $detail->diskon_barang,
                            'jumlah' => $detail->jumlah,
                        ];
                    }),
                ];
            });

            return response()->json($data);
        }

        return view('pages.laporan.laporan_penjualan.laporan_penjualan', compact('title', 'barang', 'lokasi', 'pelanggan', 'noNota'));
    }

    public function printData(Request $request)
    {
        try {

            $lokasiId = $request->lokasi;
            $pelangganId = $request->pelanggan;
            $barangId = $request->barang;
            $notaId = $request->no_nota;

            $query = Penjualan::with(['detail.barang', 'pelanggan', 'lokasi'])
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    return $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->when($request->filled('pelanggan') && $pelangganId !== 'all', function ($query) use ($pelangganId) {
                    return $query->where('id_pelanggan', $pelangganId);
                })
                ->when($request->filled('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('id_lokasi', $lokasiId);
                })
                ->when($request->filled('barang') && $barangId !== 'all', function ($query) use ($barangId) {
                    return $query->whereHas('detail', function ($q) use ($barangId) {
                        $q->where('id_barang', $barangId);
                    });
                })
                ->when($request->filled('no_nota') && $notaId !== 'all', function ($query) use ($notaId) {
                    return $query->where('no_nota', $notaId);
                });

            $data = $query->get()->map(function ($item) {
                return [
                    'tanggal' => $item->tanggal,
                    'no_nota' => $item->no_nota,
                    'nama_pelanggan' => $item->pelanggan->nama,
                    'diskon_nota' => $item->diskon_nota,
                    'bayar' => $item->bayar,
                    'nama_lokasi' => $item->lokasi ? $item->lokasi->nama : 'SEMUA LOKASI',
                    'detail' => $item->detail->map(function ($detail) {
                        return [
                            'kode_barang' => $detail->barang->kode_barang,
                            'nama_barang' => $detail->nama_barang,
                            'merek' => $detail->merek,
                            'harga' => $detail->harga,
                            'diskon_barang' => $detail->diskon_barang,
                            'jumlah' => $detail->jumlah,
                        ];
                    }),
                ];
            });

            $tanggal = $request->daterange;
            $lokasi = 'SEMUA LOKASI';
            if ($lokasiId !== 'all') {
                $lokasiObj = Lokasi::find($lokasiId);
                $lokasi = $lokasiObj ? $lokasiObj->nama : 'SEMUA LOKASI';
            }

            $totalPenjualan = $data->sum(function ($item) {
                return $item['detail']->sum(function ($detail) {
                    return $detail['harga'] * $detail['jumlah'];
                });
            });


            $totalJumlah = $data->sum(function ($item) {
                return $item['detail']->sum(function ($detail) {
                    return ($detail['harga'] * $detail['jumlah']) - $detail['diskon_barang'];
                });
            });

            $totalDiskonBarang = $data->sum(function ($item) {
                return $item['detail']->sum('diskon_barang');
            });

            $totalBayar = $data->sum(function ($item) {
                return $item['bayar'];
            });


            $totalHitung = $data->sum(function ($item) {
                return $item['detail']->sum('jumlah');
            });

            $totalDiskon = $data->sum('diskon_nota');

            $pdf = Pdf::loadView('components.pdf.laporan_penjualan_pdf', compact('data','lokasi','totalDiskonBarang','totalBayar','totalHitung','totalPenjualan', 'totalDiskon', 'totalJumlah', 'tanggal'))
                ->setPaper('a4', 'landscape');

            return $pdf->stream('Laporan_Penjualan.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        Excel::store(new LaporanPenjualanExport($request), 'temp.xlsx');

        try {

            $filePath = session('export_file');

            if (!$filePath || !Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengexport data. File tidak ditemukan.'
                ]);
            }

            $fileName = 'Laporan_Penjualan';

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
