@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <a href="/tambah-setharga">
                        <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                    </a>
                    <button class="btn btn-default btn-success btn-tone  btn-import" id="btn-import" type="button" role="button">
                        <i class="far fa-file-excel mr-1"></i>
                        <span>Import</span>
                    </button>
                </div>


                <form action="{{ url()->current() }}" method="GET" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Cari" class="form-control" style="width: 250px; margin-left: 10px;" value="{{ request()->get('search') }}">
                    <select name="lokasi" class="form-control ml-2" style="width: 200px;">
                        <option value="">Semua Lokasi</option>
                        @foreach ($lokasiList as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ request()->get('lokasi') == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary ml-2">Filter</button>
                </form>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 10%;">No</th>
                                <th scope="col" style="text-align: center; width: 80%;">Kode</th>
                                <th scope="col" style="text-align: center; width: 10%;">Nama</th>
                                <th scope="col" style="text-align: center; width: 10%;">Merek</th>
                                <th scope="col" style="text-align: center; width: 10%;">Untung</th>
                                <th scope="col" style="text-align: center; width: 10%;">Harga Jual</th>
                                <th scope="col" style="text-align: center; width: 10%;">Status</th>
                                <th scope="col" style="text-align: center; width: 10%;">Lokasi</th>
                                <th scope="col" style="text-align: center; width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $setharga)
                                <tr>
                                    <th scope="row" style="text-align: center;">{{ $index + 1 }}</th>
                                    <td style="text-align: center;">
                                        {{ $setharga->kode_barang }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $setharga->nama_barang }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $setharga->merek }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $setharga->untung }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $setharga->harga_jual }}
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="form-group d-flex align-items-center" style="margin: unset">
                                            <div class="switch m-r-10">
                                                <input type="checkbox" id="switch-1" {{ $setharga->status === 'Aktif' ? 'checked' : '' }}>
                                                <label for="switch-1"></label>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $setharga->lokasi->nama }}
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                            <a href="{{ route('setharga-edit', $setharga->id) }}">
                                                <button class="btn btn-icon btn-primary">
                                                    <i class="anticon anticon-edit"></i>
                                                </button>
                                            </a>
                                            <button class="btn-setharga-delete btn btn-icon btn-danger" data-id="{{ $setharga->id }}"">
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

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1" role="dialog"
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


    $(document).on('click', '.btn-setharga-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/delete-setharga/" + id;
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
                            text: 'Data Set Harga Telah berhasil dihapus.',
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
        let url = "/modal-import-setharga";
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
        const url = '/import-setharga';
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
        })
    })
</script>

@endpush
