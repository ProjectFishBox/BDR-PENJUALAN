<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="container mt-5">
        <p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">BDR BALL</p>
        <p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">Jl. Tinumbu No.20 Telp. (0411) 22099</p>

        @if($namaLokasi)
            <p class="text-start mt-5">DAFTAR PEMBELIAN BARANG PADA LOKASI {{ $namaLokasi}} TANGGAL {{ $tanggalRequest }}</p>
        @else
            <p class="text-start mt-5">LAPORAN STOK BARANG SEMUA TANGGAL</p>
        @endif

        <table cellspacing="0" cellpadding="0" style="border: 0.75pt solid rgb(0, 0, 0); border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">No</th>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">No. Nota</th>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Tanggal</th>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Kode Barang</th>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Nama Barang</th>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Merek</th>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Harga Satuan</th>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Jumlah</th>
                    <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_penjualan = 0;
                    $total_jumlah = 0;
                    $index = 1;
                @endphp
                @foreach ($data as $item)
                    @foreach ($item['detail'] as $detail)
                    @php
                        $total_penjualan += $detail['total_item'];
                        $total_jumlah += $detail['jumlah'];
                        $formattedDate = \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y');
                    @endphp
                        <tr>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $index++ }}</td>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $item['no_nota'] }}</td>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $formattedDate }}</td>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $detail['kode_barang'] }}</td>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $detail['nama_barang'] }}</td>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $detail['merek'] }}</td>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ number_format($detail['harga'], 0, ',', '.') }}</td>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ number_format($detail['jumlah'], 0, ',', '.') }}</td>
                            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ number_format($detail['total_item'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr style="background-color: #f4f4f4; font-weight: bold;">
                    <td colspan="7"style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">Total Penjualan:</td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">{{ number_format($total_jumlah, 0, ',', '.') }} Ball</td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">Rp {{ number_format($total_penjualan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>
