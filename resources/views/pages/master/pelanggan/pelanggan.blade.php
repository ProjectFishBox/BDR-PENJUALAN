@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <!-- Tombol Tambah -->
                <a href="/tambah-pelanggan">
                    <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                </a>

                <form action="{{ url()->current() }}" method="GET" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Cari Pelanggan" class="form-control" style="width: 250px; margin-left: 10px;" value="{{ request()->get('search') }}">
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
                                <th scope="col" style="text-align: center; width: 5%;">No</th>
                                <th scope="col" style="text-align: center; width: 60%;">Nama</th>
                                <th scope="col" style="text-align: center; width: 35%;">Alamat</th>
                                <th scope="col" style="text-align: center; width: 5%;">Kota</th>
                                <th scope="col" style="text-align: center; width: 60%;">Kode Pos</th>
                                <th scope="col" style="text-align: center; width: 35%;">Telepon</th>
                                <th scope="col" style="text-align: center; width: 35%;">Fax</th>
                                <th scope="col" style="text-align: center; width: 35%;">Lokasi</th>
                                <th scope="col" style="text-align: center; width: 35%;">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $pelanggan)
                                <tr>
                                    <th scope="row" style="text-align: center;">{{ $index + 1 }}</th>
                                    <td style="text-align: center;">
                                        {{ $pelanggan->nama }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pelanggan->alamat }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pelanggan->id_kota }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pelanggan->kode_pos }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pelanggan->telepon }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pelanggan->fax }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $pelanggan->lokasi->nama }}
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                            <a href="{{ route('pelanggan-edit', $pelanggan->id) }}">
                                                <button class="btn btn-icon btn-primary">
                                                    <i class="anticon anticon-edit"></i>
                                                </button>
                                            </a>
                                            <form action="{{ route('delete-pelanggan', $pelanggan->id) }}" method="POST">
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
