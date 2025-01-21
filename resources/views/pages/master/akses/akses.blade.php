@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>List Akses</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <!-- Tombol Tambah -->
                <a href="/tambah-akses">
                    <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                </a>

                <!-- Input Search -->
                <form action="{{ url()->current() }}" method="GET" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Cari Akses" class="form-control" style="width: 250px; margin-left: 10px;" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn btn-secondary ml-2">Cari</button>
                </form>

            </div>
            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">No</th>
                                <th scope="col" style="text-align: center; width: 60%;">Nama Akses</th>
                                <th scope="col" style="text-align: center; width: 30%;">Akses Menu</th>
                                <th scope="col" style="text-align: center; width: 5%;">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($akses as $key => $aksesItem)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td style="text-align: center;"">{{ $aksesItem->nama }}</td>
                                <td style="text-align: center;">
                                    @if($aksesItem->all_menus_selected)
                                        <li>Semua Menu</li><br>
                                    @else
                                        @foreach($aksesItem->accessMenus as $menuItem)
                                            <li>{{ $menuItem->menu->nama }}</li><br>
                                        @endforeach
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                        <a href="{{ route('akses-edit', $aksesItem->id) }}">
                                            <button class="btn btn-icon btn-primary">
                                                <i class="anticon anticon-edit"></i>
                                            </button>
                                        </a>
                                        <button class="btn-akses-delete btn btn-icon btn-danger" data-id="{{ $aksesItem->id }}"">
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


    $(document).on('click', '.btn-akses-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log('data id delete', id);
        let url = "/delete-akses/" + id;
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
