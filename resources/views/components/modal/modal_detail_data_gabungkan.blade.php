
<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h4">{{$title}}</h5>
        </div>
        <div class="modal-body">
            <div class="d-flex justify-content-between mb-3">
                <div class="mr-5">
                    <h5 style="font-weight: lighter">Tanggal   : {{ $gabungkan->created_at->format('Y-m-d') }}</h5>
                    <h5 style="font-weight: lighter">Lokasi   : {{ $gabungkan->lokasi->nama}}</h5>
                </div>
                <div>
                    <h5 style="font-weight: lighter">Dibuat Oleh : {{ $gabungkan->user->nama}}</h5>
                </div>
            </div>

            <form id="form-pembelian">
                @csrf
                    <div class="form-row">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-body">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Kode Barang</th>
                                        <th scope="col">Merek</th>
                                        <th scope="col">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detailGabungkan as $index => $d)
                                    <tr>
                                        <td style="text-align: center;">{{ $index + 1 }}</td>
                                        <td style="text-align: center;">
                                            {{ $d->kode_barang }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $d->merek }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $d->jumlah }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" style="text-align: end">Total</td>
                                        <td id="total-cell">{{$gabungkan->total_ball}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>

