<?php

namespace App\Exports;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Http\Controllers\Transaksi\PengeluaranControler;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Carbon;

class PengeluaranExport implements WithEvents
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
                $templatePath = public_path('export_template/template_pengeluaran.xlsx');
                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();

                $pengeluaranController = new PengeluaranControler();
                $data = $pengeluaranController->getData($this->request);

                $startRow = 6;
                $currentRow = $startRow;
                $totalKeluar = 0;

                $filteredData = array_filter($data, function ($item) {
                    return $item['delete'] == 0;
                });

                usort($filteredData, function ($a, $b) {
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                });
                foreach ($filteredData as $key => $item) {
                    $total_pengeluaran = $item['total'];
                    $formattedDate = Carbon::parse($item['tanggal'])->format('d-m-Y');

                    $sheet->setCellValue("A$currentRow", $item['uraian']);
                    $sheet->setCellValue("B$currentRow", $formattedDate);
                    $sheet->setCellValue("C$currentRow", 'Rp ' . number_format((float) $item['total'], 0, ',', '.'));

                    $sheet
                        ->getStyle("A$currentRow")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet
                        ->getStyle("B$currentRow")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet
                        ->getStyle("C$currentRow")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $totalKeluar += $total_pengeluaran;
                    $currentRow++;
                }

                $totalRow = $startRow + count($data);

                $tanggal = $this->request->daterange;
                $sheet->setCellValue('A4', "LAPORAN PENGELUARAN MULAI TANGGAL $tanggal");
                $sheet->getStyle('A4')->getFont()->setBold(true);

                $cellRange = "A$startRow:C$currentRow";
                $sheet->setCellValue("B$totalRow", 'TOTAL PENGELUARAN:');
                $sheet
                    ->getStyle("B$currentRow")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue("C$totalRow", 'Rp ' . number_format((float) $totalKeluar, 0, ',', '.'));
                $sheet
                    ->getStyle("C$currentRow")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ];
                $sheet->getStyle($cellRange)->applyFromArray($borderStyle);

                $exportFileName = 'exports/pengeluaran-' . time() . '.xlsx';
                Storage::put($exportFileName, '');

                $filePath = Storage::path($exportFileName);
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($filePath);

                session(['export_file' => $exportFileName]);
            },
        ];
    }
}
