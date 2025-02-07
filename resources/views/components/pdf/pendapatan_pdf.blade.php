<div>
    <p style="margin-top:0pt; margin-bottom:8pt; text-align:center;"><strong>BDR BALL</strong></p>
    <p style="margin-top:0pt; margin-bottom:8pt; text-align:center;">Jl. Tinumbu No.20 Telp. (0411) 22099</p>
    <p style="margin-top:0pt; margin-bottom:8pt;">&nbsp;</p>
    <div style="text-align:center;">
        <table cellspacing="0" cellpadding="0" style="margin-right: auto; margin-left: auto; border: 0.75pt solid rgb(0, 0, 0); border-collapse: collapse; width: 100%;">
            <tbody>
                <tr>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                        <p style="margin-top:0pt; margin-bottom:0pt; text-align:center; font-size:12pt;">No</p>
                    </td>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                        <p style="margin-top:0pt; margin-bottom:0pt; text-align:center; font-size:12pt;">Kode Barang</p>
                    </td>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                        <p style="margin-top:0pt; margin-bottom:0pt; text-align:center; font-size:12pt;">Nama Barang</p>
                    </td>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                        <p style="margin-top:0pt; margin-bottom:0pt; text-align:center; font-size:12pt;">Tanggal Jual</p>
                    </td>
                    <td style="width:83.3pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                        <p style="margin-top:0pt; margin-bottom:0pt; text-align:center; font-size:12pt;">Jumlah</p>
                    </td>
                    <td style="width:83.35pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                        <p style="margin-top:0pt; margin-bottom:0pt; text-align:center; font-size:12pt;">Harga Beli</p>
                    </td>
                    <td style="width:83.35pt; border-left-style:solid; border-left-width:0.75pt; border-bottom-style:solid; border-bottom-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                        <p style="margin-top:0pt; margin-bottom:0pt; text-align:center; font-size:12pt;">Harga Jual</p>
                    </td>
                </tr>
                @foreach ($data as $index => $item)
                    <tr>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $index + 1 }}</p>
                        </td>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $item['kode_barang'] }}</p>
                        </td>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $item['nama_barang'] }}</p>
                        </td>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $item['tanggal'] }} </p>
                        </td>
                        <td style="width:83.3pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $item['jumlah_pembelian'] }}</p>
                        </td>
                        <td style="width:83.35pt; border-top-style:solid; border-top-width:0.75pt; border-right-style:solid; border-right-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"> {{ $item['harga_pembelian'] }}</p>
                        </td>
                        <td style="width:83.35pt; border-top-style:solid; border-top-width:0.75pt; border-left-style:solid; border-left-width:0.75pt; padding-right:5.03pt; padding-left:5.03pt; vertical-align:top;">
                            <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"> {{ $item['total_penjualan']}}</p>
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
                <td style="width: 79.8664%; padding-right: 5.4pt; padding-left: 9.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;"><strong>Jumlah Penjualan</strong></p>
                </td>
                <td style="width: 19.8283%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $totalTerjual }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <p style="margin-top:0pt; margin-bottom:8pt;">&nbsp;</p>
    <table cellspacing="0" cellpadding="0" style="border-collapse: collapse; width: 47%;">
        <tbody>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Total Penjualan</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $totalPenjualan }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Total Diskon Produk</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $totalDiskonProduk }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Total Diskon Nota</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{$totalDiskonNota}}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Total Pengeluaran</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; text-align:right; font-size:12pt;"><strong>Total Transfer</strong></p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">Modal Usaha</p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">{{ $totalPembelian }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 38.7813%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; text-align:right; font-size:12pt;"><strong>Laba Bersih</strong></p>
                </td>
                <td style="width: 61.0142%; padding-right: 5.4pt; padding-left: 5.4pt; vertical-align: top;">
                    <p style="margin-top:0pt; margin-bottom:0pt; font-size:12pt;">&nbsp;</p>
                </td>
            </tr>
        </tbody>
    </table>
    <p style="margin-top:0pt; margin-bottom:8pt;">&nbsp;</p>
</div>
<p style="bottom: 10px; right: 10px; position: absolute;"><a href="https://wordtohtml.net" target="_blank" style="font-size:11px; color: #d0d0d0;">Converted to HTML with WordToHTML.net</a> <span style="font-size:11px; color: #d0d0d0;">|</span> <a href="https://wordtohtml.net" target="_blank" style="font-size:11px; color: #d0d0d0;">Email Signature Generator</a></p>
