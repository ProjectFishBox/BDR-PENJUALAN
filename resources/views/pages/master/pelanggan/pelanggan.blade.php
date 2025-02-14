@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <div style="display: flex; align-items: center;">
                <a href="/tambah-pelanggan">
                    <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                </a>

                <form id="filterForm" style="display: flex; align-items: center; margin-left: auto; width:250px">
                    <select name="lokasi" class="lokasi form-control">
                        <option value="">Semua Lokasi</option>
                        <option value="all">Semua Lokasi</option>
                        @foreach ($lokasiList as $lokasi)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary m-l-5">Filter</button>
                </form>
            </div>

            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover data-table" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center;">No</th>
                                <th scope="col" style="text-align: center;"">Nama</th>
                                <th scope="col" style="text-align: center;"">Alamat</th>
                                <th scope="col" style="text-align: center;">Kota</th>
                                <th scope="col" style="text-align: center;"">Kode Pos</th>
                                <th scope="col" style="text-align: center;"">Telepon</th>
                                <th scope="col" style="text-align: center;"">Fax</th>
                                <th scope="col" style="text-align: center;"">Lokasi</th>
                                <th scope="col" style="text-align: center;"">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@component('components.aset_datatable.aset_datatable')@endcomponent

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('.lokasi').select2({
        width: '100%',
        placeholder: 'Pilih Lokasi',
    });

</script>

<script>
    $(document).ready(function() {
        dataPelanggan();

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            reloadTable();
        });

    });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataPelanggan();
    }
</script>

<script>
    function dataPelanggan() {
        if ($.fn.DataTable.isDataTable('.data-table')) {
            $('.data-table').DataTable().destroy();
        }

        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pelanggan') }}",
                data: function (d) {
                    d.lokasi = $('select[name="lokasi"]').val();
                }
            },
            lengthMenu: [10, 20],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'nama', name: 'nama' },
                { data: 'alamat', name: 'alamat' },
                { data: 'nama_kota', name: 'nama_kota' },
                { data: 'kode_pos', name: 'kode_pos' },
                { data: 'telepon', name: 'telepon' },
                { data: 'fax', name: 'fax' },
                { data: 'lokasi.nama', name: 'lokasi.nama' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    }

    dataPelanggan();
</script>

<script>
    $(document).on('click', '.btn-pelanggan-edit', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/pelanggan-edit/" + id;
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
                $('.btn-pelanggan-edit').prop('disabled', false);
                $('.btn-pelanggan-edit').html('<i class="anticon anticon-edit"></i>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-pelanggan-edit').prop('disabled', false);
                $('.btn-pelanggan-edit').html(' <i class="anticon anticon-edit"></i>');
            }
        })
    })
</script>

<script>
    $(document).on('click', '.btn-pelanggan-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log('data id delete', id);
        let url = "/delete-pelanggan/" + id;
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
                            text: 'Data Pelanggan Telah berhasil dihapus.',
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
