@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <form action="{{ route('tambah-pelanggan')}}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama">Nama  <span style="color: red">*</span></label>
                        <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="telepon">Telepon  <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="telepon" name="telepon" placeholder="telepon" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="alamat">Alamat  <span style="color: red">*</span></label>
                        <input type="text" name="alamat" class="form-control" id="alamat" placeholder="alamat" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="Fax">Fax</label>
                        <input type="text" class="form-control" name="fax" id="fax" placeholder="Fax">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="id_kota">Kota  <span style="color: red">*</span></label>
                        <select id="id_kota" name="id_kota" class="id_kota form-control" required>
                            <option value="">Pilih Kota</option>
                            @foreach ($kota as $k)
                                <option value="{{ $k->id }}">{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="kode_pos">Kode Pos</label>
                        <input type="text" class="form-control" name="kode_pos" id="kode_pos" placeholder="Kode Pos">

                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end">
                        <a href="/pelanggan" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@component('components.aset_datatable.aset_select2')@endcomponent

@push('js')

<script>
    $('.lokasi').select2({
        width: '100%',
        placeholder: 'Pilih Lokasi',
    });

    $('.id_kota').select2({
        width: '100%',
        placeholder: 'Pilih Kota',
    });

</script>
@endpush
