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
                                            <form action="{{ route('delete-pengguna', $pengguna->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-icon btn-danger">
                                                    <i class="anticon anticon-delete"></i>
                                                </button>
                                            </form>
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
