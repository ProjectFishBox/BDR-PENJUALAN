<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

use App\Models\Barang;
use App\Models\Lokasi;
use App\Exports\LaporanPembelianExport;
use App\Models\Pembelian;

class LaporanPembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $title = 'Laporan Pembelian';
        $barang = Barang::all();
        $lokasi = Lokasi::where('delete', 0)->get();

        if ($request->ajax()) {
            $lokasiId = $request->lokasi;
            $merekId = $request->merek;

            $query = Pembelian::with(['detail' => function ($query) use ($merekId) {
                if ($merekId !== 'all') {
                    $query->where('merek', $merekId);
                }
            }, 'detail.barang'])
                ->when($request->input('daterange'), function ($query) use ($request) {
                    $dates = explode(' - ', $request->input('daterange'));
                    return $query->whereBetween('tanggal', [trim($dates[0]), trim($dates[1])]);
                })
                ->when($request->input('lokasi') && $lokasiId !== 'all', function ($query) use ($lokasiId) {
                    return $query->where('id_lokasi', $lokasiId);
                });

            $data = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal,
                    'no_nota' => $item->no_nota,
                    'detail' => $item->detail->map(function ($detail) {
                        return [
                            'id_barang' => $detail->id_barang,
                            'kode_barang' => $detail->barang->kode_barang,
                            'nama_barang' => $detail->nama_barang,
                            'merek' => $detail->merek,
                            'harga' => $detail->harga,
                            'jumlah' => $detail->jumlah,
                        ];
                    }),
                ];
            });

            return response()->json($data);
        }

        return view('pages.laporan.laporan_pembelian.laporan_pembelian', compact('title', 'barang', 'lokasi'));
    }


    public function printData(Request $request)
    {

        try {
            $query = DB::table('pembelian as p')
                ->join('pembelian_detail as dp', 'p.id', '=', 'dp.id_pembelian')
                ->join('barang as b', 'b.id', '=', 'dp.id_barang')
                ->select(
                    'p.id',
                    'p.tanggal',
                    'p.no_nota',
                    'dp.id_barang',
                    'b.nama',
                    'dp.jumlah',
                    'dp.harga',
                    'dp.merek',
                    'b.kode_barang',
                );

            if ($request->filled('daterange')) {
                $dates = explode(' - ', $request->daterange);
                $startDate = $dates[0];
                $endDate = $dates[1];
                $query->whereBetween('p.tanggal', [$startDate, $endDate]);
            }

            if ($request->filled('lokasi') && $request->lokasi != 'all') {
                $query->where('p.id_lokasi', $request->lokasi);
            }

            if ($request->filled('merek') && $request->merek != 'all') {
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
                        'detail' => []
                    ];
                }

                $subtotal = $row->jumlah * $row->harga;

                $result[$row->id]['detail'][] = [
                    'id_barang' => $row->id_barang,
                    'kode_barang' => $row->kode_barang,
                    'nama_barang' => $row->nama,
                    'merek' => $row->merek,
                    'jumlah' => $row->jumlah,
                    'harga' => $row->harga,
                    'total_item' => $subtotal
                ];
            }

            $data = array_values($result);

            $namaLokasi = '';
            if ($request->filled('lokasi')) {
                $lokasi = Lokasi::find($request->lokasi);
                if ($lokasi) {
                    $namaLokasi = $lokasi->nama;
                }
            }

            $tanggalRequest = $request->daterange;

            $pdf = Pdf::loadView('components.pdf.laporan_pembelian_pdf', compact('data', 'namaLokasi', 'tanggalRequest'))
            ->setPaper('a4', 'landscape');;

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
        Excel::store(new LaporanPembelianExport($request), 'temp.xlsx');
        try {

            $filePath = session('export_file');

            if (!$filePath || !Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengexport data. File tidak ditemukan.'
                ]);
            }

            $fileName = 'Laporan_Pembelian';

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
