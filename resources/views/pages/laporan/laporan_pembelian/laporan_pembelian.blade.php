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
                    <select id="lokasi" class="lokasi" name="lokasi">
                        <option value="">Pilih Lokasi</option>
                        <option value="all">Semua Lokasi</option>
                        @foreach ($lokasi as $l)
                            <option value="{{ $l->id }}">
                                {{ $l->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-6">
                    <label for="merek">Merek</label>
                    <select id="merek" class="merek form-control" name="merek" required>
                        <option value="">Pilih Merek</option>
                        <option value="all">Semua Merek</option>
                        @foreach ($barang->unique('merek') as $b)
                            <option value="{{$b->merek}}">{{ $b->merek}}</option>
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
                                <th scope="col" style="text-align: center;">No. Nota</th>
                                <th scope="col" style="text-align: center;">Tanggal</th>
                                <th scope="col" style="text-align: center;">Kode Barang</th>
                                <th scope="col" style="text-align: center;">Nama Barang</th>
                                <th scope="col" style="text-align: center;">Merek</th>
                                <th scope="col" style="text-align: center;">Harga Satuan</th>
                                <th scope="col" style="text-align: center;">Jumlah</th>
                                <th scope="col" style="text-align: center;">Total</th>
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

    $('.lokasi').select2({
        width: '100%',
        placeholder: 'Pilih Lokasi',
    });

    $('.merek').select2({
        width: '100%',
        placeholder: 'Pilih Merek',
    });

</script>

<script>
    $(document).ready(function() {
        $('#btn-preview').on('click', function(e) {
            e.preventDefault();
            var daterange = $('#daterange').val();
            var merek = $('#merek').val();
            var lokasi = $('#lokasi').val();

            $.ajax({
                url: '{{ route("laporan-pembelian") }}',
                method: 'GET',
                data: {
                    daterange: daterange,
                    lokasi: lokasi,
                    merek: merek,
                },
                success: function(data) {
                    var tbody = $('#data-table tbody');
                    tbody.empty();
                    var totalJumlah = 0;
                    var totalHarga = 0;
                    var overallIndex = 1;

                    $.each(data, function(index, item) {
                        $.each(item.detail, function(detailIndex, detail) {
                            var row = '<tr>' +
                                '<td>' + overallIndex + '</td>' +
                                '<td>' + item.no_nota + '</td>' +
                                '<td>' + item.tanggal + '</td>' +
                                '<td>' + detail.kode_barang + '</td>' +
                                '<td>' + detail.nama_barang + '</td>' +
                                '<td>' + detail.merek + '</td>' +
                                '<td>' + Math.floor(detail.harga).toLocaleString('id-ID') + '</td>' +
                                '<td>' + detail.jumlah + '</td>' +
                                '<td>' + (detail.harga * detail.jumlah).toLocaleString('id-ID') + '</td>' +
                                '</tr>';
                            tbody.append(row);

                            totalJumlah += parseInt(detail.jumlah);
                            totalHarga += detail.harga * detail.jumlah;
                            overallIndex++;
                        });
                    });

                    var totalRow = '<tr>' +
                        '<td colspan="7" style="text-align: right;"><strong>Total:</strong></td>' +
                        '<td>' + totalJumlah + '</td>' +
                        '<td>' + totalHarga.toLocaleString('id-ID') + '</td>' +
                        '</tr>';
                    tbody.append(totalRow);
                }
            });
        });

        $('#btn-export-pdf').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let daterange = $('#daterange').val();
            let formData = $('#filter-form').serialize();

            if (daterange) {
                formData += '&daterange=' + encodeURIComponent(daterange);
            }

            let url = "{{ route('laporan-pembelian.exportpdf') }}?" + formData;

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
            let formData = $('#filter-form').serialize();

            if (daterange) {
                formData += '&daterange=' + encodeURIComponent(daterange);
            }

            let url = "{{ route('laporan-pembelian.exportexcel') }}?" + formData;

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
                        title: 'Sedang Mengexport Data!',
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
