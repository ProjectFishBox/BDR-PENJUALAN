<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No. Nota',
            'Nama Pelanggan',
            'Kode Barang',
            'Merek',
            'Harga',
            'Diskon Barang',
            'Jumlah',
            'Diskon Nota',
            'Bayar',
            'sisa',
        ];
    }

    public function map($row): array
    {
        $mappedData = [];

        foreach ($row['detail'] as $detail) {
            $sisa = ($detail['harga'] - $detail['diskon_barang']) * $detail['jumlah'] - $row['bayar']; // Menghitung sisa

            $mappedData[] = [
                $row['tanggal'],
                $row['no_nota'],
                $row['nama_pelanggan'],
                $detail['kode_barang'],
                $detail['merek'],
                $detail['harga'],
                $detail['diskon_barang'],
                $detail['jumlah'],
                $row['diskon_nota'],
                $row['bayar'],
                $sisa
            ];
        }
        return $mappedData;
    }
}
