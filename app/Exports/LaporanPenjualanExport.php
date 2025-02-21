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
                    ->where('delete', 0)
                    ->when($this->request->filled('daterange'), function ($query) {
                        $dates = explode(' - ', $this->request->daterange);
                        $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                        $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                        return $query->whereBetween('tanggal', [$startDate, $endDate]);
                    })
                    ->when($this->request->filled('pelanggan') && $this->request->pelanggan != 'all', function ($query) {
                        return $query->where('id_pelanggan', $this->request->pelanggan);
                    })
                    ->when($this->request->filled('lokasi') && $this->request->lokasi != 'all', function ($query) {
                        return $query->where('id_lokasi', $this->request->lokasi);
                    })
                    ->when($this->request->filled('barang') && $this->request->barang != 'all', function ($query) {
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
                $totalAmount = $totalQty = $totalAll = $totalDiskon = $totalBayar = $totalSisa = 0;
                $previousPenjualanId = null;

                foreach ($data as $item) {
                    $firstRow = true;

                    $lastJumlah = 0;

                    foreach ($item->detail as $detail) {
                        $lastJumlah += ($detail->harga * $detail->jumlah);
                    }

                    foreach ($item->detail as $detail) {
                        $pelangganNama = optional($item->pelanggan)->nama ?? 'N/A';
                        $barangKode = optional($detail->barang)->kode_barang ?? 'N/A';

                        $sisa = abs(($detail->harga - $detail->diskon_barang) * $detail->jumlah - $item->bayar);
                        $jumlah = ($detail->harga * $detail->jumlah);
                        $total = ($detail->harga * $detail->jumlah) - $detail->diskon_barang;


                        $notaValue = ($detail->id_penjualan === $previousPenjualanId) ? '' : $item->no_nota;
                        $tanggalValue = ($detail->id_penjualan === $previousPenjualanId) ? '' : $item->tanggal;
                        $pelangganNamaValue = ($detail->id_penjualan === $previousPenjualanId) ? '' : $pelangganNama;

                        $previousPenjualanId = $detail->id_penjualan;

                        if ($notaValue) {
                            $sheet->setCellValue("A$currentRow", $notaValue);
                            $sheet->getStyle("A$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            $sheet->setCellValue("B$currentRow", $tanggalValue);
                            $sheet->getStyle("B$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            $sheet->setCellValue("C$currentRow", $pelangganNamaValue);
                            $sheet->getStyle("C$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            $sheet->setCellValue("J$currentRow", $lastJumlah);
                            $sheet->getStyle("J$currentRow")->getNumberFormat()->setFormatCode('#,##0');

                            $sheet->setCellValue("K$currentRow", $item->diskon_nota);
                            $sheet->getStyle("K$currentRow")->getNumberFormat()->setFormatCode('#,##0');

                            $sheet->setCellValue("L$currentRow", $item->bayar);
                            $sheet->getStyle("L$currentRow")->getNumberFormat()->setFormatCode('#,##0');

                            $sheet->setCellValue("M$currentRow", $sisa);
                            $sheet->getStyle("M$currentRow")->getNumberFormat()->setFormatCode('#,##0');

                            $currentRow++;
                        }

                        $sheet->setCellValue("D$currentRow", $barangKode);
                        $sheet->getStyle("D$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue("E$currentRow", $detail->merek);
                        $sheet->getStyle("E$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue("F$currentRow", $detail->harga);
                        $sheet->getStyle("F$currentRow")->getNumberFormat()->setFormatCode('#,##0');
                        $sheet->setCellValue("G$currentRow", $detail->diskon_barang);
                        $sheet->getStyle("G$currentRow")->getNumberFormat()->setFormatCode('#,##0');
                        $sheet->setCellValue("H$currentRow", $detail->jumlah);
                        $sheet->getStyle("H$currentRow")->getNumberFormat()->setFormatCode('#,##0');
                        $sheet->getStyle("H$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue("I$currentRow", $jumlah);
                        $sheet->getStyle("I$currentRow")->getNumberFormat()->setFormatCode('#,##0');

                        $totalQty += $detail->jumlah;
                        $totalAmount += $jumlah;
                        $totalAll += $total;
                        $totalDiskon += $item->diskon_nota;
                        $totalBayar += $item->bayar;
                        $totalSisa += $sisa;

                        $currentRow++;
                    }
                }

                $sheet->setCellValue("G$currentRow", "TOTAL:");
                $sheet->getStyle("G$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G$currentRow")->getFont()->setBold(true);

                $sheet->setCellValue("H$currentRow", $totalQty);
                $sheet->getStyle("H$currentRow")->getFont()->setBold(true);
                $sheet->getStyle("H$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("I$currentRow", $totalAmount);
                $sheet->getStyle("I$currentRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $sheet->getStyle("I$currentRow")->getFont()->setBold(true);
                $sheet->getStyle("I$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("J$currentRow", $totalAll);
                $sheet->getStyle("J$currentRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $sheet->getStyle("J$currentRow")->getFont()->setBold(true);
                $sheet->getStyle("J$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("K$currentRow", $totalDiskon);
                $sheet->getStyle("K$currentRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $sheet->getStyle("K$currentRow")->getFont()->setBold(true);
                $sheet->getStyle("K$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("L$currentRow", $totalBayar);
                $sheet->getStyle("L$currentRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $sheet->getStyle("L$currentRow")->getFont()->setBold(true);
                $sheet->getStyle("L$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("M$currentRow", $totalSisa);
                $sheet->getStyle("M$currentRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $sheet->getStyle("M$currentRow")->getFont()->setBold(true);
                $sheet->getStyle("M$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);



                $lokasi = 'SEMUA LOKASI';
                if ($this->request->lokasi !== 'all') {
                    $lokasiObj = Lokasi::find($this->request->lokasi);
                    $lokasi = $lokasiObj ? $lokasiObj->nama : 'SEMUA LOKASI';
                }

                $sheet->setCellValue("A5", "DAFTAR PENJUALAN BARANG PADA LOKASI " . $lokasi . " TANGGAL " . $this->request->daterange);

                $cellRange = "A$startRow:M$currentRow";
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
