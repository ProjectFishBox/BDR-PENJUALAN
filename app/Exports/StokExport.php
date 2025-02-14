<?php

namespace App\Exports;

use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Laporan\StokControllers;

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

                $stokController = new StokControllers();
                $data = $stokController->getData($this->request);

                $startRow = 6;
                $totalMasuk = 0;
                $totalKeluar = 0;

                foreach ($data as $key => $item) {
                    $row = $startRow + $key;
                    $total_masuk = $item['total_masuk'];
                    $total_terjual = $item['total_terjual'];
                    $total_stok = $item['stok_akhir'];

                    $sheet->setCellValue("A$row", $item['kode_barang']);
                    $sheet->setCellValue("B$row", $item['nama_barang']);
                    $sheet->setCellValue("C$row", $item['merek']);
                    $sheet->setCellValue("D$row", $total_masuk);
                    $sheet->setCellValue("E$row", $total_terjual);
                    $sheet->setCellValue("F$row", $total_stok);

                    $totalMasuk += $total_masuk;
                    $totalKeluar += $total_terjual;
                }

                $totalRow = $startRow + count($data);
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
