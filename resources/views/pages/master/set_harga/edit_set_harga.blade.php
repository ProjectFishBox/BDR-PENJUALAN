@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>
            <form action="{{ route('update-setharga', $setharga->id) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama_barang">Barang <span style="color: red">*</span></label>
                        <select id="nama_barang" class="form-control" name="nama_barang" required>
                            <option value="">Pilih Barang</option>
                            @foreach ($barang as $b)
                                <option value="{{ $b->id}}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek}} {{ $b->id == $setharga->id_barang ? 'selected' : '' }}>{{ $b->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="harga">Harga <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="harga" readonly placeholder="Harga" name="harga" value="{{ $setharga->harga}}" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" readonly name="kode_barang" value="{{ $setharga->kode_barang}}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="untung">Untung <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="untung" name="untung" required value="{{ $setharga->untung}}">
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
                        <input type="text" class="form-control" id="harga_jual" name="harga_jual" required value="{{ $setharga->harga_jual}}">
                    </div>
                </div>
                <div class="form-group d-flex align-items-center">
                    <div class="switch m-r-10">
                        <input type="checkbox" id="status" name="status"  {{ $setharga->status === 'Aktif' ? 'checked' : '' }}>
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
        const untungInput = document.getElementById('untung');
        const hargaJualInput = document.getElementById('harga_jual');
        const merekSelect = document.getElementById('merek');

        const semuaOption = [...namaBarangSelect.options];
        const selectedMerek = '{{ $setharga->merek }}';

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        function removeThousandSeparator(value) {
            return value.replace(/\./g, '');
        }

        function hitungHargaJual() {
            const harga = parseFloat(removeThousandSeparator(hargaInput.value)) || 0;
            const untung = parseFloat(removeThousandSeparator(untungInput.value)) || 0;
            const hargaJual = harga + untung;
            hargaJualInput.value = formatNumber(hargaJual);
        }

        function formatInputsOnLoad() {
            if (hargaInput.value) {
                hargaInput.value = formatNumber(removeThousandSeparator(hargaInput.value));
            }
            if (untungInput.value) {
                untungInput.value = formatNumber(removeThousandSeparator(untungInput.value));
            }
            if (hargaJualInput.value) {
                hargaJualInput.value = formatNumber(removeThousandSeparator(hargaJualInput.value));
            }
        }

        function isiMerek(kodeBarang, merekTerpilih = null) {
            merekSelect.innerHTML = '<option value="">Pilih Merek</option>';
            semuaOption.forEach(option => {
                if (option.getAttribute('data-kode') === kodeBarang) {
                    const merek = option.getAttribute('data-merek');
                    const merekOption = document.createElement('option');
                    merekOption.value = merek;
                    merekOption.textContent = merek;

                    if (merek === merekTerpilih) {
                        merekOption.selected = true;
                    }

                    merekSelect.appendChild(merekOption);
                }
            });
        }

        namaBarangSelect.addEventListener('change', function () {
            const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga');
            const kodeBarang = selectedOption.getAttribute('data-kode');
            const merek = selectedOption.getAttribute('data-merek');

            hargaInput.value = harga ? formatNumber(harga) : '';
            kodeBarangInput.value = kodeBarang ? kodeBarang : '';
            merekInput.value = merek ? merek : '';

            untungInput.value = '0';
            hargaJualInput.value = '0';
            isiMerek(kodeBarang);
        });

        [hargaInput, untungInput].forEach(input => {
            input.addEventListener('input', function () {
                const rawValue = removeThousandSeparator(input.value);
                const formattedValue = formatNumber(rawValue);
                input.value = formattedValue;
                hitungHargaJual();
            });
        });

        formatInputsOnLoad();
        const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
        if (selectedOption) {
            const kodeBarang = selectedOption.getAttribute('data-kode');
            isiMerek(kodeBarang, selectedMerek);
        }
    });
</script>



@endpush
