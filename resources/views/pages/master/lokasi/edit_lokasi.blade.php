@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <form action="{{ route('update-lokasi', $lokasi->id) }}" method="POST">
                @csrf
                <div class="form-group mt-5">
                    <label for="namalokasi">Nama Lokasi <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="nama" id="namalokasi" placeholder="Nama Lokasi" value="{{ old('nama', $lokasi->nama ?? '') }}" required>
                </div>
                <div class="form-group">
                    <div class="d-flex">
                        <a href="/lokasi" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
