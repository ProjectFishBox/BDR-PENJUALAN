@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <form id="filterForm">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="/tambah-pengeluaran" class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</a>
                </div>

                <div class="d-flex align-items-center m-2 mt-3">
                    <input type="text" class="form-control" id="daterange" name="daterange" placeholder="Pilih Tanggal" value="{{ request()->get('daterange') }}" />

                    <div class="form-group col-md-6 ml-3">
                        <label for="lokasi">Lokasi</label>
                        <select id="lokasi" class="lokasi form-control" name="lokasi">
                            <option value="">Pilih Lokasi</option>
                            <option value="all">Semua Lokasi</option>
                            @foreach ($lokasiList as $b)
                                <option value="{{ $b->id }}" {{ request()->get('lokasi') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-default ml-3 mr-3" type="submit">Filter</button>
                    <button class="btn-detail btn btn-primary m-r-5 mt-2 mb-2">Export</button>
                </div>
            </form>

            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered data-table" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">No</th>
                                <th scope="col" style="text-align: center; width: 15%;">Tanggal</th>
                                <th scope="col" style="text-align: center; width: 40%;">Uraian</th>
                                <th scope="col" style="text-align: center; width: 20%;">Total</th>
                                <th scope="col" style="text-align: center; width: 15%;">Lokasi</th>
                                <th scope="col" style="text-align: center; width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal" style="display: none;" id="detailexport" tabindex="-1" role="dialog"
    aria-labelledby="importModalLabel" aria-hidden="true">
</div>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@component('components.aset_datatable.aset_datatable')@endcomponent



@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>

    $('.lokasi').select2({
        width: '100%',
        placeholder: 'Pilih lokasi',
    });
</script>

<script>
    $(document).ready(function() {
        dataPengeluaran();

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            reloadTable();
        });
    });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataPengeluaran();
    }
</script>

<script>
    function dataPengeluaran() {
        if ($.fn.DataTable.isDataTable('.data-table')) {
            $('.data-table').DataTable().destroy();
        }

        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pengeluaran') }}",
                data: function (d) {
                    d.lokasi = $('select[name="lokasi"]').val();
                    d.daterange = $('input[name="daterange"]').val();
                }
            },
            lengthMenu: [10, 20],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'uraian', name: 'uraian' },
                { data: 'total', name: 'total', render: function(data) { return numberFormat(data); } },
                { data: 'lokasi.nama', name: 'lokasi.nama' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }

    dataPengeluaran();

    function numberFormat(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>


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
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        let url = "/modal-detail-pengeluaran";

        const daterange = $('input[name="daterange"]').val();
        const lokasi = $('select[name="lokasi"]').val();

        $(this).prop('disabled', true);

        $.ajax({
            url: url,
            data: { daterange, lokasi },
            type: "GET",
            success: function(data) {
                $('#detailexport').html(data);
                $('#detailexport').modal('show');
                $('.btn-detail').prop("disabled", false);
                $('.btn-detail').html('<span>Export</span>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-detail').prop('disabled', false);
                $('.btn-detail').html('<span>Export</span>');
            }
        });
    });
</script>

<script>
    $(document).on('click', '.btn-pengeluaran-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        console.log(id);
        console.log('data id delete', id);
        let url = "/delete-pengeluaran/" + id;
        Swal.fire({
            title: 'Apakah kamu ingin menghapus data ini?',
            text: "data tidak dapat dikembalikan lagi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya, hapus data ini!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url,
                    type: "GET",
                    dataType: "HTML",
                    success: function(data) {
                        reloadTable();
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Data pengeluaran Telah berhasil dihapus.',
                            icon: 'success',
                            timer: 2000

                        })
                    }
                })
            }
        })
    })
</script>

<script>
    $(document).on('click', '.btn-pengeluaran-edit', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/pengeluaran-edit/" + id;
        $(this).prop('disabled', true)
        $.ajax({
            url,
            data: {
                id
            },
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                window.location.href = url;
                $('.btn-pengeluaran-edit').prop('disabled', false);
                $('.btn-pengeluaran-edit').html('<i class="anticon anticon-edit"></i>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-pengeluaran-edit').prop('disabled', false);
                $('.btn-pengeluaran-edit').html(' <i class="anticon anticon-edit"></i>');
            }
        })
    })
</script>
@endpush
