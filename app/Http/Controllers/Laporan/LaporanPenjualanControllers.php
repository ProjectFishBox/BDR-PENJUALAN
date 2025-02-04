<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\LaporanPenjualanExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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
        $lokasi = Lokasi::all();
        $barang = Barang::all();
        $noNota = Penjualan::select('no_nota')->distinct()->get();


        if ($request->ajax()) {
            $query = Penjualan::with(['detail.barang', 'pelanggan'])
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    return $query->whereBetween('tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->when($request->input('pelanggan'), function ($query) use ($request) {
                    return $query->where('id_pelanggan', $request->input('pelanggan'));
                })
                ->when($request->input('lokasi'), function ($query) use ($request) {
                    return $query->where('id_lokasi', $request->input('lokasi'));
                })
                ->when($request->input('barang'), function ($query) use ($request) {
                    return $query->whereHas('detail', function ($q) use ($request) {
                        $q->where('id_barang', $request->input('barang'));
                    });
                })
                ->when($request->input('no_nota'), function ($query) use ($request) {
                    return $query->where('no_nota', $request->input('no_nota'));
                });

            $data = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal,
                    'no_nota' => $item->no_nota,
                    'nama_pelanggan' => $item->pelanggan->nama,
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
            $query = Penjualan::with(['detail.barang', 'pelanggan', 'lokasi'])
                ->when($request->filled('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->daterange);
                    return $query->whereBetween('tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->when($request->filled('pelanggan'), function ($query) use ($request) {
                    return $query->where('id_pelanggan', $request->pelanggan);
                })
                ->when($request->filled('lokasi'), function ($query) use ($request) {
                    return $query->where('id_lokasi', $request->lokasi);
                })
                ->when($request->filled('barang'), function ($query) use ($request) {
                    return $query->whereHas('detail', function ($q) use ($request) {
                        $q->where('id_barang', $request->barang);
                    });
                })
                ->when($request->filled('no_nota'), function ($query) use ($request) {
                    return $query->where('no_nota', $request->no_nota);
                });

            $data = $query->get()->map(function ($item) {
                return [
                    'tanggal' => $item->tanggal,
                    'no_nota' => $item->no_nota,
                    'nama_pelanggan' => $item->pelanggan->nama,
                    'diskon_nota' => $item->diskon_nota,
                    'bayar' => $item->bayar,
                    'nama_lokasi' => $item->lokasi->nama,
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


            $totalHitung = $data->sum(function ($item) {
                return $item['detail']->sum('jumlah');
            });

            $totalDiskon = $data->sum('diskon_nota');

            $pdf = Pdf::loadView('components.pdf.laporan_penjualan_pdf', compact('data', 'totalPenjualan', 'totalDiskon', 'totalJumlah', 'tanggal'))
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
        try {
            $fileName = 'Laporan_Pembelian';

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

            $query = Penjualan::with(['detail.barang', 'pelanggan', 'lokasi'])
                ->when($request->filled('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->daterange);
                    return $query->whereBetween('tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->when($request->filled('pelanggan'), function ($query) use ($request) {
                    return $query->where('id_pelanggan', $request->pelanggan);
                })
                ->when($request->filled('lokasi'), function ($query) use ($request) {
                    return $query->where('id_lokasi', $request->lokasi);
                })
                ->when($request->filled('barang'), function ($query) use ($request) {
                    return $query->whereHas('detail', function ($q) use ($request) {
                        $q->where('id_barang', $request->barang);
                    });
                })
                ->when($request->filled('no_nota'), function ($query) use ($request) {
                    return $query->where('no_nota', $request->no_nota);
                });

            $data = $query->get()->map(function ($item) {
                return [
                    'tanggal' => $item->tanggal,
                    'no_nota' => $item->no_nota,
                    'nama_pelanggan' => $item->pelanggan->nama,
                    'diskon_nota' => $item->diskon_nota,
                    'bayar' => $item->bayar,
                    'nama_lokasi' => $item->lokasi->nama,
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

            return Excel::download(
                new LaporanPenjualanExport($data),
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
