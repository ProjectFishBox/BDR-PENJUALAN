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
                        <select id="nama_barang" class="select2" name="nama_barang" required >
                            <option value="">Pilih Barang</option>
                            @foreach ($barang->unique('kode_barang') as $b)
                                <option value="{{ $b->id }}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-nama="{{ $b->nama }}">
                                    ({{$b->kode_barang}}) {{ $b->nama }}
                                </option>
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
                            <option value="">Pilih Merek</option>
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
        $('#harga').attr('readonly', true);

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
        $('#harga').attr('readonly', false);
    });

    function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
    }
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

        [hargaInput, untungInput, hargaJualInput].forEach(input => {
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

        function hitungUntung() {
            const hargaJual = parseFloat(removeThousandSeparator(hargaJualInput.value)) || 0;
            const harga = parseFloat(removeThousandSeparator(hargaInput.value)) || 0;
            let untung = hargaJual - harga;

            if (untung < 0) {
                untung = 0;
            }

            untungInput.value = formatNumber(untung);
        }

        hargaInput.addEventListener('input', hitungHargaJual);
        untungInput.addEventListener('input', hitungHargaJual);
        hargaJualInput.addEventListener('input', hitungUntung);
    });
</script>



@endpush
