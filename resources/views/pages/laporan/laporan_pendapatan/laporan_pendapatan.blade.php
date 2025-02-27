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
                    <option value="all">Semua Lokasi</option>
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

        <div class="mt-4">
            <div class="table-responsive">
                <table class="w-100 table table-bordered table-hover text-center data-table" id="data-table">
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

            <hr class="my-3" style="border: 1px solid">
            {{-- class="text-end" --}}

            <div class="total-summary">
                <table>
                    <tr>
                        <td>Total Penjualan</td>
                        <td id="total-penjualan" class="text-end" style="text-align: end;"></td>
                    </tr>
                    <tr>
                        <td>Total Diskon Produk</td>
                        <td id="total-diskon-produk" style="text-align: end;"></td>
                    </tr>
                    <tr>
                        <td>Total Diskon Nota</td>
                        <td id="total-diskon-nota" style="text-align: end;"></td>
                    </tr>
                    <tr>
                        <td>Total Pengeluaran</td>
                        <td id="total-pengeluaran" style="text-align: end;"></td>
                    </tr>
                    <tr>
                        <td class="right-text bold-text" style="text-align: center; font-weight:bold">Total Transfer</td>
                        <td class="bold-text" id="total-transfer" style="border-top: 2px solid black; padding-top: 5px; text-align: end;"></td>
                    </tr>
                    <tr>
                        <td>Modal Usaha</td>
                        <td id="modal-usaha" style="text-align: end;"></td>
                    </tr>
                    <tr>
                        <td class="right-text bold-text" style="text-align: center; font-weight:bold">Laba Bersih</td>
                        <td class="bold-text" id="laba-bersih" style="border-top: 2px solid black; padding-top: 5px; text-align: end;"></td>
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
                format: 'DD-MM-YYYY'
            },
            autoUpdateInput: false
        });

        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
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
                    var jumlahPenjualan = 0;
                    var totalDiskon = 0;
                    var totalDiskonNota = 0;
                    var totalPenjualan = 0;
                    var modalUsaha = 0;
                    var totalPengeluaran = 0;
                    var uniquePenjualan = {};
                    var uniquePengeluaran = {};

                    $.each(data, function(index, item) {
                        if (!uniquePenjualan[item.id_penjualan]) {
                            uniquePenjualan[item.id_penjualan] = item.diskon_nota;
                        }

                        if (!uniquePengeluaran[item.tanggal]) {
                            uniquePengeluaran[item.tanggal] = parseInt(item.total_pengeluaran);
                        }

                        var total_pembelian_detail_barang = item.total_jumlah * item.harga_pembelian
                        var formattedDate = moment(item.tanggal).format('DD-MM-YYYY');

                        var row = '<tr>' +
                            '<td>' + overallIndex + '</td>' +
                            '<td>' + item.kode_barang + '</td>' +
                            '<td>' + item.nama_barang + '</td>' +
                            '<td>' + formattedDate + '</td>' +
                            '<td>' + item.total_jumlah + '</td>' +
                            '<td>' + Math.floor(item.harga_pembelian).toLocaleString('id-ID') + '</td>' +
                            '<td>' + Math.floor(item.harga).toLocaleString('id-ID') + '</td>' +
                            '<td>' + Math.floor(total_pembelian_detail_barang).toLocaleString('id-ID') + '</td>' +
                            '<td>' + Math.floor(item.total_jual).toLocaleString('id-ID') + '</td>' +
                            '</tr>';
                        tbody.append(row);

                        jumlahPenjualan += parseInt(item.total_jumlah);
                        totalDiskon += parseInt(item.total_diskon_barang);
                        totalPenjualan += parseInt(item.total_jual);
                        modalUsaha += parseInt(total_pembelian_detail_barang);
                        overallIndex++;
                    });

                    $.each(uniquePengeluaran, function(tanggal, pengeluaran) {
                        totalPengeluaran += pengeluaran;
                    });

                    $.each(uniquePenjualan, function(id, diskon) {
                        totalDiskonNota += parseInt(diskon);
                    });

                    var totalRow = '<tr>' +
                        '<td colspan="4" style="text-align: left;"><strong>Jumlah Penjualan:</strong></td>' +
                        '<td>' + jumlahPenjualan + '</td>' +
                        '</tr>';
                    tbody.append(totalRow);

                    var totalTransfer = (totalPenjualan - (totalPengeluaran + totalDiskonNota + totalDiskon));
                    var labaBersih = totalTransfer - modalUsaha;

                    $('#total-penjualan').text('Rp ' + totalPenjualan.toLocaleString('id-ID'));
                    $('#total-diskon-produk').text('Rp ' + totalDiskon.toLocaleString('id-ID'));
                    $('#total-diskon-nota').text('Rp ' + totalDiskonNota.toLocaleString('id-ID'));
                    $('#total-pengeluaran').text('Rp ' + totalPengeluaran.toLocaleString('id-ID'));
                    $('#modal-usaha').text('Rp ' + modalUsaha.toLocaleString('id-ID'));
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
