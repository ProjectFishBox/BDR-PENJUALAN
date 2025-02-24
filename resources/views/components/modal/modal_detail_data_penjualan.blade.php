
<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h4">{{$title}}</h5>
        </div>
        <div class="modal-body">
            <div class="d-flex mb-3">
                <div class="mr-5">
                    <h5 style="font-weight: lighter">Tanggal : {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y') }}</h5>
                    <h5 style="font-weight: lighter">No Nota   : {{ $penjualan->no_nota}}</h5>
                    <h5 style="font-weight: lighter">Lokasi    : {{ $lokasi->nama}}</h5>

                </div>
                <div class="ml-5">
                    <h5 style="font-weight: lighter">Pelanggan : {{ $detailPelanggan->nama}}</h5>
                    <h5 style="font-weight: lighter">Alamat : {{ $detailPelanggan->alamat}}</h5>
                    <h5 style="font-weight: lighter">Kota : {{ $kota->name}}</h5>
                    <h5 style="font-weight: lighter">Telepon : {{ $detailPelanggan->telepon}}</h5>
                </div>
            </div>


                <div class="form-row">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table-body">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kode Barang</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Merek</th>
                                    <th scope="col">Harga</th>
                                    <th scope="col">Diskon</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailpenjualan as $index => $d)
                                @php
                                    $subTotal = (($d->harga * $d->jumlah) - ($d->diskon_barang * $d->jumlah));
                                @endphp
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: center;">
                                        {{ $d->barang->kode_barang }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $d->nama_barang }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $d->merek }}
                                    </td>
                                    <td style="text-align: center;">
                                        Rp{{ number_format($d->harga, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        Rp{{ number_format($d->diskon_barang, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $d->jumlah }}
                                    </td>
                                    <td style="text-align: center;">
                                        Rp{{ number_format($subTotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="7" style="text-align: end">Total Penjualan</td>
                                    <td style="text-align: center; font-weight: bold;">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="diskon_nota">Diskon Nota</label>
                            <input type="text" class="form-control" id="diskon_nota" placeholder="Diskon Nota" name="diskon_nota"  readonly value="Rp{{ number_format($penjualan->diskon_nota, 0, ',', '.') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="bayar_input">Bayar</label>
                            <input type="text" class="form-control" id="bayar_input" placeholder="Bayar" name="bayar" readonly value="Rp{{ number_format($penjualan->bayar, 0, ',', '.') }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="total">Total</label>
                            <input type="text" class="form-control" id="total" placeholder="Total" name="total" readonly value="Rp{{ number_format($penjualan->total_penjualan, 0, ',', '.') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="kembali_input">Kembali</label>
                            <input type="text" class="form-control" id="kembali_input" placeholder="kembali" readonly value="Rp {{ number_format(max($penjualan->bayar - $penjualan->total_penjualan, 0), 0, ',', '.') }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="sisa_input">Sisa</label>
                            <input type="text" class="form-control" id="sisa_input" placeholder="Diskon Nota" name="sisa" readonly value="  Rp {{ number_format(max($penjualan->total_penjualan - $penjualan->bayar, 0), 0, ',', '.') }}">
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>

@push('js')

@endpush

