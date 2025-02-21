<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPembelianExport implements WithEvents
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
                $templatePath = public_path('export_template/template_pembelian.xlsx');
                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();

                $query = DB::table('pembelian as p')
                    ->join('pembelian_detail as dp', 'p.id', '=', 'dp.id_pembelian')
                    ->join('barang as b', 'b.id', '=', 'dp.id_barang')
                    ->select(
                        'p.no_nota',
                        'p.tanggal',
                        'b.kode_barang',
                        'b.nama as nama_barang',
                        'dp.merek',
                        'dp.harga',
                        'dp.jumlah',
                        DB::raw('(dp.jumlah * dp.harga) as total')
                    )
                    ->where('p.delete', 0)
                    ->where('dp.delete', 0);

                if ($this->request->filled('daterange')) {
                    $dates = explode(' - ', $this->request->daterange);
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $query->whereBetween('p.tanggal', [$startDate, $endDate]);
                }

                if ($this->request->filled('lokasi') && $this->request->lokasi != 'all') {
                    $query->where('p.id_lokasi', $this->request->lokasi);
                }

                if ($this->request->filled('merek') && $this->request->merek != 'all') {
                    $query->where('dp.merek', $this->request->merek);
                }

                $data = $query->get();
                $startRow = 6;
                $currentRow = $startRow;
                $totalAmount = 0;
                $totalQty = 0;

                foreach ($data as $key => $item) {
                    // $row = $startRow + $key;
                    $formattedDate = Carbon::parse($item->tanggal)->format('d-m-Y');
                    $sheet->setCellValue("A$currentRow", $item->no_nota);
                    $sheet->setCellValue("B$currentRow", $formattedDate);
                    $sheet->setCellValue("C$currentRow", $item->kode_barang);
                    $sheet->setCellValue("D$currentRow", $item->nama_barang);
                    $sheet->setCellValue("E$currentRow", $item->merek);
                    $sheet->setCellValue("F$currentRow", 'Rp ' . number_format((float) $item->harga, 0, ',', '.'));
                    $sheet->setCellValue("G$currentRow", $item->jumlah);
                    $sheet->setCellValue("H$currentRow",  'Rp ' . number_format((float) $item->total, 0, ',', '.'));

                    $totalAmount += $item->total;
                    $totalQty += $item->jumlah;

                    $currentRow++;
                }

                $totalRow = $startRow + count($data);
                $sheet->setCellValue("F$totalRow", "TOTAL:");
                $sheet->getStyle("F$totalRow")->getFont()->setBold(true);
                $sheet->setCellValue("G$totalRow", $totalQty);
                $sheet->getStyle("G$totalRow")->getFont()->setBold(true);
                $sheet->setCellValue("H$totalRow", 'Rp ' . number_format((float) $totalAmount, 0, ',', '.'));
                $sheet->getStyle("H$totalRow")->getFont()->setBold(true);

                $endRow = $totalRow;
                $cellRange = "A$startRow:H$endRow";

                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ];

                $sheet->getStyle($cellRange)->applyFromArray($borderStyle);

                $exportFileName = 'exports/laporan-pembelian-' . time() . '.xlsx';
                Storage::put($exportFileName, '');

                $filePath = Storage::path($exportFileName);
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($filePath);

                session(['export_file' => $exportFileName]);
            },
        ];
    }
}
