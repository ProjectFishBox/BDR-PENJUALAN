
<div>
    <p style="margin-top:0pt; margin-bottom:8pt; text-align:center;"><strong>BDR BALL</strong></p>
    <p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">Jl. Tinumbu No.20 Telp. (0411) 22099</p>
    <p style="margin-top:0pt; margin-bottom:8pt;">&nbsp;</p>
    <p style="margin-top:0pt; margin-bottom:8pt;">DAFTAR LABA/RUGI PADA LOKASI {{ $lokasi}} TANGGAL {{$tanggal}}</p>
    <div style="text-align:center;">
        <table cellspacing="0" cellpadding="0" style="margin-right: auto; margin-left: auto; border: 0.75pt solid rgb(0, 0, 0); border-collapse: collapse; width: 100%;">
            <tbody>
                <tr>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                        <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">No</p>
                    </td>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                        <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Kode Barang</p>
                    </td>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                        <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Nama Barang</p>
                    </td>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                        <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Tanggal Jual</p>
                    </td>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                        <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Jumlah</p>
                    </td>
                    <td style="width:83.35pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                        <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Harga Beli</p>
                    </td>
                    <td style="width:83.35pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                        <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Harga Jual</p>
                    </td>
                </tr>
                @php
                    use Carbon\Carbon;
                @endphp
                @foreach ($penjualan as $index => $item)
                    <tr>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $index + 1 }}</p>
                        </td>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $item['kode_barang'] }}</p>
                        </td>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $item['nama_barang'] }}</p>
                        </td>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</p>
                        </td>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $item['total_jumlah'] }}</p>
                        </td>
                        <td style="width:83.35pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"> Rp {{ number_format($item['harga_pembelian'], 0, ',', '.') }}</p>
                        </td>
                        <td style="width:83.35pt; border-top-style:solid; border-top-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding:5.03pt; vertical-align:middle; text-align:center;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p style="margin-top:0pt; margin-bottom:8pt;">&nbsp;</p>
    <table cellspacing="0" cellpadding="0" style="border-collapse: collapse; width: 71%; margin-right: calc(29%);">
        <tbody>
            <tr>
                <td style="width: 79.8664%; padding-right: 5.4pt; padding-left: 9.4pt; vertical-align: middle; text-align:center;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"><strong>Jumlah Penjualan</strong></p>
                </td>
                <td style="width: 19.8283%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:center;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $totalPenjualan }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <hr style="border: 1px solid black; width: 100%; margin-top: 8pt; margin-bottom: 8pt;">
    <p style="margin-top:0pt; margin-bottom:8pt;">&nbsp;</p>
    <table cellspacing="0" cellpadding="0" style="border-collapse: collapse; width: 47%;">
        <tbody>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Total Penjualan</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt; text-align:right;">Rp {{ number_format($totalTerjual, 0, ',', '.') }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Total Diskon Produk</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt; text-align:right;">Rp {{ number_format($totalDiskonProduk, 0, ',', '.') }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Total Diskon Nota</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt; text-align:right;">Rp {{ number_format($totalDiskonNota, 0, ',', '.') }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt; ">Total Pengeluaran</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt; text-align:right;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                    <hr style="border: 1px solid black; width: 100%; margin-top: 2pt; margin-bottom: 2pt;">
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:right;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"><strong>Total Transfer</strong></p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:right;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"><strong>Rp {{ number_format($totalTransfer, 0, ',', '.') }}</strong></p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Modal Usaha</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:left;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt; text-align:right;">Rp {{ number_format($modalUsaha, 0, ',', '.') }}</p>
                    <hr style="border: 1px solid black; width: 100%; margin-top: 2pt; margin-bottom: 2pt;">
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:right;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"><strong>Laba Bersih</strong></p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: middle; text-align:right;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"><strong>Rp {{ number_format($labaBersih, 0, ',', '.') }}</strong></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p style="margin-top:0pt; margin-bottom:8pt;">&nbsp;</p>
</div>
