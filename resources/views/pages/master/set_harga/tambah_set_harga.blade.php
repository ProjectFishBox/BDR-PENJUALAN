@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>
            <form action="{{ route('tambah-setharga')}}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama_barang">Barang <span style="color: red">*</span></label>
                        <select id="nama_barang" class="form-control" name="nama_barang" required>
                            <option value="">Pilih Barang</option>
                            @foreach ($barang as $b)
                                <option value="{{ $b->id}}">{{ $b->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="harga">Harga <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="harga" placeholder="Harga" name="harga" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="untung">Untung <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="untung" name="untung" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="merek">Merek <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="merek" name="merek">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="harga_jual">Harga Jual <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="harga_jual" name="harga_jual" required>
                    </div>
                </div>
                <div class="form-group d-flex align-items-center">
                    <div class="switch m-r-10">
                        <input type="checkbox" id="status" name="status">
                        <label for="status"></label>
                    </div>
                    <label>Status</label>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end">
                        <a href="/pengguna" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
