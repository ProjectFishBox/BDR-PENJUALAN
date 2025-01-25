<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h4">{{$title}}</h5>
        </div>
        <div class="modal-body">
            <div class="d-flex justify-content-center">
                <h2>BDR HALL</h2>
            </div>
            <div class="d-flex justify-content-center">
                <h4>Jl. Tinumbu No.20 Tep. (0411) 22099</h4>
            </div>
            <div class="mt-3">
                LAPORAN PENGELUARAN MULAI TANGGAL {{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d/m/Y') }}
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Uraian</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>{{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d/m/Y') }}</td>
                            <td>{{$pengeluaran->uraian}}</td>
                            <td>{{$pengeluaran->total}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
