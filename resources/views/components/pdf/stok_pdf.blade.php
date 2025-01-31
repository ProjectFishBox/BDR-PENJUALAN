<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .table {
            width: 100%;
            margin-bottom: 2rem;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .table thead th {
            background-color: #343a40;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .table tbody td {
            padding: 10px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .company-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .company-address {
            font-size: 16px;
            color: #333;
        }

    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="company-header">
            <div class="company-name">BDR BALL</div>
            <div class="company-address">JL. Tinumbu No.20 Telp (0411) 22099</div>
        </div>

        @if($namaLokasi)
            <h3 class="text-start mt-5">LAPORAN STOK BARANG PADA LOKASI {{ $namaLokasi }}</h3>
        @else
            <h3 class="text-start mt-5">LAPORAN STOK BARANG SEMUA LOKASI</h3>
        @endif

        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Kode Barang</th>
                    <th class="text-center">Merek</th>
                    <th class="text-center">Masuk</th>
                    <th class="text-center">Keluar</th>
                    <th class="text-center">Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $item['kode_barang'] }}</td>
                            <td class="text-center">{{ $item['merek'] }}</td>
                            <td class="text-center">{{ number_format($item['total_masuk'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($item['total_terjual'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($item['stok_akhir'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-bold">Total</td>
                        <td class="text-center fw-bold">{{ number_format(array_sum(array_column($data, 'total_masuk')), 0, ',', '.') }}</td>
                        <td class="text-center fw-bold">{{ number_format(array_sum(array_column($data, 'total_terjual')), 0, ',', '.') }}</td>
                        <td class="text-center fw-bold">{{ number_format(array_sum(array_column($data, 'stok_akhir')), 0, ',', '.') }}</td>
                    </tr>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
