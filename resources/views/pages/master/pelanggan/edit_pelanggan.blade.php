@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <form action="{{ route('update-pelanggan', $pelanggan->id) }}" method="POST">
                @csrf
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama" value="{{ $pelanggan->nama }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="telepon">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" placeholder="telepon" value="{{ $pelanggan->telepon }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="alamat">Alamat</label>
                            <input type="text" name="alamat" class="form-control" id="alamat" placeholder="alamat" value="{{ $pelanggan->alamat }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="Fax">Fax</label>
                            <input type="text" class="form-control" name="fax" id="fax" placeholder="Fax" value="{{ $pelanggan->fax }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="id_kota">Kota</label>
                            <select id="id_kota" name="id_kota" class="form-control">
                                <option value="1">Kota Makassar</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="id_lokasi">Lokasi</label>
                            <select id="id_lokasi" name="id_lokasi" class="form-control">
                                @foreach ($lokasi as $l)
                                    <option value="{{ $l->id }}" {{ $l->id == $pelanggan->id_lokasi ? 'selected' : '' }}>
                                        {{ $l->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="kode_pos">Kode Pos</label>
                            <input type="text" class="form-control" name="kode_pos" id="kode_pos" placeholder="kode_pos" value="{{ $pelanggan->kode_pos }}">
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
