
<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h4">{{$title}}</h5>
        </div>
        <div class="modal-body">
            <div class="d-flex justify-content-start mb-3">
                <div class="mr-5">
                    <h5 style="font-weight: lighter">Tanggal   : {{ $pembelian->tanggal}}</h5>
                    <h5 style="font-weight: lighter">No Nota   : {{ $pembelian->no_nota}}</h5>
                </div>
                <div>
                    <h5 style="font-weight: lighter">Kontainer : {{ $pembelian->kontainer}}</h5>
                    <h5 style="font-weight: lighter">Lokasi    : {{$lokasi->nama}}</h5>
                </div>
            </div>

            <form action="{{ route('tambah-pembelian')}}" method="POST" id="form-pembelian">
                @csrf
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
                                        <th scope="col">Jumlah</th>
                                        <th scope="col">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detailPembelian as $index => $d)
                                    <tr>
                                        <td style="text-align: center;">{{ $index + 1 }}</td>
                                        <td style="text-align: center;">
                                            {{ $d->id_barang }}
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
                                            {{ $d->jumlah }}
                                        </td>
                                        <td style="text-align: center;">
                                            Rp{{ number_format($d->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6" style="text-align: end">Total Pembelian</td>
                                        <td style="text-align: center; font-weight: bold;">Rp{{ number_format($totalPembelian, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="bayar_input">Bayar</label>
                                <input type="text" class="form-control" id="bayar_input" placeholder="Bayar" name="bayar" readonly value="Rp{{ number_format($pembelian->bayar, 0, ',', '.') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="kembali_input">Kembali</label>
                                <input type="text" class="form-control" id="kembali_input" placeholder="Kembali" readonly value="Rp {{ number_format(max($pembelian->bayar - $totalPembelian, 0), 0, ',', '.') }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sisa_input">Sisa</label>
                                <input type="text" class="form-control" id="sisa_input" placeholder="Sisa" readonly value="  Rp {{ number_format(max($totalPembelian - $pembelian->bayar, 0), 0, ',', '.') }}">
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>

@push('js')

@endpush

