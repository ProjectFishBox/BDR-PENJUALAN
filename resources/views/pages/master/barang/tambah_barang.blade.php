@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <form action="{{ route('tambah-barang')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="kode_barang">Kode Barang <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="kode_barang" placeholder="Kode Barang" name="kode_barang" required>
                </div>
                <div class="form-group">
                    <label for="nama">Nama Barang <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="nama" placeholder="Nama Barang" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="merek">Merek <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="merek" placeholder="Merek Barang" name="merek" required>
                </div>
                <div class="form-group">
                    <label for="harga">harga <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="harga" placeholder="Harga Barang" name="harga" required>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end">
                        <a href="/lokasi" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
