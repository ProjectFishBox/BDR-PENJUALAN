@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title}}</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <!-- Tombol Tambah -->
                <div>
                    <a href="/tambah-pembelian">
                        <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                    </a>
                </div>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover data-table" id="data-table"">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center;">No</th>
                                <th scope="col" style="text-align: center;">Tanggal</th>
                                <th scope="col" style="text-align: center;">No Nota</th>
                                <th scope="col" style="text-align: center;">Kontainer</th>
                                <th scope="col" style="text-align: center;">Total</th>
                                <th scope="col" style="text-align: center;">Lokasi</th>
                                <th scope="col" style="text-align: center;">Detail</th>
                                <th scope="col" style="text-align: center;">Akses</th>
                            </tr>
                        </thead>
                        <tbody>
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

@component('components.aset_datatable.aset_datatable')@endcomponent

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        dataPembelian();
        });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataPembelian();
    }
</script>

<script>
    function dataPembelian() {

        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pembelian') }}",
            lengthMenu: [
                10, 20
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'no_nota',
                    name: 'no_nota'
                },
                {
                    data: 'kontainer',
                    name: 'kontainer'
                },
                {
                    data: 'bayar',
                    name: 'bayar',
                    render: function (data, type, row) {
                        return numberFormat(data);
                    }
                },
                {
                    data: 'lokasi.nama',
                    name: 'lokasi.nama'
                },
                {
                    data: 'nama',
                    name: 'nama',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-primary btn-detail" id="btn-detail-${row.id}" data-id="${row.id}">
                                Detail
                            </button>`;
                    },

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

    function numberFormat(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>

<script>
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log('data id',id)
        let url = "/modal-detail-pembelian";
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
    $(document).on('click', '.btn-pembelian-edit', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/pembelian-edit/" + id;
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
                $('.btn-pembelian-edit').prop('disabled', false);
                $('.btn-pembelian-edit').html('<i class="anticon anticon-edit"></i>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-pembelian-edit').prop('disabled', false);
                $('.btn-pembelian-edit').html(' <i class="anticon anticon-edit"></i>');
            }
        })
    })
</script>

<script>
    $(document).on('click', '.btn-pembelian-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/delete-pembelian/" + id;
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
                            text: 'Data Pembelian Telah berhasil dihapus.',
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
