<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\SetHarga;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SetHargaImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Skip header row
            if ($index === 0) {
                continue;
            }

            $barang = Barang::where('nama', $row[0])->first();

            if ($barang) {
                SetHarga::create([
                    'id_lokasi'  => auth()->user()->id_lokasi,
                    'id_barang'  => $barang->id,
                    'nama_barang'=> $row[0],
                    'kode_barang'=> $row[1] ?? null,
                    'merek'      => $row[2],
                    'harga'      => $row[3],
                    'untung'     => $row[4],
                    'harga_jual' => $row[5],
                    'status'     => 'Aktif',
                    'create_by'  => auth()->id(),
                    'last_user'  => auth()->id(),
                ]);
            }
        }
    }
}
