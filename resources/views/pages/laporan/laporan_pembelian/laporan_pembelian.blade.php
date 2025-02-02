@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">{{ $title }}</h4>

            <div class="form-group col-6">
                <label for="merek">Periode</label>
                <input type="text" class="form-control" id="daterange" name="daterange" placeholder="Pilih Tanggal" value="{{ request()->get('daterange') }}" />
            </div>

            <form id="filter-form">
                <div class="form-group col-6">
                    <label for="lokasi">Lokasi</label>
                    <select id="lokasi" class="lokasi" name="lokasi">
                        <option value="">Pilih Lokasi</option>
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
                        @foreach ($barang as $b)
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
        dataLaporanPembelian();
        });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataLaporanPembelian();
    }
</script>

<script>
    function dataLaporanPembelian() {
        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('laporan-pembelian') }}",
                data: function(d) {
                    d.daterange = $('#daterange').val();
                    d.lokasi = $('#lokasi').val();
                    d.merek = $('#merek').val();
                }
            },
            lengthMenu: [10, 20],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'no_nota', name: 'no_nota'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'kode_barang', name: 'kode_barang'},
                {data: 'nama_barang', name: 'nama_barang'},
                {data: 'merek', name: 'merek'},
                {
                    data: 'harga',
                    name: 'harga',
                    render: function(data) {
                        return 'Rp ' + data;
                    }
                },
                {data: 'jumlah', name: 'jumlah'},
                {
                    data: 'total',
                    name: 'total',
                    render: function(data) {
                        return 'Rp ' + data;
                    }
                }
            ],
            drawCallback: function(settings) {
                let response = settings.json;
                if (response && response.total_penjualan) {
                    let tfoot = `
                        <tr style="background-color: #f4f4f4; font-weight: bold;">
                            <td colspan="8" style="text-align: right;">Total Penjualan:</td>
                            <td style="text-align: right;">Rp ${response.total_penjualan}</td>
                        </tr>
                    `;
                    $(this).find('tfoot').remove();
                    $(this).append('<tfoot>' + tfoot + '</tfoot>');
                }
            }
        });
    }

</script>


<script>
    $(document).ready(function() {
        $('#btn-preview').on('click', function(e) {
            e.preventDefault();
            let daterange = $('#daterange').val();
            let lokasi = $('#lokasi').val();
            let merek = $('#merek').val();

            let formData = $('#filter-form').serialize();

            if (daterange) {
                formData += '&daterange=' + encodeURIComponent(daterange);
            }

            let url = "{{ route('laporan-penjualan.filtered') }}?" + formData;
            $(this).prop('disabled', true);

            $.ajax({
                url: url,
                type: "GET",
                dataType: "HTML",
                success: function(data) {
                    $('#previewpembelian').html(data);
                    $('#previewpembelian').modal('show');
                    $('#btn-preview').prop("disabled", false);
                    $('#btn-preview').html('<span>Preview</span>');
                },
                error: function(error) {
                    console.error(error);
                    $('#btn-preview').prop('disabled', false);
                    $('#btn-preview').html('<span>Preview</span>');
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

            let url = "{{ route('laporan-penjualan.exportpdf') }}?" + formData;

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

            let url = "{{ route('laporan-penjualan.exportexcel') }}?" + formData;

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
