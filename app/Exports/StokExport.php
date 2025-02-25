<?php

namespace App\Exports;

use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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

                $startRow = 7;
                $totalMasuk = 0;
                $totalKeluar = 0;

                $nama_lokasi = "SEMUA LOKASI";
                if (!empty($data) && $this->request->filled('lokasi') && $this->request->lokasi !== 'all') {
                    $nama_lokasi = $data[0]['nama_lokasi'] ?? "SEMUA LOKASI";
                }

                $sheet->setCellValue("A5", "LAPORAN STOK BARANG PADA  $nama_lokasi");
                $sheet->getStyle("A5")->getFont()->setBold(true);

                foreach ($data as $key => $item) {
                    $row = $startRow + $key;
                    $total_masuk = $item['total_masuk'];
                    $total_terjual = $item['total_terjual'];
                    $total_stok = $item['stok_akhir'];

                    $sheet->setCellValue("A$row", $key + 1);
                    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->setCellValue("B$row", $item['kode_barang']);
                    $sheet->getStyle("B$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->setCellValue("C$row", $item['nama_barang']);
                    $sheet->getStyle("C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->setCellValue("D$row", $item['merek']);
                    $sheet->getStyle("D$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->setCellValue("E$row", $total_masuk);
                    $sheet->getStyle("E$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->setCellValue("F$row", $total_terjual);
                    $sheet->getStyle("F$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->setCellValue("G$row", $total_stok);
                    $sheet->getStyle("G$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                    $totalMasuk += $total_masuk;
                    $totalKeluar += $total_terjual;
                }

                $totalRow = $startRow + count($data);


                $sheet->mergeCells("A$totalRow:D$totalRow");
                $sheet->setCellValue("A$totalRow", "TOTAL MASUK:");
                $sheet->getStyle("A$totalRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Rata kanan


                $sheet->setCellValue("E$totalRow", $totalMasuk);
                $sheet->getStyle("E$totalRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("F$totalRow", $totalKeluar);
                $sheet->getStyle("F$totalRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("G$totalRow", $totalMasuk - $totalKeluar);
                $sheet->getStyle("G$totalRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                $cellRange = "A$startRow:G$totalRow";
                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ];
                $sheet->getStyle($cellRange)->applyFromArray($borderStyle);

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
