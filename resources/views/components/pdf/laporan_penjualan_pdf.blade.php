<p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">BDR BALL</p>
<p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">Jl. Tinumbu No.20 Telp. (0411) 22099</p>

<p>DAFTAR PENJUALAN BARANG PADA LOKASI {{ $lokasi }} TANGGAL {{ $tanggal }}</p>
<table cellspacing="0" cellpadding="0" style="border: 0.75pt solid rgb(0, 0, 0); border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:4%;">No.</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:8%;">Tanggal</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:12%;">Nama Pelanggan</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:8%;">Kode Barang</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:10%;">Merek</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:8%;">Harga</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:7%;">Diskon Produk</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:6%;">Qty</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:8%;">Jumlah</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:8%;">Total</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:8%;">Diskon Nota</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:6%;">Bayar</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt; width:7%;">Sisa</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalSisa = 0;
        $index = 1;
        @endphp
        @foreach ($data as $item)
            @php

            $total = 0;
            foreach ($item['detail'] as $detail) {
                $total += (($detail['harga'] * $detail['jumlah']) - ($detail['diskon_barang'] * $detail['jumlah']));
            }

            $sisa = $total - $item['bayar'] - $item['diskon_nota'];
            $sisa = abs($sisa);
            $totalSisa += $sisa;


            @endphp
            <tr>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                    {{ $index }}
                </td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                    {{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}
                </td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $item['nama_pelanggan'] }}</td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"> {{ $total !== null ? number_format($total, 0, ',', '.') : '' }}</td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"> {{ $item['diskon_nota'] !== null ? number_format($item['diskon_nota'], 0, ',', '.') : '' }}</td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $item['bayar'] !== null ? number_format($item['bayar'], 0, ',', '.') : '' }}</td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ number_format($sisa, 0, ',', '.') }}</td>
            </tr>
            @foreach ($item['detail'] as $detail)
                <tr>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;"></td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $detail['kode_barang'] }}</td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $detail['merek'] }}</td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                        {{ number_format($detail['harga'], 0, ',', '.') }}</td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                        {{ number_format($detail['diskon_barang'], 0, ',', '.') }}</td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                        {{ number_format($detail['jumlah'], 0, ',', '.') }}</td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                        {{ number_format($detail['harga'] * $detail['jumlah'], 0, ',', '.') }}</td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                    </td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                    </td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                    </td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                    </td>
                </tr>
            @endforeach
            @php
                $index++;
            @endphp
        @endforeach
        <tr>
            <td colspan="6" style="border: 0.75pt solid rgb(0, 0, 0); text-align:right; font-weight:bold;">
                Total Penjualan
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                {{ number_format($totalDiskonBarang, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                {{ number_format($totalHitung, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                {{ number_format($totalPenjualan, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                {{ number_format($totalJumlah, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                {{ number_format($totalDiskon, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                {{ number_format($totalBayar, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                {{ number_format(abs($totalSisa), 0, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>
