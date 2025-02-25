<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\DB;

use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;
USE App\Models\Lokasi;
use App\Models\Penjualan;

class PendapatanExport implements WithEvents
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
                $templatePath = public_path('export_template/template_pendapatan.xlsx');
                if (!file_exists($templatePath)) {
                    throw new \Exception("Template Excel tidak ditemukan di path: $templatePath");
                }

                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();

                $penjualan = PenjualanDetail::query()
                    ->select([
                        'penjualan_detail.id_barang',
                        'penjualan_detail.nama_barang',
                        'penjualan_detail.merek',
                        'penjualan_detail.harga',
                        'penjualan_detail.diskon_barang',
                        'penjualan_detail.jumlah',
                        'penjualan_detail.id_penjualan',
                        'barang.kode_barang as kode_barang',
                        'penjualan.id_lokasi',
                        'penjualan.tanggal',
                        'penjualan.diskon_nota',
                        'lokasi.nama',
                        DB::raw('(SELECT pembelian_detail.harga
                                FROM pembelian_detail
                                JOIN pembelian ON pembelian_detail.id_pembelian = pembelian.id
                                WHERE pembelian.tanggal = penjualan.tanggal
                                AND pembelian_detail.id_barang = penjualan_detail.id_barang
                                ORDER BY pembelian.id DESC
                                LIMIT 1) as harga_pembelian'),
                        DB::raw('(SELECT SUM(pengeluaran.total)
                                FROM pengeluaran
                                WHERE pengeluaran.tanggal = penjualan.tanggal) as total_pengeluaran')
                    ])
                    ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
                    ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id')
                    ->join('lokasi', 'penjualan.id_lokasi', '=', 'lokasi.id')
                    ->where('penjualan_detail.delete', 0)
                    ->where('penjualan.delete', 0)
                    ->when($this->request->filled('lokasi') && $this->request->lokasi != 'all', fn($query) => $query->where('penjualan.id_lokasi', $this->request->lokasi))
                    ->when($this->request->filled('daterange'), function ($query) {
                        $dates = explode(' - ', $this->request->daterange);
                        $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                        $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                        return $query->whereBetween('penjualan.tanggal', [$startDate, $endDate]);
                    })
                    ->get()
                    ->groupBy(function ($item) {
                        return $item->tanggal . '-' . $item->id_barang . '-' . $item->kode_barang . '-' . $item->merek . '-' . $item->harga;
                    })
                    ->map(function ($group) {
                        $firstItem = $group->first();

                        $totalPenjualanItem = $group->sum('jumlah');
                        $totalPembelianItem = $firstItem->harga_pembelian ?? DB::table('barang')->where('id', $firstItem->id_barang)->value('harga');

                        return [
                            'id_penjualan' => $firstItem->id_penjualan,
                            'id_barang' => $firstItem->id_barang,
                            'nama_barang' => $firstItem->nama_barang,
                            'merek' => $firstItem->merek,
                            'harga' => $firstItem->harga,
                            'diskon_barang' => $firstItem->diskon_barang,
                            'kode_barang' => $firstItem->kode_barang,
                            'tanggal' => $firstItem->tanggal,
                            'total_jual' => $group->sum(function ($item) {
                                return $item->harga * $item->jumlah;
                            }),
                            'total_diskon_barang' => $group->sum(function ($item) {
                                return $item->diskon_barang * $item->jumlah;
                            }),
                            'total_jumlah' => $group->sum('jumlah'),
                            'diskon_nota' => $firstItem->diskon_nota,
                            'harga_pembelian' => $firstItem->harga_pembelian ?? DB::table('barang')->where('id', $firstItem->id_barang)->value('harga'),
                            'total_pengeluaran' => $firstItem->total_pengeluaran ?? 0,
                            'modal_usaha' => $totalPenjualanItem * $totalPembelianItem,
                        ];
                    })
                    ->values();

                $totalPenjualan = $penjualan->sum('total_jumlah');
                $totalTerjual = $penjualan->sum('total_jual');
                $totalDiskonProduk = $penjualan->sum('total_diskon_barang');
                $totalPembelian = $penjualan->sum('harga_pembelian');
                // $totalPengeluaran = $penjualan->sum('total_pengeluaran');
                $modalUsaha = $penjualan->sum('modal_usaha');

                $uniqueDiskonNota = [];
                $uniquePengeluaran = [];


                foreach ($penjualan as $item) {
                    if (!isset($uniqueDiskonNota[$item['id_penjualan']])) {
                        $uniqueDiskonNota[$item['id_penjualan']] = $item['diskon_nota'];
                    }

                    if (!isset($uniquePengeluaran[$item['tanggal']])) {
                        $uniquePengeluaran[$item['tanggal']] = $item['total_pengeluaran'];
                    }

                }
                $totalDiskonNota = array_sum($uniqueDiskonNota);
                $totalPengeluaran = array_sum($uniquePengeluaran);

                $totalTransfer = ($totalTerjual - ($totalPengeluaran + $totalDiskonNota + $totalDiskonProduk));
                $labaBersih = $totalTransfer - $modalUsaha;

                $startRow = 6;
                $currentRow = $startRow;

                foreach ($penjualan as $key => $item) {
                    $sheet->setCellValue("B$currentRow", $key + 1);
                    $sheet->setCellValue("C$currentRow", $item['kode_barang']);
                    $sheet->setCellValue("D$currentRow", $item['nama_barang']);

                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\Carbon\Carbon::createFromFormat('Y-m-d', $item['tanggal']));
                    $sheet->setCellValue("E$currentRow", $date);
                    $sheet->getStyle("E$currentRow")->getNumberFormat()->setFormatCode('DD-MM-YYYY');

                    $sheet->setCellValue("F$currentRow", $item['total_jumlah']);
                    $sheet->setCellValue("G$currentRow", $item['harga_pembelian']);
                    $sheet->getStyle("G$currentRow")->getNumberFormat()->setFormatCode('"Rp"#,##0');
                    $sheet->setCellValue("H$currentRow", $item['harga']);
                    $sheet->getStyle("H$currentRow")->getNumberFormat()->setFormatCode('"Rp"#,##0');

                    $sheet->getStyle("B$currentRow:H$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("B$currentRow:H$currentRow")->getFont()->setBold(false);

                    $currentRow++;
                }

                $tanggal = $this->request->daterange;
                $lokasi = 'SEMUA LOKASI';
                if ($this->request->lokasi !== 'all') {
                    $lokasiObj = Lokasi::find($this->request->lokasi);
                    $lokasi = $lokasiObj ? $lokasiObj->nama : 'SEMUA LOKASI';
                }

                $sheet->setCellValue("B4", "DAFTAR LABA/RUGI PADA LOKASI $lokasi TANGGAL $tanggal");
                $sheet->getStyle("B4")->getFont()->setBold(true);

                $sheet->setCellValue("B$currentRow", "Jumlah Penjualan");
                $sheet->getStyle("B$currentRow")->getFont()->setBold(true);

                $sheet->setCellValue("F$currentRow", $totalPenjualan . " Ball ");
                $sheet->getStyle("F$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F$currentRow")->getFont()->setBold(true);


                $sheet->setCellValue("B" . ($currentRow + 3), "Total Penjualan");
                $sheet->mergeCells("B" . ($currentRow + 3) . ":C" . ($currentRow + 3));
                $sheet->setCellValue("D" . ($currentRow + 3), $totalTerjual);
                $sheet->getStyle("D" . ($currentRow + 3))->getNumberFormat()->setFormatCode('"Rp"#,##0');
                $sheet->getStyle("D" . ($currentRow + 3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("B" . ($currentRow + 4), "Total Diskon Produk");
                $sheet->mergeCells("B" . ($currentRow + 4) . ":C" . ($currentRow + 4));
                $sheet->setCellValue("D" . ($currentRow + 4), $totalDiskonProduk);
                $sheet->getStyle("D" . ($currentRow + 4))->getNumberFormat()->setFormatCode('"Rp"#,##0');
                $sheet->getStyle("D" . ($currentRow + 4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("B" . ($currentRow + 5), "Total Diskon Nota");
                $sheet->mergeCells("B" . ($currentRow + 5) . ":C" . ($currentRow + 5));
                $sheet->setCellValue("D" . ($currentRow + 5), $totalDiskonNota);
                $sheet->getStyle("D" . ($currentRow + 5))->getNumberFormat()->setFormatCode('"Rp"#,##0');
                $sheet->getStyle("D" . ($currentRow + 5))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("B" . ($currentRow + 6), "Total Pengeluaran");
                $sheet->mergeCells("B" . ($currentRow + 6) . ":C" . ($currentRow + 6));
                $sheet->setCellValue("D" . ($currentRow + 6), $totalPengeluaran);
                $sheet->getStyle("D" . ($currentRow + 6))->getNumberFormat()->setFormatCode('"Rp"#,##0');
                $sheet->getStyle("D" . ($currentRow + 6))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("B" . ($currentRow + 7), "Total Transfer");
                $sheet->getStyle("B" . ($currentRow + 7))->getFont()->setBold(true);
                $sheet->mergeCells("B" . ($currentRow + 7) . ":C" . ($currentRow + 7));
                $sheet->getStyle("B" . ($currentRow + 7))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue("D" . ($currentRow + 7), $totalTransfer);
                $sheet->getStyle("D" . ($currentRow + 7))->getNumberFormat()->setFormatCode('"Rp"#,##0');
                $sheet->getStyle("D" . ($currentRow + 7))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D" . ($currentRow + 7))->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $sheet->setCellValue("B" . ($currentRow + 8), "Modal Usaha");
                $sheet->mergeCells("B" . ($currentRow + 8) . ":C" . ($currentRow + 8));
                $sheet->setCellValue("D" . ($currentRow + 8), $modalUsaha);
                $sheet->getStyle("D" . ($currentRow + 8))->getNumberFormat()->setFormatCode('"Rp"#,##0');
                $sheet->getStyle("D" . ($currentRow + 8))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->setCellValue("B" . ($currentRow + 9), "Laba Bersih");
                $sheet->getStyle("B" . ($currentRow + 9))->getFont()->setBold(true);
                $sheet->mergeCells("B" . ($currentRow + 9) . ":C" . ($currentRow + 9));
                $sheet->getStyle("B" . ($currentRow + 9))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue("D" . ($currentRow + 9), $labaBersih);
                $sheet->getStyle("D" . ($currentRow + 9))->getNumberFormat()->setFormatCode('"Rp"#,##0');
                $sheet->getStyle("D" . ($currentRow + 9))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D" . ($currentRow + 9))->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $cellRange = "B$startRow:H$currentRow";

                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ];
                $sheet->getStyle($cellRange)->applyFromArray($borderStyle);

                $exportFileName = 'exports/laporan-pendapatan-' . time() . '.xlsx';
                Storage::put($exportFileName, '');

                $filePath = Storage::path($exportFileName);
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($filePath);

                session(['export_file' => $exportFileName]);
            },
        ];
    }
}
