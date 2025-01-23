<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\SetHarga;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\ValidationException;

class SetHargaImport implements ToCollection
{

    public function collection(Collection $rows)
    {
        $rowsArray = $rows->values()->toArray();

        $errors = $this->processRows($rowsArray);

        if (!empty($errors)) {
            throw ValidationException::withMessages([
                'import_errors' => implode("\n", $errors),
            ]);
        }
    }

    private function processRows(array $rows): array
    {
        $errors = [];

        foreach ($rows as $index => $row) {

            if ($index === 0) {
                continue;
            }

            // Validasi dan impor data
            $this->processRow($row, $index + 1, $errors);
        }

        return $errors;
    }

    private function processRow(array $row, int $lineNumber, array &$errors): void
    {
        $namaBarang = $row[0] ?? null;

        if (empty($namaBarang) || !is_string($namaBarang)) {
            $errors[] = "Nama barang tidak valid pada baris ke {$lineNumber}.";
            return;
        }

        $barang = Barang::where('nama', $namaBarang)->first();

        if ($barang) {
            $this->createSetHarga($row, $barang);
        } else {
            $errors[] = "Barang dengan nama '{$namaBarang}' tidak ditemukan pada baris ke {$lineNumber}.";
        }
    }

    private function createSetHarga(array $row, Barang $barang): void
    {
        SetHarga::create([
            'id_lokasi'  => auth()->user()->id_lokasi,
            'id_barang'  => $barang->id,
            'nama_barang'=> $row[0],
            'kode_barang'=> $barang->kode_barang ?? null,
            'merek'      => $barang->merek,
            'harga'      => $barang->harga,
            'untung'     => $row[1] ?? 0,
            'harga_jual' => $row[2] ?? 0,
            'status'     => $row[3],
            'create_by'  => auth()->id(),
            'last_user'  => auth()->id(),
        ]);
    }
}
