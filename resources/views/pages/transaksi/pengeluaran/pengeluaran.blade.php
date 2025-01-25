@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>List Pengeluaran</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <!-- Tombol Tambah -->
                <a href="/tambah-pengeluaran">
                    <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                </a>

                <!-- Input Search -->
                <form action="{{ url()->current() }}" method="GET" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Cari" class="form-control" style="width: 250px; margin-left: 10px;" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn btn-secondary ml-2">Cari</button>
                </form>
            </div>

            <!-- Filter Form -->
            <form action="{{ url()->current() }}" method="GET">
                <div class="form-group mt-3">
                    <div class="d-flex align-items-center m-2">
                        <!-- Filter Tanggal -->
                        <input type="text" class="form-control datepicker-input" name="start" placeholder="From" value="{{ request()->get('start') }}">
                        <span class="p-h-10">to</span>
                        <input type="text" class="form-control datepicker-input" name="end" placeholder="To" value="{{ request()->get('end') }}">

                        <!-- Filter Lokasi -->
                        <div class="form-group col-md-6">
                            <label for="lokasi">Lokasi</label>
                            <select id="lokasi" class="form-control" name="lokasi">
                                <option value="">Pilih Lokasi</option>
                                @foreach ($lokasi as $b)
                                    <option value="{{ $b->id }}" {{ request()->get('lokasi') == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tombol Filter dan Export -->
                        <button class="btn btn-default ml-3 mr-3" type="submit">Filter</button>
                        <button class="btn-detail btn btn-primary m-r-5 mt-2 mb-2">Export</button>
                        {{-- <a href="{{ route('pengeluaran.export', request()->query()) }}" class="btn btn-primary">Export</a> --}}
                    </div>
                </div>
            </form>



            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">No</th>
                                <th scope="col" style="text-align: center; width: 15%;">Tanggal</th>
                                <th scope="col" style="text-align: center; width: 40%;">Uraian</th>
                                <th scope="col" style="text-align: center; width: 20%;">Total</th>
                                <th scope="col" style="text-align: center; width: 15%;">Lokasi</th>
                                <th scope="col" style="text-align: center; width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengeluaran as $index => $p)
                                <tr>
                                    <th scope="row" style="text-align: center;">{{ $index + 1 }}</th>
                                    <td style="text-align: center;">{{ $p->tanggal }}</td>
                                    <td>{{ $p->uraian }}</td>
                                    <td style="text-align: right;">{{ number_format($p->total, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">{{ $p->lokasi->nama }}</td>
                                    <td style="text-align: center;">
                                        <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                            <a href="{{ route('pengeluaran-edit', $p->id) }}">
                                                <button class="btn btn-icon btn-primary">
                                                    <i class="anticon anticon-edit"></i>
                                                </button>
                                            </a>
                                            <button class="btn-barang-delete btn btn-icon btn-danger" data-id="{{ $p->id }}">
                                                <i class="anticon anticon-delete"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal" style="display: none;" id="detailexport" tabindex="-1" role="dialog"
    aria-labelledby="importModalLabel" aria-hidden="true">
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script>
    $('.datepicker-input').datepicker();
</script>
<script>

    function reloadTable() {
            $.ajax({
                url: "{{ url()->current() }}",
                type: "GET",
                success: function(data) {
                    let tableContent = $(data).find('table tbody').html();
                    $('table tbody').html(tableContent);
                },
                error: function(xhr) {
                    console.error('Failed to reload table:', xhr);
                }
            });
    }


    $(document).on('click', '.btn-lokasi-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log('data id delete', id);
        let url = "/delete-lokasi/" + id;
        Swal.fire({
            title: 'Apakah kamu ingin menghapus data ini?',
            text: "data tidak dapat dikembalikan lagi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya, hapus data ini!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url,
                    type: "GET",
                    dataType: "HTML",
                    success: function(data) {
                        reloadTable();
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Data Lokasi Telah berhasil dihapus.',
                            icon: 'success',
                            timer: 2000

                        })
                    }
                })
            }
        })
    })
</script>

{{-- <script>
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        let url = "/modal-detail-pengeluaran";
        const start = $('#start').val();  // Ambil nilai start
        const end = $('#end').val();  // Ambil nilai end
        const lokasi = $('#lokasi').val();  // Ambil nilai lokasi
        $(this).prop('disabled', true)
        $.ajax({
            url,
            data: { start, end, lokasi },
            type: "GET",
            // dataType: "HTML",
            success: function(data) {
                $('#detailexport').html(data);
                $('#detailexport').modal('show');
                $('.btn-detail').prop("disabled", false);
                $('.btn-detail').html('<span>Export</span>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-detail').prop('disabled', false);
                $('.btn-detail').html('</i><span>Export</span>');
            }
        })
    })
</script> --}}

<script>
    $(document).on('click', '.btn-detail', function(e) {
    e.preventDefault();
    let url = "/modal-detail-pengeluaran"; // Pastikan URL-nya benar

    // Ambil data dari form
    const start = $('input[name="start"]').val(); // Ambil nilai start
    const end = $('input[name="end"]').val(); // Ambil nilai end
    const lokasi = $('select[name="lokasi"]').val(); // Ambil nilai lokasi

    $(this).prop('disabled', true); // Menonaktifkan tombol export sementara

    // Lakukan request AJAX untuk mengambil data dengan filter yang dikirim
    $.ajax({
        url: url, // URL untuk membuka modal dan mengirimkan data filter
        data: { start, end, lokasi }, // Kirimkan data filter
        type: "GET", // Method request
        success: function(data) {
            // Update modal dengan data yang diterima dari server
            $('#detailexport').html(data);
            $('#detailexport').modal('show'); // Tampilkan modal
            $('.btn-detail').prop("disabled", false); // Aktifkan tombol kembali
            $('.btn-detail').html('<span>Export</span>'); // Kembalikan teks tombol
        },
        error: function(error) {
            console.error(error);
            $('.btn-detail').prop('disabled', false); // Aktifkan tombol jika error
            $('.btn-detail').html('<span>Export</span>'); // Kembalikan teks tombol
        }
    });
});

</script>

<script>

    function reloadTable() {
            $.ajax({
                url: "{{ url()->current() }}",
                type: "GET",
                success: function(data) {
                    let tableContent = $(data).find('table tbody').html();
                    $('table tbody').html(tableContent);
                },
                error: function(xhr) {
                    console.error('Failed to reload table:', xhr);
                }
            });
    }


    $(document).on('click', '.btn-barang-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        console.log(id);
        console.log('data id delete', id);
        let url = "/delete-pengeluaran/" + id;
        Swal.fire({
            title: 'Apakah kamu ingin menghapus data ini?',
            text: "data tidak dapat dikembalikan lagi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya, hapus data ini!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url,
                    type: "GET",
                    dataType: "HTML",
                    success: function(data) {
                        reloadTable();
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Data pengeluaran Telah berhasil dihapus.',
                            icon: 'success',
                            timer: 2000

                        })
                    }
                })
            }
        })
    })
</script>
@endpush
