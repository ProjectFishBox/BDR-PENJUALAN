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

        <div class="m-t-25">
            <div class="table-responsive">
                <table class="table table-bordered table-hover data-table" id="data-table">
                    <thead>
                        <tr>
                            <th scope="col" style="text-align: center;">No</th>
                            <th scope="col" style="text-align: center;">Kode Barang</th>
                            <th scope="col" style="text-align: center;">Nama Barang</th>
                            <th scope="col" style="text-align: center;">Tanggal Jual</th>
                            <th scope="col" style="text-align: center;">Jumlah</th>
                            <th scope="col" style="text-align: center;">Harga Beli</th>
                            <th scope="col" style="text-align: center;">Harga Jual</th>
                            <th scope="col" style="text-align: center;">Total Beli</th>
                            <th scope="col" style="text-align: center;">Total Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                {{-- <div id="total-penjualan" class="mt-3" style="font-weight: bold;">
                    Total Penjualan: <span id="total-jumlah">0</span>
                </div> --}}

                <hr style="border-top: 2px solid #000; margin: 20px 0;">

                <div class="total-summary" style="font-weight: bold;">
                    <table style="width: 40%; border-collapse: collapse; border: none;">
                        <tr>
                            <td style="text-align: left;">Total Penjualan</td>
                            <td id="total-penjualan" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Total Diskon Produk</td>
                            <td id="total-diskon-produk" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Total Diskon Nota</td>
                            <td id="total-diskon-nota" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Total Pengeluaran</td>
                            <td id="total-pengeluaran" style="text-align: center;"></td>
                        </tr>
                    </table>
                </div>

                {{-- <hr style="border-top: 2px solid #000; width: 40px; margin: 10px auto;"> --}}

                <div class="total-summary" style="font-weight: bold;">
                    <table style="width: 40%; border-collapse: collapse; border: none;">
                        <tr>
                            <td style="text-align: right;">Total Transfer</td>
                            <td id="total-transfer " style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Modal Usaha</td>
                            <td id="modal-usaha" style="text-align: center;"></td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

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
                    var totalPengeluaran = 0;
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
                        totalPengeluaran += parseInt(item.total_pembelian)
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

                    $('#total-penjualan').text(totalPenjualan.toLocaleString('id-ID'));
                    $('#total-diskon-produk').text(totalDiskon.toLocaleString('id-ID'));
                    $('#total-diskon-nota').text(totalDiskonNota.toLocaleString('id-ID'));
                    // $('#total-pengeluaran').text(totalPengeluaran.toLocaleString('id-ID'));
                    $('#modal-usaha').text(totalPembelian.toLocaleString('id-ID'));
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


    });
</script>

@endpush
