<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .table-cell {
            border: 0.75pt solid rgb(0, 0, 0);
            text-align: center;
        }
        .table-header {
            font-size: 12pt;
            text-align: center;
        }
        .total-row {
            background-color: #f4f4f4;
            font-weight: bold;
        }
    </style>
    
</head>

<body>
    <div class="container mt-5">
        <p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">BDR BALL</p>
        <p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">Jl. Tinumbu No.20 Telp. (0411) 22099</p>

        @if($namaLokasi)
            <p class="text-start mt-5">LAPORAN STOK BARANG PADA LOKASI {{ $namaLokasi }}</p>
        @else
            <p class="text-start mt-5">LAPORAN STOK BARANG SEMUA LOKASI</p>
        @endif

        <table cellspacing="0" cellpadding="0" style="border: 0.75pt solid rgb(0, 0, 0); border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th class="table-header">No</th>
                    <th class="table-header">Kode Barang</th>
                    <th class="table-header">Merek</th>
                    <th class="table-header">Masuk</th>
                    <th class="table-header">Keluar</th>
                    <th class="table-header">Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                        <tr>
                            <td class="table-cell">{{ $index + 1 }}</td>
                            <td class="table-cell">{{ $item['kode_barang'] }}</td>
                            <td class="table-cell">{{ $item['merek'] }}</td>
                            <td class="table-cell">{{ number_format($item['total_masuk'], 0, ',', '.') }}</td>
                            <td class="table-cell">{{ number_format($item['total_terjual'], 0, ',', '.') }}</td>
                            <td class="table-cell">{{ number_format($item['stok_akhir'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                    <tr class="total-row">
                        <td colspan="3" class="text-end fw-bold">Total</td>
                        <td class="table-cell font-weight-bold">{{ number_format(array_sum(array_column($data, 'total_masuk')), 0, ',', '.') }}</td>
                        <td class="table-cell font-weight-bold">{{ number_format(array_sum(array_column($data, 'total_terjual')), 0, ',', '.') }}</td>
                        <td class="table-cell font-weight-bold">{{ number_format(array_sum(array_column($data, 'stok_akhir')), 0, ',', '.') }}</td>
                    </tr>
            </tbody>
        </table>
    </div>

</body>

</html>
