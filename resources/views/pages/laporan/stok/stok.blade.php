@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">{{ $title }}</h4>

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
                    <label for="barang">Barang</label>
                    <select id="barang" class="barang" name="barang">
                        <option value="">Pilih Barang</option>
                        @foreach ($barang->unique('kode_barang') as $b)
                            <option value="{{ $b->id }}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-nama="{{ $b->nama }}">
                                ({{$b->kode_barang}}) {{ $b->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-6">
                    <label for="merek">Merek</label>
                    <select id="merek" class="merek form-control" name="merek" required>
                        <option value="">Pilih Merek</option>
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
                                <th scope="col" style="text-align: center; width: 10%;">No</th>
                                <th scope="col" style="text-align: center;">Kode Barang</th>
                                <th scope="col" style="text-align: center;">Nama</th>
                                <th scope="col" style="text-align: center;">Merek</th>
                                <th scope="col" style="text-align: center;">Masuk</th>
                                <th scope="col" style="text-align: center;">Keluar</th>
                                <th scope="col" style="text-align: center;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal" style="display: none;" id="previewstok" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"> </div>
@endsection

@component('components.aset_datatable.aset_datatable')@endcomponent


@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        dataStok();
        });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataStok();
    }
</script>

<script>
    function dataStok() {

        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('stok') }}",
            lengthMenu: [
                10, 20
            ],
            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'kode_barang', name: 'kode_barang'},
            {data: 'nama_barang', name: 'nama_barang'},
            {data: 'merek', name: 'merek'},
            {data: 'total_masuk', name: 'total_masuk'},
            {data: 'total_terjual', name: 'total_terjual'},
            {data: 'stok_akhir', name: 'stok_akhir'},
            ],
            drawCallback: function(settings) {
                let response = settings.json;
                let tfoot = `
                    <tr style="background-color: #f4f4f4; font-weight: bold;">
                        <td colspan="4" style="text-align: center;">Total</td>
                        <td style="text-align: center;">${response.total_masuk}</td>
                        <td style="text-align: center;">${response.total_keluar}</td>
                        <td style="text-align: center;">${response.total_stok}</td>
                    </tr>
                `;

                $(this).find('tfoot').remove();

                $(this).append('<tfoot>' + tfoot + '</tfoot>');
            }
        });
    }

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
            let url = "{{ route('stok.filtered') }}?" + $('#filter-form').serialize();
            $(this).prop('disabled', true)
            $.ajax({
                url,
                type: "GET",
                dataType: "HTML",
                success: function(data) {
                    $('#previewstok').html(data);
                    $('#previewstok').modal('show');
                    $('#btn-preview').prop("disabled", false);
                    $('#btn-preview').html('<span>Preview</span>');
                },
                error: function(error) {
                    console.error(error);
                    $('#btn-preview').prop('disabled', false);
                    $('#btn-preview').html('</i><span>Preview</span>');
                }
            })
        });

        $('#btn-export-pdf').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let url = "{{ route('stok.exportpdf') }}?" + $('#filter-form').serialize();
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
            let url = "{{ route('stok.exportexcel') }}?" + $('#filter-form').serialize();
            Swal.fire({
                title: 'Apakah kamu ingin mengexport excel data ini?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, export sekarang!'
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

    })

</script>

@endpush
