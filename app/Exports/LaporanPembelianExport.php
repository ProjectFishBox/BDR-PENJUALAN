<?php

namespace App\Exports;

use App\Models\Barang;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class LaporanPembelianExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'No Nota',
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Merek',
            'Harga Satuan',
            'Jumlah',
            'Total',
        ];
    }

    public function collection()
    {
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
            );

        if ($this->request->filled('daterange')) {
            $dates = explode(' - ', $this->request->daterange);
            $startDate = $dates[0];
            $endDate = $dates[1];
            $query->whereBetween('p.tanggal', [$startDate, $endDate]);
        }

        if ($this->request->filled('lokasi')) {
            $query->where('p.id_lokasi', $this->request->lokasi);
        }

        if ($this->request->filled('merek')) {
            $query->where('dp.merek', $this->request->merek);
        }

        return $query->get();
    }
}
