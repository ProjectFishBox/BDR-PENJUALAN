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
                                <option value="{{ $b->id}}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek}}>{{ $b->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="harga">Harga <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="harga"  placeholder="Harga" name="harga" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang"  name="kode_barang" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="untung">Untung <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="untung" name="untung" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="merek">Merek <span style="color: red">*</span></label>
                        <select id="merek" class="form-control" name="merek" required>
                            <option value="">Pilih Barang</option>
                        </select>
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
                        <a href="/setharga" class="btn btn-danger mr-3">Batal</a>
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
        const namaBarangSelect = document.getElementById('nama_barang');
        const hargaInput = document.getElementById('harga');
        const kodeBarangInput = document.getElementById('kode_barang');
        const merekInput = document.getElementById('merek');

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        namaBarangSelect.addEventListener('change', function () {
            const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga');
            const kodeBarang = selectedOption.getAttribute('data-kode');
            const  merek= selectedOption.getAttribute('data-merek');

            hargaInput.value = harga ? formatNumber(harga) : '';
            kodeBarangInput.value = kodeBarang  ? kodeBarang  : '';
            merekInput.value = merek  ? merek  : '';

            merekSelect.innerHTML = '<option value="">Pilih Merek</option>';

        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hargaInput = document.getElementById('harga');
        const untungInput = document.getElementById('untung');
        const hargaJualInput = document.getElementById('harga_jual');

        function hitungHargaJual() {

            const harga = parseFloat(hargaInput.value) || 0;
            const untung = parseFloat(untungInput.value) || 0;

            const hargaJual = harga + untung;
            hargaJualInput.value = hargaJual;
        }

        hargaInput.addEventListener('input', hitungHargaJual);
        untungInput.addEventListener('input', hitungHargaJual);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const untungInput = document.getElementById('untung');
        const hargaInput = document.getElementById('harga');
        const hargaJualInput = document.getElementById('harga_jual');

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        function removeThousandSeparator(value) {
            return value.replace(/\./g, '');
        }
        [hargaInput, untungInput].forEach(input => {
            input.addEventListener('input', function () {
                const rawValue = removeThousandSeparator(input.value);
                const formattedValue = formatNumber(rawValue);
                input.value = formattedValue;
            });
        });


        function hitungHargaJual() {
            const harga = parseFloat(removeThousandSeparator(hargaInput.value)) || 0;
            const untung = parseFloat(removeThousandSeparator(untungInput.value)) || 0;
            const hargaJual = harga + untung;
            hargaJualInput.value = formatNumber(hargaJual);
        }

        hargaInput.addEventListener('input', hitungHargaJual);
        untungInput.addEventListener('input', hitungHargaJual);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const namaBarangSelect = document.getElementById('nama_barang');
    const merekSelect = document.getElementById('merek');
    const semuaOption = [...namaBarangSelect.options];

    namaBarangSelect.addEventListener('change', function () {
        const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
        const kodeBarang = selectedOption.getAttribute('data-kode');

        merekSelect.innerHTML = '<option value="">Pilih Merek</option>';

        semuaOption.forEach(option => {
            if (option.getAttribute('data-kode') === kodeBarang) {
                const merek = option.getAttribute('data-merek');
                const merekOption = document.createElement('option');
                merekOption.value = merek;
                merekOption.textContent = merek;
                merekSelect.appendChild(merekOption);
            }
        });
    });
});

</script>

@endpush
