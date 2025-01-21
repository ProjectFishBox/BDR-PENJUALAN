@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <form action="{{ route('tambah-pengguna')}}" method="POST">
                @csrf
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama">Nama <span style="color: red">*</span></label>
                            <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="username">Username <span style="color: red">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="jabatan">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" id="jabatan" placeholder="jabatan">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password">Password <span style="color: red">*</span></label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="id_akses">Akses <span style="color: red">*</span></label>
                            <select id="id_akses" name="id_akses" class="form-control" required>
                                <option value="1">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="id_lokasi">Lokasi <span style="color: red">*</span></label>
                            <select id="id_lokasi" name="id_lokasi" class="form-control" required>
                                @foreach ($lokasi as $l)
                                    <option value="{{ $l->id}}">{{ $l->nama}}|</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-end">
                            <a href="/pengguna" class="btn btn-danger mr-3">Batal</a>
                            <button class="btn btn-success" type="submit">Simpan</button>
                        </div>
                    </div>
                </form>
            </form>
        </div>
    </div>
@endsection
