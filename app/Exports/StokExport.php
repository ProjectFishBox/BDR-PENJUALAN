<?php

namespace App\Exports;

use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StokExport implements WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $templatePath = public_path('export_template/template_stok.xlsx');
                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();


                $barangMasuk = PembelianDetail::query()
                    ->select([
                        'pembelian_detail.id_barang',
                        'pembelian_detail.nama_barang',
                        'pembelian_detail.merek',
                        'barang.kode_barang',
                        'pembelian.id_lokasi',
                        'lokasi.nama'
                    ])
                    ->join('barang', 'pembelian_detail.id_barang', '=', 'barang.id')
                    ->join('pembelian', 'pembelian_detail.id_pembelian', '=', 'pembelian.id')
                    ->join('lokasi', 'pembelian.id_lokasi', '=', 'lokasi.id')
                    ->where('pembelian_detail.delete', 0)
                    ->when($this->request->filled('lokasi'), fn($query) => $query->where('pembelian.id_lokasi', $this->request->lokasi))
                    ->when($this->request->filled('barang'), fn($query) => $query->where('pembelian_detail.id_barang', $this->request->barang))
                    ->when($this->request->filled('merek'), fn($query) => $query->where('pembelian_detail.merek', $this->request->merek))
                    ->selectRaw('SUM(pembelian_detail.jumlah) as total_masuk')
                    ->groupBy(['pembelian_detail.id_barang', 'pembelian_detail.nama_barang', 'pembelian_detail.merek', 'barang.kode_barang', 'pembelian.id_lokasi', 'lokasi.nama'])
                    ->get()
                    ->keyBy('id_barang');

                $barangKeluar = PenjualanDetail::query()
                    ->select([
                        'penjualan_detail.id_barang',
                        'penjualan_detail.nama_barang',
                        'penjualan_detail.merek',
                        'barang.kode_barang',
                        'penjualan.id_lokasi',
                        'lokasi.nama'
                    ])
                    ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
                    ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id')
                    ->join('lokasi', 'penjualan.id_lokasi', '=', 'lokasi.id')
                    ->where('penjualan_detail.delete', 0)
                    ->when($this->request->filled('lokasi'), fn($query) => $query->where('penjualan.id_lokasi', $this->request->lokasi))
                    ->when($this->request->filled('barang'), fn($query) => $query->where('penjualan_detail.id_barang', $this->request->barang))
                    ->when($this->request->filled('merek'), fn($query) => $query->where('penjualan_detail.merek', $this->request->merek))
                    ->selectRaw('SUM(penjualan_detail.jumlah) as total_terjual')
                    ->groupBy(['penjualan_detail.id_barang', 'penjualan_detail.nama_barang', 'penjualan_detail.merek', 'barang.kode_barang', 'penjualan.id_lokasi', 'lokasi.nama'])
                    ->get()
                    ->keyBy('id_barang');

                $startRow = 6;
                $totalMasuk = 0;
                $totalKeluar = 0;
                $allBarang = $barangMasuk->merge($barangKeluar);

                foreach ($allBarang as $key => $item) {
                    $row = $startRow + $key;
                    $total_masuk = $barangMasuk[$item->id_barang]->total_masuk ?? 0;
                    $total_terjual = $barangKeluar[$item->id_barang]->total_terjual ?? 0;
                    $total_stok = $total_masuk - $total_terjual;

                    $sheet->setCellValue("A$row", $item->kode_barang);
                    $sheet->setCellValue("B$row", $item->nama_barang);
                    $sheet->setCellValue("C$row", $item->merek);
                    $sheet->setCellValue("D$row", $total_masuk);
                    $sheet->setCellValue("E$row", $total_terjual);
                    $sheet->setCellValue("E$row", $total_terjual);
                    $sheet->setCellValue("F$row", $total_stok);



                    $totalMasuk += $total_masuk;
                    $totalKeluar += $total_terjual;
                }

                $totalRow = $startRow + count($allBarang);
                $sheet->setCellValue("C$totalRow", "TOTAL MASUK:");
                $sheet->setCellValue("D$totalRow", $totalMasuk);
                $sheet->setCellValue("E$totalRow", $totalKeluar);
                $sheet->setCellValue("F$totalRow", $totalMasuk - $totalKeluar);


                $exportFileName = 'exports/laporan-stok-' . time() . '.xlsx';
                Storage::put($exportFileName, '');

                $filePath = Storage::path($exportFileName);
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($filePath);

                session(['export_file' => $exportFileName]);
            },
        ];
    }
}
