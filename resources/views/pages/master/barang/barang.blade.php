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



                <!-- Input Search -->
                <form action="{{ url()->current() }}" method="GET" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Cari Barang" class="form-control" style="width: 250px; margin-left: 10px;" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn btn-secondary ml-2">Cari</button>
                </form>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">No</th>
                                <th scope="col" style="text-align: center; width: 50%;">Kode</th>
                                <th scope="col" style="text-align: center; width: 20%;">Nama</th>
                                <th scope="col" style="text-align: center; width: 10%;">Merek</th>
                                <th scope="col" style="text-align: center; width: 10%;">Harga</th>
                                <th scope="col" style="text-align: center; width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $barang)
                                <tr>
                                    <th scope="row" style="text-align: center;">{{ $index + 1 }}</th>
                                    <td style="text-align: center;">
                                        {{ $barang->kode_barang }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $barang->nama }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $barang->merek }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $barang->harga }}
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                            <a href="{{ route('barang-edit', $barang->id) }}">
                                                <button class="btn btn-icon btn-primary">
                                                    <i class="anticon anticon-edit"></i>
                                                </button>
                                            </a>
                                            <button class="btn-barang-delete btn btn-icon btn-danger" data-id="{{ $barang->id }}"">
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
            error: function(error)
            {
                console.error(error);
                $('#savefile').prop("disabled",false);
                $('#savefile').html('Save');
            }
        })
    })
</script>

@endpush
