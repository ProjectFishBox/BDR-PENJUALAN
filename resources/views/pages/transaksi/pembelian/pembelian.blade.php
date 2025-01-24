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



                <!-- Input Search -->
                <form action="{{ url()->current() }}" method="GET" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Cari" class="form-control" style="width: 250px; margin-left: 10px;" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn btn-secondary ml-2">Cari</button>
                </form>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">No</th>
                                <th scope="col" style="text-align: center; width: 50%;">Tanggal</th>
                                <th scope="col" style="text-align: center; width: 20%;">No Nota</th>
                                <th scope="col" style="text-align: center; width: 10%;">Kontainer</th>
                                <th scope="col" style="text-align: center; width: 10%;">Total</th>
                                <th scope="col" style="text-align: center; width: 5%;">Lokasi</th>
                                <th scope="col" style="text-align: center; width: 5%;">Detail</th>
                                <th scope="col" style="text-align: center; width: 5%;">Akse</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $pembelian)
                                <tr>
                                    <th scope="row" style="text-align: center;">{{ $index + 1 }}</th>
                                    <td style="text-align: center;">
                                        {{ $pembelian->tanggal }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pembelian->no_nota }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pembelian->kontainer }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pembelian->bayar }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pembelian->lokasi->nama }}
                                    </td>
                                    <td style="text-align: center;">
                                        <button class="btn btn-primary btn-detail" id="btn-detail" data-id={{ $pembelian->id }}>Detail</button>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                            <a href="{{ route('pembelian-edit', $pembelian->id) }}">
                                                <button class="btn btn-icon btn-primary">
                                                    <i class="anticon anticon-edit"></i>
                                                </button>
                                            </a>
                                            <button class="btn-pembelian-delete btn btn-icon btn-danger" data-id="{{ $pembelian->id }}"">
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
{{-- <script>

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
</script> --}}

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

@endpush
