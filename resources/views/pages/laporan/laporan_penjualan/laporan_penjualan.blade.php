@extends('components._partials.layout')


@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">{{ $title }}</h4>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="daterange">Periode</label>
                    <input type="text" class="form-control" id="daterange" name="daterange" placeholder="Pilih Tanggal" value="{{ request()->get('daterange') }}" />
                </div>
                <div class="form-group col-md-6">
                    <label for="pelanggan">Pelanggan</label>
                    <select id="pelanggan" class="pelanggan" name="pelanggan">
                        <option value="">Pilih Pelanggan</option>
                        @foreach ($pelanggan as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Lokasi</label>
                    <select id="lokasi" class="lokasi" name="lokasi">
                        <option value="">Pilih Lokasi</option>
                        @foreach ($lokasi as $l)
                            <option value="{{ $l->id }}">
                                {{ $l->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputPassword4">Barang</label>
                    <select id="barang" class="barang" name="barang">
                        <option value="">Pilih Barang</option>
                        @foreach ($barang->unique('kode_barang') as $b)
                            <option value="{{ $b->id }}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-nama="{{ $b->nama }}">
                                ({{$b->kode_barang}}) {{ $b->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4">No Nota</label>
                    <select id="no_nota" class="no_nota" name="no_nota">
                        <option value="">Pilih Nota</option>
                        @foreach ($noNota as $n)
                            <option value="{{ $n->no_nota }}">
                                {{ $n->no_nota }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputPassword4">Merek</label>
                    <select id="merek" class="merek form-control" name="merek" required>
                        <option value="">Pilih Merek</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-start gap-10">
                    <button class="btn btn-primary mr-4 btn-preview" id="btn-preview">Preview</button>
                    <button class="btn btn-danger mr-4 btn-export-pdf" id="btn-export-pdf">Export PDF</button>
                    <button class="btn btn-success btn-export-excel" id="btn-export-excel">Export Excel</button>
                </div>
            </div>

            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover data-table" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center;">No</th>
                                <th scope="col" style="text-align: center;">No. Nota</th>
                                <th scope="col" style="text-align: center;">Tanggal</th>
                                <th scope="col" style="text-align: center;">Nama Pelanggan</th>
                                <th scope="col" style="text-align: center;">Kode Barang</th>
                                <th scope="col" style="text-align: center;">Nama Barang</th>
                                <th scope="col" style="text-align: center;">Merek</th>
                                <th scope="col" style="text-align: center;">Harga</th>
                                <th scope="col" style="text-align: center;">Diskon Produk</th>
                                <th scope="col" style="text-align: center;">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade bd-example-modal" style="display: none;" id="previewpembelian" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"> </div>
@endsection

@component('components.aset_datatable.aset_datatable')@endcomponent


@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function() {
        $('#daterange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoUpdateInput: false
        });

        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>

<script>
    $('.barang').select2({
        width: '100%',
        placeholder: 'Pilih Barang',
    });

    $('.lokasi').select2({
        width: '100%',
        placeholder: 'Pilih Lokasi',
    });

    $('.merek').select2({
        width: '100%',
        placeholder: 'Pilih Merek'
    });

    $('.pelanggan').select2({
        width: '100%',
        placeholder: 'Pilih Pelanggan'
    });

    $('.no_nota').select2({
        width: '100%',
        placeholder: 'Pilih No Nota'
    });


    $('#barang').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var kodeBarang = selectedOption.data('kode');

        var filteredMerek = @json($barang);

        $('#merek').empty().append('<option value="">Pilih Merek</option>');

        filteredMerek.forEach(function(item) {
            if (item.kode_barang === kodeBarang) {
                $('#merek').append('<option value="' + item.merek + '" data-harga="' + item.harga + '">' + item.merek + '</option>');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#btn-preview').on('click', function(e) {
            e.preventDefault();
            var daterange = $('#daterange').val();
            var pelanggan = $('#pelanggan').val();
            var lokasi = $('#lokasi').val();
            var barang = $('#barang').val();
            var no_nota = $('#no_nota').val();

            $.ajax({
                url: '{{ route("laporan-penjualan") }}',
                method: 'GET',
                data: {
                    daterange: daterange,
                    pelanggan: pelanggan,
                    lokasi: lokasi,
                    barang: barang,
                    no_nota: no_nota
                },
                success: function(data) {
                    var tbody = $('#data-table tbody');
                    tbody.empty();
                    var totalDiskon = 0;
                    var totalJumlah = 0;
                    var overallIndex = 1;

                    $.each(data, function(index, item) {
                        $.each(item.detail, function(detailIndex, detail) {
                            var row = '<tr>' +
                                '<td>' + overallIndex + '</td>' +
                                '<td>' + item.no_nota + '</td>' +
                                '<td>' + item.tanggal + '</td>' +
                                '<td>' + item.nama_pelanggan + '</td>' +
                                '<td>' + detail.kode_barang + '</td>' +
                                '<td>' + detail.nama_barang + '</td>' +
                                '<td>' + detail.merek + '</td>' +
                                '<td>' + Math.floor(detail.harga).toLocaleString('id-ID') + '</td>' +
                                '<td>' + Math.floor(detail.diskon_barang).toLocaleString('id-ID') + '</td>' +
                                '<td>' + detail.jumlah + '</td>' +
                                '</tr>';
                            tbody.append(row);

                            totalDiskon += parseFloat(detail.diskon_barang);
                            totalJumlah += parseInt(detail.jumlah);
                            overallIndex++;
                        });
                    });

                    var totalRow = '<tr>' +
                        '<td colspan="8" style="text-align: right;"><strong>Total:</strong></td>' +
                        '<td>' + Math.floor(totalDiskon).toLocaleString('id-ID') + '</td>' +
                        '<td>' + totalJumlah + '</td>' +
                        '</tr>';
                    tbody.append(totalRow);
                }
            });
        });

        $('#btn-export-pdf').on('click', function(e) {
            e.preventDefault();
            var daterange = $('#daterange').val();
            var pelanggan = $('#pelanggan').val();
            var lokasi = $('#lokasi').val();
            var barang = $('#barang').val();
            var no_nota = $('#no_nota').val();

            var url = '{{ route("laporan-penjualan.exportpdf") }}' + '?daterange=' + daterange + '&pelanggan=' + pelanggan + '&lokasi=' + lokasi + '&barang=' + barang + '&no_nota=' + no_nota;

            Swal.fire({
                title: 'Apakah kamu ingin mencetak data ini?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, Cetak sekarang!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(url, '_blank');
                    Swal.fire({
                        title: 'Sedang Mencetak!',
                        text: 'File PDF sedang diproses...',
                        icon: 'success',
                        timer: 2000
                    });
                }
            });
        });

        $('#btn-export-excel').on('click', function(e) {
            e.preventDefault();
            var daterange = $('#daterange').val();
            var pelanggan = $('#pelanggan').val();
            var lokasi = $('#lokasi').val();
            var barang = $('#barang').val();
            var no_nota = $('#no_nota').val();

            var url = '{{ route("laporan-penjualan.exportexcel") }}' + '?daterange=' + daterange + '&pelanggan=' + pelanggan + '&lokasi=' + lokasi + '&barang=' + barang + '&no_nota=' + no_nota;


            Swal.fire({
                title: 'Apakah kamu ingin mencetak data ini?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, Cetak sekarang!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(url, '_blank');
                    Swal.fire({
                        title: 'Sedang Mencetak!',
                        text: 'File PDF sedang diproses...',
                        icon: 'success',
                        timer: 2000
                    });
                }
            });
        });
    });
</script>

@endpush


