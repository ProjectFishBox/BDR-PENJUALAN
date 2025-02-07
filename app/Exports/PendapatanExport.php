<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;

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


                $Pembelian = PembelianDetail::query()
                    ->select([
                        'pembelian_detail.id_barang',
                        'pembelian_detail.nama_barang',
                        'pembelian_detail.merek',
                        'pembelian_detail.harga',
                        'pembelian_detail.jumlah',
                        'pembelian_detail.subtotal',
                        'pembelian.id_lokasi',
                        'barang.kode_barang as kode_barang',
                        'lokasi.nama'
                    ])
                    ->join('barang', 'pembelian_detail.id_barang', '=', 'barang.id')
                    ->join('pembelian', 'pembelian_detail.id_pembelian', '=', 'pembelian.id')
                    ->join('lokasi', 'pembelian.id_lokasi', '=', 'lokasi.id')
                    ->where('pembelian_detail.delete', 0)
                    ->when($this->request->filled('lokasi'), fn($query) => $query->where('pembelian.id_lokasi', $this->request->lokasi))
                    ->selectRaw('SUM(pembelian_detail.jumlah) as total_masuk')
                    ->selectRaw('SUM(pembelian_detail.subtotal) as total_pembelian')
                    ->groupBy([
                        'pembelian_detail.id_barang',
                        'pembelian_detail.nama_barang',
                        'pembelian_detail.merek',
                        'pembelian_detail.harga',
                        'pembelian_detail.jumlah',
                        'pembelian_detail.subtotal',
                        'barang.kode_barang',
                        'pembelian.id_lokasi',
                        'lokasi.nama'
                    ])
                    ->get();

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
                        'lokasi.nama'
                    ])
                    ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
                    ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id')
                    ->join('lokasi', 'penjualan.id_lokasi', '=', 'lokasi.id')
                    ->where('penjualan_detail.delete', 0)
                    ->when($this->request->filled('lokasi'), fn($query) => $query->where('penjualan.id_lokasi', $this->request->lokasi))
                    ->selectRaw('SUM(penjualan_detail.jumlah) as total_terjual')
                    ->selectRaw('SUM((penjualan_detail.harga - penjualan_detail.diskon_barang) * penjualan_detail.jumlah) as total_penjualan')
                    ->selectRaw('SUM(penjualan.diskon_nota) as total_diskon_nota')
                    ->groupBy([
                        'penjualan_detail.id_barang',
                        'penjualan_detail.nama_barang',
                        'penjualan_detail.merek',
                        'penjualan_detail.harga',
                        'penjualan_detail.diskon_barang',
                        'penjualan_detail.jumlah',
                        'penjualan_detail.id_penjualan',
                        'barang.kode_barang',
                        'penjualan.id_lokasi',
                        'penjualan.tanggal',
                        'penjualan.diskon_nota',
                        'lokasi.nama'
                    ])
                    ->get();




                $data = $Pembelian->flatMap(function ($item) use ($penjualan) {
                    $terjual = $penjualan
                        ->where('id_barang', $item->id_barang)
                        ->where('nama_barang', $item->nama_barang)
                        ->where('merek', $item->merek)
                        ->where('id_lokasi', $item->id_lokasi);

                    return $terjual->map(function ($jual) use ($item) {
                        return [
                            'id_barang' => $item->id_barang,
                            'kode_barang' => $item->kode_barang,
                            'nama_barang' => $item->nama_barang,
                            'merek' => $item->merek,
                            'diskon_barang' => $jual->diskon_barang,
                            'diskon_nota' => $jual->diskon_nota,
                            'id_lokasi' => $item->id_lokasi,
                            'tanggal' => $jual->tanggal,
                            'nama_lokasi' => $item->nama,
                            'total_masuk' => $item->total_masuk,
                            'harga_pembelian' => $item->harga,
                            'jumlah_pembelian' => $item->jumlah,
                            'harga_penjualan' => $jual->harga,
                            'total_terjual' => $jual->total_terjual ?? 0,
                            'stok_akhir' => $item->total_masuk - ($jual->total_terjual ?? 0),
                            'total_pembelian' => $item->total_pembelian,
                            'total_penjualan' => $jual->total_penjualan,
                            'total_diskon_nota' => $jual->total_diskon_nota,
                            'id_penjualan' => $jual->id_penjualan
                        ];
                    });
                })->toArray();

                $totalTerjual = array_sum(array_column($data, 'total_terjual'));
                $totalPenjualan = array_sum(array_column($data, 'total_penjualan'));
                $totalDiskonProduk = array_sum(array_column($data, 'diskon_barang'));
                $totalPembelian = array_sum(array_column($data, 'total_pembelian'));

                $uniqueDiskonNota = [];
                foreach ($data as $item) {
                    if (!isset($uniqueDiskonNota[$item['id_penjualan']])) {
                        $uniqueDiskonNota[$item['id_penjualan']] = $item['diskon_nota'];
                    }
                }
                $totalDiskonNota = array_sum($uniqueDiskonNota);

                $startRow = 6;
                $currentRow = $startRow;

                foreach ($data as $key => $item) {

                    $sheet->setCellValue("B$currentRow", $item['kode_barang']);
                    $sheet->setCellValue("C$currentRow", $item['nama_barang']);
                    $sheet->setCellValue("D$currentRow", $item['tanggal']);
                    $sheet->setCellValue("E$currentRow", $item['total_terjual']);
                    $sheet->setCellValue("F$currentRow", $item['harga_pembelian']);
                    $sheet->setCellValue("G$currentRow", $item['harga_penjualan']);

                    $sheet->getStyle("B$currentRow:G$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $currentRow++;

                }

                $totalPengeluaran = $totalDiskonProduk + $totalDiskonNota;
                $totalTransfer  = ($totalPenjualan - ($totalPengeluaran + $totalDiskonNota + $totalDiskonProduk));
                $labaBersih = $totalTransfer - $totalPembelian;



                $sheet->setCellValue("B$currentRow", "Jumlah Penjualan");
                $sheet->getStyle("B$currentRow")->getFont()->setBold(true);

                $sheet->setCellValue("E$currentRow", $totalTerjual);
                $sheet->getStyle("E$currentRow")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("C" . ($currentRow + 1), $totalPenjualan);
                $sheet->getStyle("C" . ($currentRow + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("C" . ($currentRow + 2), $totalDiskonProduk);
                $sheet->getStyle("C" . ($currentRow + 2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("C" . ($currentRow + 3), $totalDiskonNota);
                $sheet->getStyle("C" . ($currentRow + 3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("C" . ($currentRow + 4), $totalPengeluaran);
                $sheet->getStyle("C" . ($currentRow + 4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("C" . ($currentRow + 5), $totalTransfer);
                $sheet->getStyle("C" . ($currentRow + 5))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("C" . ($currentRow + 6), $totalPembelian);
                $sheet->getStyle("C" . ($currentRow + 6))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("C" . ($currentRow + 7), $labaBersih);
                $sheet->getStyle("C" . ($currentRow + 7))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $cellRange = "B$startRow:G$currentRow";

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
