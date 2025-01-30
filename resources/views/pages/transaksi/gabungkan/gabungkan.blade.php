@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">

                <a href="/tambah-gabungkan">
                    <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                </a>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover data-table" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center;">No</th>
                                <th scope="col" style="text-align: center;">Tanggal</th>
                                <th scope="col" style="text-align: center;">Dibuat Oleh</th>
                                <th scope="col" style="text-align: center;">Total Ball</th>
                                <th scope="col" style="text-align: center;">Lokasi</th>
                                <th scope="col" style="text-align: center;">Detail</th>
                                <th scope="col" style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal" style="display: none;" id="detailgabungkan" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"> </div>
@endsection

@component('components.aset_datatable.aset_datatable')@endcomponent

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        dataGabungkan();
        });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataGabungkan();
    }
</script>

<script>
    function dataGabungkan() {

        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('gabungkan') }}",
            lengthMenu: [
                10, 20
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'user.nama',
                    name: 'user.nama'
                },
                {
                    data: 'total_ball',
                    name: 'total_ball'
                },
                {
                    data: 'lokasi.nama',
                    name: 'lokasi.nama'
                },
                {
                    data: 'create_by',
                    name: 'create_by',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-primary btn-detail" id="btn-detail-${row.id}" data-id="${row.id}">
                                Detail
                            </button>`;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }
</script>

<script>
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/modal-detail-gabungkan";
        $(this).prop('disabled', true)
        $.ajax({
            url,
            data: {
                id
            },
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                $('#detailgabungkan').html(data);
                $('#detailgabungkan').modal('show');
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
    $(document).on('click', '.btn-gabungkan-edit', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/gabungkan-edit/" + id;
        $(this).prop('disabled', true)
        $.ajax({
            url,
            data: {
                id
            },
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                window.location.href = url;
                $('.btn-gabungkan-edit').prop('disabled', false);
                $('.btn-gabungkan-edit').html('<i class="anticon anticon-edit"></i>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-gabungkan-edit').prop('disabled', false);
                $('.btn-gabungkan-edit').html(' <i class="anticon anticon-edit"></i>');
            }
        })
    })
</script>


<script>
    $(document).on('click', '.btn-gabungkan-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/delete-gabungkan/" + id;
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
                            text: 'Data Gabungkan Telah berhasil dihapus.',
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
    $(document).on('click', '.btn-gabungkan-print', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/print-gabungkan/" + id;
        Swal.fire({
            title: 'Apakah kamu ingin mencetak data ini?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya, Cetak sekarang!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(url, '_blank');
                Swal.fire({
                    title: 'Sedang Mencetak!',
                    text: 'File PDF sedang diproses...',
                    icon: 'success',
                    timer: 2000
                });
            }
        })
    });
</script>


@endpush
