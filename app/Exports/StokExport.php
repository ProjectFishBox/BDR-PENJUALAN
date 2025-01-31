<?php

namespace App\Exports;

use App\Models\PembelianDetail;
use App\Models\PenjualanDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class StokExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Merek',
            'Lokasi',
            'Total Masuk',
            'Total Keluar',
            'Stok Akhir'
        ];
    }

    public function collection()
    {
        $barangMasuk = PembelianDetail::query()
            ->select([
                'pembelian_detail.id_barang',
                'pembelian_detail.nama_barang',
                'pembelian_detail.merek',
                'barang.kode_barang as kode_barang',
                'pembelian.id_lokasi',
                'lokasi.nama'
            ])
            ->join('barang', 'pembelian_detail.id_barang', '=', 'barang.id')
            ->join('pembelian', 'pembelian_detail.id_pembelian', '=', 'pembelian.id')
            ->join('lokasi', 'pembelian.id_lokasi', '=', 'lokasi.id')
            ->where('pembelian_detail.delete', 0)
            ->when($this->request->filled('lokasi'), function ($query) {
                return $query->where('pembelian.id_lokasi', $this->request->lokasi);
            })
            ->when($this->request->filled('barang'), function ($query) {
                return $query->where('pembelian_detail.id_barang', $this->request->barang);
            })
            ->when($this->request->filled('merek'), function ($query) {
                return $query->where('pembelian_detail.merek', $this->request->merek);
            })
            ->selectRaw('SUM(pembelian_detail.jumlah) as total_masuk')
            ->groupBy([
                'pembelian_detail.id_barang',
                'pembelian_detail.nama_barang',
                'pembelian_detail.merek',
                'barang.kode_barang',
                'pembelian.id_lokasi',
                'lokasi.nama'
            ])
            ->get();

        $barangKeluar = PenjualanDetail::query()
            ->select([
                'penjualan_detail.id_barang',
                'penjualan_detail.nama_barang',
                'penjualan_detail.merek',
                'barang.kode_barang as kode_barang',
                'penjualan.id_lokasi',
                'lokasi.nama'
            ])
            ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id')
            ->join('lokasi', 'penjualan.id_lokasi', '=', 'lokasi.id')
            ->where('penjualan_detail.delete', 0)
            ->when($this->request->filled('lokasi'), function ($query) {
                return $query->where('penjualan.id_lokasi', $this->request->lokasi);
            })
            ->when($this->request->filled('barang'), function ($query) {
                return $query->where('penjualan_detail.id_barang', $this->request->barang);
            })
            ->when($this->request->filled('merek'), function ($query) {
                return $query->where('penjualan_detail.merek', $this->request->merek);
            })
            ->selectRaw('SUM(penjualan_detail.jumlah) as total_terjual')
            ->groupBy([
                'penjualan_detail.id_barang',
                'penjualan_detail.nama_barang',
                'penjualan_detail.merek',
                'barang.kode_barang',
                'penjualan.id_lokasi',
                'lokasi.nama'
            ])
            ->get();

        return collect($barangMasuk->map(function ($item) use ($barangKeluar) {
            $terjual = $barangKeluar
                ->where('id_barang', $item->id_barang)
                ->where('nama_barang', $item->nama_barang)
                ->where('merek', $item->merek)
                ->where('id_lokasi', $item->id_lokasi)
                ->first();

            return [
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'merek' => $item->merek,
                'nama_lokasi' => $item->nama,
                'total_masuk' => $item->total_masuk,
                'total_terjual' => $terjual->total_terjual ?? 0,
                'stok_akhir' => $item->total_masuk - ($terjual->total_terjual ?? 0)
            ];
        }));
    }
}
