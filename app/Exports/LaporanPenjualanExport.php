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

                    foreach ($item->detail as $detail) {
                        $pelangganNama = optional($item->pelanggan)->nama ?? 'N/A';
                        $barangKode = optional($detail->barang)->kode_barang ?? 'N/A';

                        $sisa = ($detail->harga - $detail->diskon_barang) * $detail->jumlah - $item->bayar;
                        $jumlah = ($detail->harga * $detail->jumlah);
                        $total = ($detail->harga * $detail->jumlah) - $detail->diskon_barang;

                        $notaValue = ($detail->id_penjualan === $previousPenjualanId) ? '' : $item->no_nota;
                        $tanggalValue = ($detail->id_penjualan === $previousPenjualanId) ? '' : $item->tanggal;
                        $pelangganNamaValue = ($detail->id_penjualan === $previousPenjualanId) ? '' : $pelangganNama;

                        $previousPenjualanId = $detail->id_penjualan;

                        if ($notaValue) {
                            $sheet->setCellValue("A$currentRow", $notaValue);
                            $sheet->setCellValue("B$currentRow", $tanggalValue);
                            $sheet->setCellValue("C$currentRow", $pelangganNamaValue);
                            $sheet->mergeCells("D$currentRow:M$currentRow");
                            $currentRow++;
                        }

                        $sheet->setCellValue("D$currentRow", $barangKode);
                        $sheet->setCellValue("E$currentRow", $detail->merek);
                        $sheet->setCellValue("F$currentRow", $detail->harga);
                        $sheet->setCellValue("G$currentRow", $detail->diskon_barang);
                        $sheet->setCellValue("H$currentRow", $detail->jumlah);
                        $sheet->setCellValue("I$currentRow", $jumlah);
                        $sheet->setCellValue("J$currentRow", $total);
                        $sheet->setCellValue("K$currentRow", $item->diskon_nota);
                        $sheet->setCellValue("L$currentRow", $item->bayar);
                        $sheet->setCellValue("M$currentRow", $sisa);

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
                $sheet->getStyle("G$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("H$currentRow", $totalQty);
                $sheet->getStyle("H$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("I$currentRow", "Rp" . $totalAmount);
                $sheet->getStyle("I$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("J$currentRow", "Rp" . $totalAll);
                $sheet->getStyle("J$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("K$currentRow", "Rp" . $totalDiskon);
                $sheet->getStyle("K$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("L$currentRow", "Rp" . $totalBayar);
                $sheet->getStyle("L$currentRow")->getFont()->setBold(true);
                $sheet->setCellValue("M$currentRow", "Rp" . $totalSisa);
                $sheet->getStyle("M$currentRow")->getFont()->setBold(true);

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
