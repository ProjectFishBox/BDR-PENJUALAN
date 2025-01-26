@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title}}</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <!-- Tombol Tambah -->
                <div>
                    <a href="/tambah-barang">
                        <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                    </a>
                    <button class="btn btn-default btn-success btn-tone  btn-import" id="btn-import" type="button" role="button">
                        <i class="far fa-file-excel mr-1"></i>
                        <span>Import</span>
                    </button>
                </div>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered data-table" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">No</th>
                                <th scope="col" style="text-align: center; width: 20%;">Kode</th>
                                <th scope="col" style="text-align: center; width: 30%;">Nama</th>
                                <th scope="col" style="text-align: center; width: 15%;">Merek</th>
                                <th scope="col" style="text-align: center; width: 15%;">Harga</th>
                                <th scope="col" style="text-align: center; width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1" role="dialog"
        aria-labelledby="importModalLabel" aria-hidden="true">
    </div>

@endsection
@component('components.aset_datatable.aset_datatable')@endcomponent
@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        dataBarang();
        });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataBarang();
    }
</script>

<script>
    function dataBarang() {

        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('barang') }}",
            lengthMenu: [
                10, 20
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'kode_barang',
                    name: 'kode_barang'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'merek',
                    name: 'merek'
                },
                {
                    data: 'harga',
                    name: 'harga',
                    render: function (data, type, row) {
                        return numberFormat(data);
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

    function numberFormat(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>

<script>
    $(document).on('click', '.btn-barang-edit', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/barang-edit/" + id;
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
                $('.btn-barang-edit').prop('disabled', false);
                $('.btn-barang-edit').html('<i class="anticon anticon-edit"></i>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-barang-edit').prop('disabled', false);
                $('.btn-barang-edit').html(' <i class="anticon anticon-edit"></i>');
            }
        })
    })
</script>

<script>
    $(document).on('click', '.btn-barang-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log('data id delete', id);
        let url = "/delete-barang/" + id;
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
                            text: 'Data Barang Telah berhasil dihapus.',
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
    $(document).on('click', '.btn-import', function(e) {
        e.preventDefault();
        let url = "/modal-import-barang";
        $(this).prop('disabled', true)
        $.ajax({
            url,
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                $('#importmodal').html(data);
                $('#importmodal').modal('show');
                $('.btn-import').prop("disabled", false);
                $('.btn-import').html('<i class="far fa-file-excel mr-1"></i><span>Import</span>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-import').prop('disabled', false);
                $('.btn-import').html('<i class="far fa-file-excel mr-1"></i><span>Import</span>');
            }
        })
    })
</script>

<script>
    $(document).on('submit', '#form-imporbarang', function(e){
        e.preventDefault();
        let data = new FormData(this);
        const url = '/import-barang';
        $('#savefile').html("Uploading");
        $('#savefile').prop("disabled",true);
        console.log("berhasil ditekan");
        $.ajax({
            url,
            data,
            type: "POST",
            dataType: "JSON",
            cache:false,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Loading...',
                    html: 'Please wait while we are uploading your file.',
                    icon: "info",
                    buttons: false,
                    dangerMode: true,
                    showConfirmButton: false
                });
            },
            success: function(data)
            {
                if(data.code == 200)
                {
                    Swal.fire({
                        title: 'Success',
                        text: data.success,
                        icon: "success",
                        timer: 2000
                    });
                    $('#savefile').prop("disabled",false);
                    $('#savefile').html('Save');
                    $('#importmodal').modal('hide');
                    reloadTable();
                }else if(data.code == 400)
                {
                    Swal.fire({
                        title: 'Failed',
                        icon: "error",
                        text: data.error,
                        showConfirmButton: true,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#DD6B55",
                    });
                    $('#savefile').prop("disabled",false);
                    $('#savefile').html('Save');
                }
            },
            error: function(error) {
                let errorMessage = 'An error occurred. Please try again.';
                if (error.responseJSON) {
                    // Handle detailed error message
                    errorMessage = error.responseJSON.message;
                    if (error.responseJSON.errors) {
                        const errorDetails = Object.values(error.responseJSON.errors)
                            .flat()
                            .join(', ');
                        errorMessage += `: ${errorDetails}`;
                    }
                }
                Swal.fire({
                    title: 'Failed',
                    icon: "error",
                    text: errorMessage,
                    showConfirmButton: true,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#DD6B55",
                });
                console.error(error);
                $('#savefile').prop("disabled", false);
                $('#savefile').html('Save');
            }
        });
    })
</script>

@endpush
