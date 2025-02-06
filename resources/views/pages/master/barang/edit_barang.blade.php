@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <form action="{{ route('update-barang', $barang->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="kode_barang">Kode Barang <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="kode_barang" placeholder="Kode Barang" name="kode_barang" required value="{{ $barang->kode_barang }}">
                </div>
                <div class="form-group">
                    <label for="nama">Nama Barang <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="nama" placeholder="Nama Barang" name="nama" required value="{{ $barang->nama }}">
                </div>
                <div class="form-group">
                    <label for="merek">Merek <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="merek" placeholder="Merek Barang" name="merek" required value="{{ $barang->merek }}">
                </div>
                <div class="form-group">
                    <label for="harga">harga <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="harga" placeholder="Harga Barang" name="harga" required value="{{ number_format($barang->harga, 0, ',', '.') }}">
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end">
                        <a href="/barang" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hargaInput = document.getElementById('harga');

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        function removeNonNumeric(value) {
            return value.replace(/[^0-9]/g, '');
        }

        hargaInput.addEventListener('keypress', function (e) {
            if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
                e.preventDefault();
            }
        });

        hargaInput.addEventListener('input', function () {
            const rawValue = removeNonNumeric(hargaInput.value);
            const formattedValue = formatNumber(parseInt(rawValue || 0));
            hargaInput.value = formattedValue;
        });
    });
</script>

@endpush
