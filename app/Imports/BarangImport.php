<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row)
    {
        return new Barang([
            'kode_barang' => $row['kode_barang'],
            'nama'        => $row['nama'],
            'merek'       => $row['merek'],
            'harga'       => $row['harga'],
            'create_by'   => $this->userId,
            'last_user'   => $this->userId,
        ]);
    }
}
