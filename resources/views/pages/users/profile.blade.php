@extends('components._partials.layout')

@section('content')
    <div class="page-header">
        <h2 class="header-title">Profile</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Pages</a>
                <span class="breadcrumb-item active">Profile</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h3>Ubah Profile</h3>
            <form action="{{ route('user-update') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" name="nama" value="{{ auth()->user()->nama }}" id="nama" placeholder="Nama">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" value="{{ auth()->user()->username }}" id="username" placeholder="Username">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" class="form-control" name="jabatan" value="{{ auth()->user()->jabatan }}" id="jabatan" placeholder="Jabatan">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">Passoword</label>
                        <div>
                            <small style="color: red"> * Kosongkan jika tidak ingin merubah password</small>
                        </div>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password lama">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="lokasi">Lokasi</label>
                        <select id="id_lokasi" name="id_lokasi" class="form-control">
                            @foreach ($lokasi as $l )
                                <option value="{{ $l->id }}">{{ $l->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">Ketik Password</label>
                        <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Masukkan password baru">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group  col-md-6">
                        <label for="nama">Akses</label>
                        <input type="text" class="form-control" name="id_akses" value="{{ auth()->user()->id_akses }}" id="id_akses">
                    </div>
                    <div class="form-group  col-md-6">
                        <label for="nama">Created By</label>
                        <input type="text" class="form-control" name="create_by" value="{{ auth()->user()->create_by }}" id="create_by">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group  col-md-6">
                        <label for="last_user">Last User</label>
                        <input type="text" class="form-control" name="last_user" value="{{ auth()->user()->last_user }}" id="last_user">
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-10">
                    <button class="btn btn-danger" style="margin-right: 10px">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
