<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header">

        </div>
        <div class="modal-body">
            <div class="d-flex justify-content-center">
                <h2>BDR BALL</h2>
            </div>

            <div class="d-flex justify-content-center">
                JL. Tinumbu No.20 Telp (0411) 22099
            </div>

            @if($namaLokasi)
                <h3 class="text-start mt-5">LAPORAN STOK BARANG PADA LOKASI {{ $namaLokasi }}</h3>
            @else
                <h3 class="text-start mt-5">LAPORAN STOK BARANG SEMUA LOKASI</h3>
            @endif

            <div class="table-responsive mt-4">
                <table class="table table-bordered" id="table-body">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Kode Barang</th>
                            <th class="text-center">Merek</th>
                            <th class="text-center">Masuk</th>
                            <th class="text-center">Keluar</th>
                            <th class="text-center">Stok Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $item['kode_barang'] }}</td>
                                <td class="text-center">{{ $item['merek'] }}</td>
                                <td class="text-center">{{ number_format($item['total_masuk'], 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($item['total_terjual'], 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($item['stok_akhir'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-center fw-bold">{{ number_format(array_sum(array_column($data, 'total_masuk')), 0, ',', '.') }}</td>
                            <td class="text-center fw-bold">{{ number_format(array_sum(array_column($data, 'total_terjual')), 0, ',', '.') }}</td>
                            <td class="text-center fw-bold">{{ number_format(array_sum(array_column($data, 'stok_akhir')), 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

