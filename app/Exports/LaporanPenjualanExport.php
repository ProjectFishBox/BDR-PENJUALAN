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
use App\Models\Lokasi;

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
                    ->when($this->request->filled('pelanggan') && $this->request->pelanggan != 'all', function ($query) {
                        return $query->where('id_pelanggan', $this->request->pelanggan);
                    })
                    ->when($this->request->filled('lokasi') && $this->request->lokasi != 'all', function ($query) {
                        return $query->where('id_lokasi', $this->request->lokasi);
                    })
                    ->when($this->request->filled('barang') && $this->request->barang != 'all' , function ($query) {
                        return $query->whereHas('detail', function ($q) {
                            $q->where('id_barang', $this->request->barang);
                        });
                    })
                    ->when($this->request->filled('no_nota') && $this->request->no_nota != 'all', function ($query) {
                        return $query->where('no_nota', $this->request->no_nota);
                    });

                $data = $query->get();

                $startRow = 8;
                $currentRow = $startRow;
                $totalAmount = 0;
                $totalQty = 0;
                $totalAll = 0;
                $totalDiskon = 0;
                $totalBayar = 0;
                $totalSisa = 0;

                foreach ($data as $item) {

                    // dd($item);

                    foreach ($item->detail as $detail) {
                        $pelangganNama = optional($item->pelanggan)->nama ?? 'N/A';
                        $barangKode = optional($detail->barang)->kode_barang ?? 'N/A';

                        $sisa = ($detail->harga - $detail->diskon_barang) * $detail->jumlah - $item->bayar;
                        $jumlah = ($detail->harga * $detail->jumlah);
                        $total = ($detail->harga * $detail->jumlah) - $detail->diskon_barang;

                        $sheet->setCellValue("A$currentRow", $item->tanggal);
                        $sheet->setCellValue("B$currentRow", $pelangganNama);
                        $sheet->setCellValue("C$currentRow", $barangKode);
                        $sheet->setCellValue("D$currentRow", $detail->merek);
                        $sheet->setCellValue("E$currentRow", $detail->harga);
                        $sheet->setCellValue("F$currentRow", $detail->diskon_barang);
                        $sheet->setCellValue("G$currentRow", $detail->jumlah);
                        $sheet->setCellValue("H$currentRow", $jumlah);
                        $sheet->setCellValue("I$currentRow", $total);
                        $sheet->setCellValue("J$currentRow", $item->diskon_nota);
                        $sheet->setCellValue("K$currentRow", $item->bayar);
                        $sheet->setCellValue("L$currentRow", $sisa);

                        $totalQty += $detail->jumlah;
                        $totalAmount += $jumlah;
                        $totalAll +=$total;
                        $totalDiskon +=$item->diskon_nota;
                        $totalBayar += $item->bayar;
                        $totalSisa += $sisa;

                        $currentRow++;
                    }
                }

                $sheet->setCellValue("F$currentRow", "TOTAL:");
                $sheet->getStyle("F$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("G$currentRow", $totalQty);
                $sheet->getStyle("G$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("H$currentRow", "Rp" . $totalAmount);
                $sheet->getStyle("H$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("I$currentRow", "Rp" . $totalAll);
                $sheet->getStyle("I$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("J$currentRow", "Rp" . $totalDiskon);
                $sheet->getStyle("J$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("K$currentRow", "Rp" . $totalBayar);
                $sheet->getStyle("K$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("L$currentRow", "Rp" . $totalSisa);
                $sheet->getStyle("L$currentRow")->getFont()->setBold(true);

                $lokasi = 'SEMUA LOKASI';
                if ($this->request->lokasi !== 'all') {
                    $lokasi = Lokasi::find($this->request->lokasi)->nama;
                }

                $sheet->setCellValue("A5", "DAFTAR PENJUALAN BARANG PADA LOKASI " . $lokasi . " TANGGAL " . $this->request->daterange);

                $cellRange = "A$startRow:L$currentRow";
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
