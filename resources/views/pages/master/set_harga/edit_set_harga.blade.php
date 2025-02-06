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
                        <select id="nama_barang" class="select2 form-control" name="nama_barang" required>
                            <option value="">Pilih Barang</option>
                            @foreach ($barang as $b)
                                <option value="{{ $b->id}}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek}} {{ $b->id == $setharga->id_barang ? 'selected' : '' }}>({{$b->kode_barang}}) {{ $b->nama}}</option>
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

@component('components.aset_datatable.aset_select2')@endcomponent


@push('js')

<script>
    $('.select2').select2({
        width: '100%',
        placeholder: 'Pilih Barang',
    });

    $('#nama_barang').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var kodeBarang = selectedOption.data('kode');
        var namaBarang = selectedOption.data('nama');

        $('#harga').val('');

        $('#kode_barang').val(kodeBarang);
        var filteredMerek = @json($barang);

        $('#merek').empty().append('<option value="">Pilih Merek</option>');

        filteredMerek.forEach(function(item) {
            if (item.kode_barang === kodeBarang) {
                $('#merek').append('<option value="' + item.merek + '" data-harga="' + item.harga + '">' + item.merek + '</option>');
            }
        });

        $('#merek').select2({
            width: '100%',
            placeholder: 'Pilih Merek'
        });
    });

    $('#merek').on('change', function() {
        var selectedMerek = $(this).find('option:selected');
        var harga = formatNumber(selectedMerek.data('harga'));
        $('#harga').val(harga);
    });

    function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
    }
</script>

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

        function removeNonNumeric(value) {
            return value.replace(/[^0-9]/g, '');
        }

        function preventNonNumericInput(event) {
            if (!/[0-9]/.test(event.key) && event.key !== 'Backspace' && event.key !== 'Delete' && event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
                event.preventDefault();
            }
        }

        function hitungHargaJual() {
            const harga = parseInt(removeNonNumeric(hargaInput.value)) || 0;
            const untung = parseInt(removeNonNumeric(untungInput.value)) || 0;
            const hargaJual = harga + untung;
            hargaJualInput.value = formatNumber(hargaJual);
        }

        function formatInputsOnLoad() {
            [hargaInput, untungInput, hargaJualInput].forEach(input => {
                if (input.value) {
                    input.value = formatNumber(removeNonNumeric(input.value));
                }
            });
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

        [hargaInput, untungInput, hargaJualInput].forEach(input => {
            input.addEventListener('keypress', preventNonNumericInput);
            input.addEventListener('input', function () {
                const rawValue = removeNonNumeric(input.value);
                const formattedValue = formatNumber(parseInt(rawValue || 0));
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
