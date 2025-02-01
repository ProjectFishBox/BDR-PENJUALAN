<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-body">
            <div class="d-flex justify-content-center">
                <h2>BDR BALL</h2>
            </div>

            <div class="d-flex justify-content-center">
                JL. Tinumbu No.20 Telp (0411) 22099
            </div>

            @if($namaLokasi)
                <h3 class="text-start mt-5">DAFTAR PEMBELIAN BARANG PADA LOKASI {{ $namaLokasi}} TANGGAL {{ $tanggalRequest }}</h3>
            @else
                <h3 class="text-start mt-5">LAPORAN STOK BARANG SEMUA TANGGAL</h3>
            @endif

            <div class="table-responsive">
                <div class="m-t-25">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover data-table" id="data-table">
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
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')

@endpush

