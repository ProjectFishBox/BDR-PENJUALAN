@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 style="padding: 25px">{{ $title }}</h2>

            <form action="{{ url()->current() }}" method="GET">
                <div class="d-flex align-items-center m-2 mt-3">
                    <div class="form-group col-md-3">
                        <label for="daterange">Periode</label>
                        <input type="text" class="form-control" id="daterange" name="daterange" placeholder="Pilih Tanggal"
                            value="{{ request()->get('daterange') }}" />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="text">Lokasi</label>
                        <select id="lokasi" class="lokasi form-control">
                            <option value="">Pilih Lokasi</option>
                            <option value="all">Semua Lokasi</option>
                            @foreach ($lokasi as $b)
                                <option value="{{ $b->id }}"
                                    {{ request()->get('lokasi') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputPassword4">Barang</label>
                        <select id="nama_barang" class="barang form-control">
                            <option value="">Pilih Barang</option>
                            <option value="all">Semua Barang</option>
                            @foreach ($barang->unique('kode_barang') as $b)
                                <option value="{{ $b->id }}" data-id="{{ $b->id }}"
                                    data-nama="{{ $b->nama }}" data-harga="{{ $b->harga }}"
                                    data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek }}>
                                    ({{ $b->kode_barang }}){{ $b->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputPassword4">Merek</label>
                        <select id="merek" class="merek form-control">
                            <option value="">Pilih Merek</option>
                        </select>
                    </div>
                    <button class="btn-detail btn btn-primary m-r-5 mt-2 mb-2 btn-filter" id="btn-filter">Filter</button>
                </div>
            </form>

            <div class="row" style="padding: 25px">
                <div class="col-md-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="m-b-0 ">Pembelian</p>
                            <div class="media align-items-center">
                                <div class="">
                                    <h1 class="m-b-0" style="font-size: 35px" id="total-pembelian">Rp 0</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="m-b-0 ">Pengeluaran</p>
                            <div class="media align-items-center">
                                <div class="">
                                    <h1 class="m-b-0" style="font-size: 35px" id="total-pengeluaran">Rp 0</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="m-b-0 ">Penjualan</p>
                            <div class="media align-items-center">
                                <div class="">
                                    <h1 class="m-b-0" style="font-size: 35px" id="total-penjualan">Rp 0</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row" style="padding: 25px">
                <div class="col-md-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="m-b-0 ">Stok Masuk</p>
                            <div class="media align-items-center">
                                <div class="">
                                    <h1 class="m-b-0" style="font-size: 35px" id="stok-masuk">0</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="m-b-0 ">Stok Keluar</p>
                            <div class="media align-items-center">
                                <div class="">
                                    <h1 class="m-b-0" style="font-size: 35px" id="stok-keluar">0</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="m-b-0 ">Sisa Stok</p>
                            <div class="media align-items-center">
                                <div class="">
                                    <h1 class="m-b-0" style="font-size: 35px" id="sisa-stok">0</h1>
                                    <small>Total Nilai Jual </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@component('components.aset_datatable.aset_select2')
@endcomponent

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .card h1 {
            font-size: clamp(20px, 2vw, 32px);
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }
    </style>
@endpush



@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(function() {
            $('#daterange').daterangepicker({
                locale: {
                    format: 'DD-MM-YYYY'
                },
                autoUpdateInput: false
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format(
                    'DD-MM-YYYY'));
            });

            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>

    <script>
        $('.lokasi').select2({
            width: '100%',
            placeholder: 'Pilih Lokasi',
        });

        $('.merek').select2({
            width: '100%',
            placeholder: 'Pilih Merek',
        });

        $('.barang').select2({
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
                    $('#merek').append('<option value="' + item.merek + '" data-harga="' + item.harga +
                        '">' + item.merek + '</option>');
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
        });

        $('#harga').on('input', function() {
            var value = $(this).val().replace(/[^\d]/g, '');
            $(this).val(formatNumber(value));
        });

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }
    </script>

<script>
    $(document).ready(function() {
        $('#btn-filter').on('click', function(e) {
            e.preventDefault();

            var lokasi = $('#lokasi').val();
            var merek = $('#merek').val();
            var barang = $('#nama_barang').val();
            var daterange = $('#daterange').val();


            console.log('Lokasi', lokasi);
            console.log('Merek' ,merek);
            console.log('barang' ,barang,);
            console.log('tanggal' ,daterange);

            $.ajax({
                url: '{{ route("dashboard") }}',
                method: 'GET',
                data: {
                    daterange: daterange,
                    lokasi: lokasi,
                    merek: merek,
                    barang: barang
                },
                success: function(response) {
                    $('#total-pembelian').text('Rp ' + response.total_pembelian.toLocaleString('id-ID'));
                    $('#total-penjualan').text('Rp ' + response.total_penjualan.toLocaleString('id-ID'));
                    $('#total-pengeluaran').text('Rp ' + response.total_pengeluaran.toLocaleString('id-ID'));
                    $('#stok-masuk').text(response.stok_masuk.toLocaleString('id-ID'));
                    $('#stok-keluar').text(response.stok_keluar.toLocaleString('id-ID'));
                    $('#sisa-stok').text(response.sisa_stok);
                }
            });
        });
    })

</script>

@endpush
