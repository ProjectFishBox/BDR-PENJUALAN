<p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">BDR BALL</p>
<p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">Jl. Tinumbu No.20 Telp. (0411) 22099</p>

<p>DAFTAR PENJUALAN BARANG PADA LOKASI {{ $lokasi }} TANGGAL {{ $tanggal }}</p>
<table cellspacing="0" cellpadding="0" style="border: 0.75pt solid rgb(0, 0, 0); border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">No.</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Tanggal</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Nama Pelanggan</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Kode Barang</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Merek</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Harga</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Diskon Produk</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Qty</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Jumlah</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Total</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Diskon Nota</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Bayar</th>
            <th style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-size:12pt;">Sisa</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalSisa = 0;
        $index = 1;
        @endphp
        @foreach ($data as $item)
            <tr>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                    {{ $index }}
                </td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                    {{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}
                </td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">{{ $item['nama_pelanggan'] }}</td>
                <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;" colspan="10"></td>
            </tr>
            @foreach ($item['detail'] as $detail)
                @php
                    $sisa =
                        $detail['harga'] * $detail['jumlah'] -
                        $detail['diskon_barang'] -
                        $item['diskon_nota'] -
                        $item['bayar'];
                    $totalSisa += $sisa;
                @endphp
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
                        {{ number_format($detail['harga'] * $detail['jumlah'] - $detail['diskon_barang'], 0, ',', '.') }}
                    </td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                        {{ $item['diskon_nota'] !== null ? number_format($item['diskon_nota'], 0, ',', '.') : '' }}
                    </td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                        {{ $item['bayar'] !== null ? number_format($item['bayar'], 0, ',', '.') : '' }}
                    </td>
                    <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center;">
                        {{ number_format($sisa, 0, ',', '.') }}
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
                Rp{{ number_format($totalDiskonBarang, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                {{ number_format($totalHitung, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                Rp{{ number_format($totalPenjualan, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                Rp{{ number_format($totalJumlah, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                Rp{{ number_format($totalDiskon, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                Rp{{ number_format($totalBayar, 0, ',', '.') }}
            </td>
            <td style="border: 0.75pt solid rgb(0, 0, 0); text-align:center; font-weight:bold;">
                Rp{{ number_format($totalSisa, 0, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>
