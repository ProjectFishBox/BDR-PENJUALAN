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
                    <input type="text" name="search" placeholder="Cari Lokasi" class="form-control" style="width: 250px; margin-left: 10px;" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn btn-secondary ml-2">Cari</button>
                </form>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 10%;">No</th>
                                <th scope="col" style="text-align: center; width: 80%;">Tanggal</th>
                                <th scope="col" style="text-align: center; width: 80%;">Uraian</th>
                                <th scope="col" style="text-align: center; width: 80%;">Total</th>
                                <th scope="col" style="text-align: center; width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengeluaran as $index => $p)
                                <tr>
                                    <th scope="row" style="text-align: center;">{{ $index + 1 }}</th>
                                    <td style="text-align: center;">
                                        {{ $p->tanggal }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $p->uraian }}
                                    </td>
                                    <td style="text-align: center;">
                                        Rp{{ number_format($p->total, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                            <button class="btn btn-primary btn-detail" id="btn-detail" data-id={{ $p->id }}>Detail</button>
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

    <div class="modal fade bd-example-modal" style="display: none;" id="detailpembelianmodal" tabindex="-1" role="dialog"
    aria-labelledby="importModalLabel" aria-hidden="true">
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


<script>
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log('data id',id)
        let url = "/modal-detail-pengeluaran";
        $(this).prop('disabled', true)
        $.ajax({
            url,
            data: {
                id
            },
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                $('#detailpembelianmodal').html(data);
                $('#detailpembelianmodal').modal('show');
                $('.btn-detail').prop("disabled", false);
                $('.btn-detail').html('<span>Detail</span>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-detail').prop('disabled', false);
                $('.btn-detail').html('</i><span>Detail</span>');
            }
        })
    })
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
