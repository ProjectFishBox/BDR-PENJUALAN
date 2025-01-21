@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>List Pengguna</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <!-- Tombol Tambah -->
                <a href="/tambah-pengguna">
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
                                <th scope="col">No</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Jabatan</th>
                                <th scope="col">Akses</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Username</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $pengguna)
                                <tr>
                                    <th scope="row" style="text-align: center;">{{ $index + 1 }}</th>
                                    <td style="text-align: center;">
                                        {{ $pengguna->nama }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pengguna->jabatan }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pengguna->id_akses }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pengguna->lokasi->nama }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pengguna->username }}
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                            <a href="{{ route('pengguna-edit', $pengguna->id) }}">
                                                <button class="btn btn-icon btn-primary">
                                                    <i class="anticon anticon-edit"></i>
                                                </button>
                                            </a>
                                            <button class="btn-pengguna-delete btn btn-icon btn-danger" data-id="{{ $pengguna->id }}"">
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


    $(document).on('click', '.btn-pengguna-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/delete-pengguna/" + id;
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

@endpush

