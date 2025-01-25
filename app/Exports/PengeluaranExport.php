<?php

namespace App\Exports;

use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengeluaranExport implements FromCollection,WithHeadings
{
    protected $pengeluaran;

    public function __construct($pengeluaran)
    {
        $this->pengeluaran = $pengeluaran;
    }

    public function collection()
    {
        return $this->pengeluaran->map(function ($item) {
            return [
                'tanggal' => $item->tanggal,
                'uraian' => $item->uraian,
                'total' => $item->total,
                'lokasi' => $item->lokasi->nama,
            ];
        });
    }

    public function headings(): array
    {
        return ['Tanggal', 'Uraian', 'Total', 'Lokasi'];
    }
}
