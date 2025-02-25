<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PDF Pendapatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .center-text {
            text-align: center;
        }
        .bold-text {
            font-weight: bold;
        }
        .table {
            margin: 0 auto;
            border: 0.75pt solid rgb(0, 0, 0);
            border-collapse: collapse;
            width: 100%;
        }
        .table td, .table th {
            border: 0.75pt solid rgb(0, 0, 0);
            padding: 5.03pt;
            vertical-align: middle;
            text-align: center;
        }
        .table th {
            font-size: 12pt;
            font-weight: bold;
        }
        .table td {
            font-size: 12pt;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5.4pt;
            vertical-align: middle;
        }
        .summary-table .left-text {
            text-align: left;
        }
        .summary-table .right-text {
            text-align: right;
        }
        .summary-table .center-text {
            text-align: center;
        }
        .summary-table .bold-text {
            font-weight: bold;
        }
        .hr {
            border: 1px solid black;
            width: 100%;
            margin: 8pt 0;
        }
    </style>
</head>
<body>
    <div>
        <p class="center-text bold-text" style="margin: 0 0 8pt 0;">BDR BALL</p>
        <p class="center-text" style="margin: 0 0 8pt 0;">Jl. Tinumbu No.20 Telp. (0411) 22099</p>
        <p style="margin: 0 0 8pt 0;">&nbsp;</p>
        <p class="bold-text" style="margin: 0 0 8pt 0;">DAFTAR LABA/RUGI PADA LOKASI {{ $lokasi }} TANGGAL {{ $tanggal }}</p>
        <div class="center-text">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50pt;">No</th>
                        <th style="width:100pt;">Kode Barang</th>
                        <th style="width:150pt;">Nama Barang</th>
                        <th style="width:100pt;">Tanggal Jual</th>
                        <th style="width:50pt;">Jumlah</th>
                        <th style="width:100pt;">Harga Beli</th>
                        <th style="width:100pt;">Harga Jual</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        use Carbon\Carbon;
                    @endphp
                    @foreach ($penjualan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['kode_barang'] }}</td>
                            <td>{{ $item['nama_barang'] }}</td>
                            <td>{{ Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                            <td>{{ $item['total_jumlah'] }}</td>
                            <td>Rp {{ number_format($item['harga_pembelian'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p style="margin: 0 0 8pt 0;">&nbsp;</p>
        <table class="summary-table" style="width: 100%;">
            <tbody>
                <tr>
                    <td class="left-text" style="width: 25%;"><p class="bold-text" style="margin: 0;">Jumlah Penjualan</p></td>
                    <td class="center-text" style="width: 50%; font-weight: bold"><p style="margin: 0;">{{ $totalPenjualan }}  Ball</p></td>
                </tr>
            </tbody>
        </table>
        <hr class="hr">
        <p style="margin: 0 0 8pt 0;">&nbsp;</p>
        <table class="summary-table" style="width: 50%;">
            <tbody>
                <tr>
                    <td class="left-text" style="width: 50%;"><p style="margin: 0;">Total Penjualan</p></td>
                    <td class="right-text" style="width: 50%;"><p style="margin: 0;">Rp {{ number_format($totalTerjual, 0, ',', '.') }}</p></td>
                </tr>
                <tr>
                    <td class="left-text"><p style="margin: 0;">Total Diskon Produk</p></td>
                    <td class="right-text"><p style="margin: 0;">Rp {{ number_format($totalDiskonProduk, 0, ',', '.') }}</p></td>
                </tr>
                <tr>
                    <td class="left-text"><p style="margin: 0;">Total Diskon Nota</p></td>
                    <td class="right-text"><p style="margin: 0;">Rp {{ number_format($totalDiskonNota, 0, ',', '.') }}</p></td>
                </tr>
                <tr>
                    <td class="left-text"><p style="margin: 0;">Total Pengeluaran</p></td>
                    <td class="right-text"><p style="margin: 0;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p></td>
                </tr>
                <tr>
                    <td class="right-text bold-text">
                        <p style="margin: 0;">Total Transfer</p>
                    </td>
                    <td class="right-text bold-text" style="border-top: 2px solid black; padding-top: 5px;">
                        <p style="margin: 0;">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</p>
                    </td>
                </tr>

                <tr>
                    <td class="left-text"><p style="margin: 0;">Modal Usaha</p></td>
                    <td class="right-text"><p style="margin: 0;">Rp {{ number_format($modalUsaha, 0, ',', '.') }}</p></td>
                </tr>
                <tr>
                    <td class="right-text"><p class="bold-text" style="margin: 0;">Laba Bersih</p></td>
                    <td class="right-text"><p class="bold-text" style="margin: 0; border-top: 2px solid black; padding-top: 5px;">Rp {{ number_format($labaBersih, 0, ',', '.') }}</p></td>
                </tr>
            </tbody>
        </table>
        <p style="margin: 0 0 8pt 0;">&nbsp;</p>
    </div>
</body>
</html>
