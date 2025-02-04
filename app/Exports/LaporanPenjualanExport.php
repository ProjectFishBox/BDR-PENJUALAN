<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Storage;
use App\Models\Penjualan;

class LaporanPenjualanExport implements WithEvents
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
                $templatePath = public_path('export_template/template_penjualan.xlsx');
                if (!file_exists($templatePath)) {
                    throw new \Exception("Template Excel tidak ditemukan di path: $templatePath");
                }

                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();

                $query = Penjualan::with(['detail.barang', 'pelanggan', 'lokasi'])
                    ->when($this->request->filled('daterange'), function ($query) {
                        $dates = explode(' - ', $this->request->daterange);
                        return $query->whereBetween('tanggal', [trim($dates[0]), trim($dates[1])]);
                    })
                    ->when($this->request->filled('pelanggan'), function ($query) {
                        return $query->where('id_pelanggan', $this->request->pelanggan);
                    })
                    ->when($this->request->filled('lokasi'), function ($query) {
                        return $query->where('id_lokasi', $this->request->lokasi);
                    })
                    ->when($this->request->filled('barang'), function ($query) {
                        return $query->whereHas('detail', function ($q) {
                            $q->where('id_barang', $this->request->barang);
                        });
                    })
                    ->when($this->request->filled('no_nota'), function ($query) {
                        return $query->where('no_nota', $this->request->no_nota);
                    });

                $data = $query->get();

                $startRow = 6;
                $currentRow = $startRow;
                $totalAmount = 0;
                $totalQty = 0;

                foreach ($data as $item) {
                    foreach ($item->detail as $detail) {
                        $pelangganNama = optional($item->pelanggan)->nama ?? 'N/A';
                        $barangKode = optional($detail->barang)->kode_barang ?? 'N/A';

                        $sisa = ($detail->harga - $detail->diskon_barang) * $detail->jumlah - $item->bayar;
                        $total = ($detail->harga * $detail->jumlah);

                        $sheet->setCellValue("A$currentRow", $item->tanggal);
                        $sheet->setCellValue("B$currentRow", $pelangganNama);
                        $sheet->setCellValue("C$currentRow", $barangKode);
                        $sheet->setCellValue("D$currentRow", $detail->merek);
                        $sheet->setCellValue("E$currentRow", $detail->harga);
                        $sheet->setCellValue("F$currentRow", $detail->diskon_barang);
                        $sheet->setCellValue("G$currentRow", $detail->jumlah);
                        $sheet->setCellValue("H$currentRow", $detail->jumlah);
                        $sheet->setCellValue("I$currentRow", $total);
                        $sheet->setCellValue("J$currentRow", $item->diskon_nota);
                        $sheet->setCellValue("K$currentRow", $item->bayar);
                        $sheet->setCellValue("L$currentRow", $sisa);


                        $totalAmount += $sisa;
                        $totalQty += $detail->jumlah;

                        $currentRow++;
                    }
                }

                $sheet->setCellValue("F$currentRow", "TOTAL:");
                $sheet->getStyle("F$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("G$currentRow", $totalQty);
                $sheet->getStyle("G$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("H$currentRow", $totalAmount);
                $sheet->getStyle("H$currentRow")->getFont()->setBold(true);

                $cellRange = "A$startRow:K$currentRow";
                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ];
                $sheet->getStyle($cellRange)->applyFromArray($borderStyle);

                $exportFileName = 'exports/laporan-penjualan-' . time() . '.xlsx';
                $filePath = Storage::path($exportFileName);

                Storage::makeDirectory('exports');

                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($filePath);

                session(['export_file' => $exportFileName]);
            },
        ];
    }
}
