@extends('components._partials.layout')

@section('content')

<div class="card">
    <div class="card-body">
        <h4 class="mb-4">{{ $title }}</h4>

        <form id="filter-form">

            <div class="form-group col-6">
                <label for="merek">Periode</label>
                <input type="text" class="form-control" id="daterange" name="daterange" placeholder="Pilih Tanggal" value="{{ request()->get('daterange') }}" />
            </div>


            <div class="form-group col-6">
                <label for="lokasi">Lokasi</label>
                <select id="lokasi" class="select2 lokasi" name="lokasi">
                    <option value="">Pilih Lokasi</option>
                    @foreach ($lokasi as $l)
                        <option value="{{ $l->id }}">
                            {{ $l->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-start gap-10">
                    <button class="btn btn-primary mr-4 btn-preview" id="btn-preview">Preview</button>
                    <button class="btn btn-danger mr-4 btn-export-pdf" id="btn-export-pdf">Export PDF</button>
                    <button class="btn btn-success btn-export-excel" id="btn-export-excel">Export Excel</button>
                </div>
            </div>
        </form>

        <div class="container mt-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center data-table" id="data-table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Tanggal Jual</th>
                            <th>Jumlah</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Total Beli</th>
                            <th>Total Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            {{-- <p class="fw-bold text-center mt-3">Jumlah Penjualan: 3 Ball</p> --}}

            <hr class="my-3">
            {{-- class="text-end" --}}

            <div class="total-summary">
                <table>
                    <tr>
                        <td>Total Penjualan</td>
                        <td id="total-penjualan" class="text-end"></td>
                    </tr>
                    <tr>
                        <td>Total Diskon Produk</td>
                        <td id="total-diskon-produk" class="text-end"></td>
                    </tr>
                    <tr>
                        <td>Total Diskon Nota</td>
                        <td id="total-diskon-nota" class="text-end"></td>
                    </tr>
                    <tr>
                        <td>Total Pengeluaran</td>
                        <td id="total-pengeluaran" class="text-end"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="underline"></td>
                    </tr>
                    <tr class="bold">
                        <td>Total Transfer</td>
                        <td id="total-transfer"></td>
                    </tr>
                    <tr>
                        <td>Modal Usaha</td>
                        <td id="modal-usaha" class="text-end"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="underline"></td>
                    </tr>
                    <tr class="bold">
                        <td>Laba Bersih</td>
                        <td id="laba-bersih" class="text-end"></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection

@component('components.aset_datatable.aset_datatable')@endcomponent

@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>

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
    $('.lokasi').select2({
        width: '100%',
        placeholder: 'Pilih Lokasi',
    });
</script>

<script>
    $(document).ready(function() {
        $('#btn-preview').on('click', function(e) {
            e.preventDefault();
            var daterange = $('#daterange').val()
            var lokasi = $('#lokasi').val();

            $.ajax({
                url: '{{ route("pendapatan") }}',
                method: 'GET',
                data: {
                    daterange: daterange,
                    lokasi: lokasi,
                },
                success: function(data) {
                    var tbody = $('#data-table tbody');
                    tbody.empty();
                    var overallIndex = 1;
                    var totalJumlah = 0;
                    var totalDiskon = 0;
                    var totalDiskonNota = 0;
                    var totalPenjualan = 0;
                    var totalPembelian = 0;
                    var uniquePenjualan = {};

                    $.each(data, function(index, item) {
                        if (!uniquePenjualan[item.id_penjualan]) {
                            uniquePenjualan[item.id_penjualan] = item.diskon_nota;
                        }

                        var row = '<tr>' +
                            '<td>' + overallIndex + '</td>' +
                            '<td>' + item.kode_barang + '</td>' +
                            '<td>' + item.nama_barang + '</td>' +
                            '<td>' + item.tanggal + '</td>' +
                            '<td>' + item.total_terjual + '</td>' +
                            '<td>' + Math.floor(item.harga_pembelian).toLocaleString('id-ID') + '</td>' +
                            '<td>' + Math.floor(item.harga_penjualan).toLocaleString('id-ID') + '</td>' +
                            '<td>' + Math.floor(item.total_pembelian).toLocaleString('id-ID') + '</td>' +
                            '<td>' + Math.floor(item.total_penjualan).toLocaleString('id-ID') + '</td>' +
                            '</tr>';
                        tbody.append(row);

                        totalJumlah += parseInt(item.total_terjual);
                        totalDiskon += parseInt(item.diskon_barang);
                        totalPenjualan += parseInt(item.total_penjualan)
                        totalPembelian += parseInt(item.total_pembelian)

                        overallIndex++;
                    });

                    $.each(uniquePenjualan, function(id, diskon) {
                        totalDiskonNota += parseInt(diskon);
                    });

                    var totalRow = '<tr>' +
                        '<td colspan="4" style="text-align: left;"><strong>Total Penjualan:</strong></td>' +
                        '<td>' + totalJumlah + '</td>' +
                        '</tr>';
                    tbody.append(totalRow);

                    var totalPengeluaran = totalDiskon + totalDiskonNota;
                    var totalTransfer  = (totalPenjualan - (totalPengeluaran + totalDiskonNota +totalDiskon));
                    var labaBersih = totalTransfer - totalPembelian;

                    $('#total-penjualan').text('Rp ' + totalPenjualan.toLocaleString('id-ID'));
                    $('#total-diskon-produk').text('Rp ' + totalDiskon.toLocaleString('id-ID'));
                    $('#total-diskon-nota').text('Rp ' + totalDiskonNota.toLocaleString('id-ID'));
                    $('#total-pengeluaran').text('Rp ' + totalPengeluaran.toLocaleString('id-ID'));
                    $('#modal-usaha').text('Rp ' + totalPembelian.toLocaleString('id-ID'));
                    $('#total-transfer').text('Rp ' + totalTransfer.toLocaleString('id-ID'));
                    $('#laba-bersih').text('Rp ' + labaBersih.toLocaleString('id-ID'));

                }
            });
        });



        $('#btn-export-pdf').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let daterange = $('#daterange').val();
            var lokasi = $('#lokasi').val();
            let formData = $('#filter-form').serialize();

            if (daterange) {
                formData += '&daterange=' + encodeURIComponent(daterange);
            }

            let url = "{{ route('laporan-pendapatan.exportpdf') }}?" + formData;

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
            })
        });

        $('#btn-export-excel').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let daterange = $('#daterange').val();
            var lokasi = $('#lokasi').val();
            let formData = $('#filter-form').serialize();

            if (daterange) {
                formData += '&daterange=' + encodeURIComponent(daterange);
            }

            let url = "{{ route('laporan-pendapatan.exportexcel') }}?" + formData;

            Swal.fire({
                title: 'Apakah kamu ingin mengexport data ini?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, Export sekarang!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(url, '_blank');
                    Swal.fire({
                        title: 'Sedang Mengexport!',
                        text: 'File Excel sedang diproses...',
                        icon: 'success',
                        timer: 2000
                    });
                }
            })
        });



    });
</script>

@endpush
