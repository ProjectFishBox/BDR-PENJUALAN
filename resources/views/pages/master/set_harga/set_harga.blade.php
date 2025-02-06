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


                <form id="filterForm" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Cari" class="form-control" style="width: 250px; margin-right: 10px;">
                    <select name="lokasi" class="form-control" style="width: 200px; margin-right: 10px;">
                        <option value="">Semua Lokasi</option>
                        @foreach ($lokasiList as $lokasi)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-secondary" onclick="$('.data-table').DataTable().ajax.reload();">Filter</button>
                </form>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover data-table" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center;">No</th>
                                <th scope="col" style="text-align: center;">Kode</th>
                                <th scope="col" style="text-align: center;">Nama</th>
                                <th scope="col" style="text-align: center;">Merek</th>
                                <th scope="col" style="text-align: center;">Untung</th>
                                <th scope="col" style="text-align: center;">Harga Jual</th>
                                <th scope="col" style="text-align: center;">Status</th>
                                <th scope="col" style="text-align: center;">Lokasi</th>
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

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1" role="dialog"
        aria-labelledby="importModalLabel" aria-hidden="true">
    </div>
@endsection

@component('components.aset_datatable.aset_datatable')@endcomponent


@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function updateFileName() {
        var input = document.getElementById('customFile');
        var label = document.querySelector('.custom-file-label');
        label.textContent = input.files[0] ? input.files[0].name : 'Choose file';
    }
</script>
<script>
    $(document).ready(function() {
        dataStHarga();
        });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataStHarga();
    }
</script>

<script>
    function dataStHarga() {

        if ($.fn.DataTable.isDataTable('.data-table')) {
            $('.data-table').DataTable().destroy();
        }

        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "{{ route('setharga') }}",
                data: function (d) {
                    d.lokasi = $('select[name="lokasi"]').val();
                    d.search.value = $('input[name="search"]').val();
                }
            },
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
                    data: 'nama_barang',
                    name: 'nama_barang'
                },
                {
                    data: 'merek',
                    name: 'merek'
                },
                {
                    data: 'untung',
                    name: 'untung',
                    render: function (data, type, row) {
                        return numberFormat(data);
                    }
                },
                {
                    data: 'harga_jual',
                    name: 'harga_jual',
                    render: function (data, type, row) {
                        return numberFormat(data);
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        const checked = data === 'Aktif' ? 'checked' : '';
                        return `
                            <div class="form-group d-flex align-items-center" style="margin: unset">
                                <div class="switch m-r-10">
                                    <input type="checkbox" id="switch-${row.id}" ${checked} data-id="${row.id}">
                                    <label for="switch-${row.id}"></label>
                                </div>
                            </div>`;
                    },
                },
                {
                    data: 'lokasi.nama',
                    name: 'lokasi.nama'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('select[name="lokasi"], input[name="search"]').on('change keyup', function () {
            table.ajax.reload(); // Reload data saat filter berubah
        });
    }

    dataStHarga();

    function numberFormat(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>

<script>
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
    $(document).on('click', '.btn-setharga-edit', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/setharga-edit/" + id;
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
                $('.btn-lokasi-edit').prop('disabled', false);
                $('.btn-lokasi-edit').html('<i class="anticon anticon-edit"></i>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-lokasi-edit').prop('disabled', false);
                $('.btn-lokasi-edit').html(' <i class="anticon anticon-edit"></i>');
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
            console.log(error);
            Swal.fire({
                title: 'Failed',
                icon: "error",
                text: 'Terjadi kesalahan pada server.',
                showConfirmButton: true,
                confirmButtonText: "Ok",
                confirmButtonColor: "#DD6B55",
            });
        }
        })
    })
</script>

<script>
    $(document).on('change', 'input[type="checkbox"]', function() {
        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 'Aktif' : 'Tidak Aktif';
        $.ajax({
            url: `/update-status/${id}`,
            type: "POST",
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Status telah diperbarui.',
                    icon: 'success',
                    timer: 2000
                });
                reloadTable()
            },
            error: function(error) {
                console.error(error);
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat memperbarui status.',
                    icon: 'error'
                });
                $(this).prop('checked', !$(this).is(':checked'));
            }
        });
    });
</script>

@endpush
