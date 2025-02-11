<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h4">{{$title}}</h5>
        </div>
        <div class="modal-body">
            <div class="d-flex justify-content-center">
                <h2>BDR BALL</h2>
            </div>

            <div class="d-flex justify-content-center">
                JL. Tinumbu No.20 Telp (0411) 22099
            </div>
            <h3 class="text-center">LAPORAN PENGELUARAN MULAI TANGGAL {{ $daterange}}</h3>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 10%;">No</th>
                            <th style="text-align: center;">Tanggal</th>
                            <th style="text-align: center;">Uraian</th>
                            <th style="text-align: center;">Total</th>
                            <th style="text-align: center;">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengeluaran as $index => $item)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $item->uraian }}</td>
                                <td style="text-align: right;">{{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>{{ $item->lokasi->nama }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data pengeluaran yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                 <a href="{{ route('pengeluaran.export', request()->query()) }}" class="btn btn-primary">Export</a>

            </div>
        </div>
    </div>
</div>
