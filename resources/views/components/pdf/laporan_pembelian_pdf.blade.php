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
            <h3 class="text-start mt-5">DAFTAR PEMBELIAN BARANG PADA LOKASI {{ $namaLokasi}} TANGGAL {{ $tanggalRequest }}</h3>
        @else
            <h3 class="text-start mt-5">LAPORAN STOK BARANG SEMUA TANGGAL</h3>
        @endif

        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col" style="text-align: center;">No</th>
                    <th scope="col" style="text-align: center;">No. Nota</th>
                    <th scope="col" style="text-align: center;">Tanggal</th>
                    <th scope="col" style="text-align: center;">Kode Barang</th>
                    <th scope="col" style="text-align: center;">Nama Barang</th>
                    <th scope="col" style="text-align: center;">Merek</th>
                    <th scope="col" style="text-align: center;">Harga Satuan</th>
                    <th scope="col" style="text-align: center;">Jumlah</th>
                    <th scope="col" style="text-align: center;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_penjualan = 0;
                    $total_jumlah = 0;
                @endphp
                @foreach ($data as $index => $item)
                    @foreach ($item['detail'] as $detail)
                    @php
                        $total_penjualan += $detail['total_item'];
                        $total_jumlah += $detail['jumlah'];
                    @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $item['no_nota'] }}</td>
                            <td class="text-center">{{ $item['tanggal'] }}</td>
                            <td class="text-center">{{ $detail['kode_barang'] }}</td>
                            <td class="text-center">{{ $detail['nama_barang'] }}</td>
                            <td class="text-center">{{ $detail['merek'] }}</td>
                            <td class="text-center">{{ number_format($detail['harga'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($detail['jumlah'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($detail['total_item'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr style="background-color: #f4f4f4; font-weight: bold;">
                    <td colspan="7" style="text-align: right;">Total Penjualan:</td>
                    <td class="text-center">{{ number_format($total_jumlah, 0, ',', '.') }} Ball</td>
                    <td style="text-align: right;">Rp {{ number_format($total_penjualan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
