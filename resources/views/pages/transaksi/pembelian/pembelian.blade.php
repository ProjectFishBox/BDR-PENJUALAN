@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title}}</h4>
            <form id="filterForm">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="/tambah-pembelian" class="btn btn-primary m-r-5 mt-2 mb-2" >Tambah</a>
                </div>

                <div class="d-flex align-items-center m-2 mt-3">
                    <input type="text" class="form-control" id="daterange" name="daterange" placeholder="Pilih Tanggal" value="{{ request()->get('daterange') }}" />

                    <div class="form-group col-md-4 ml-3">
                        <label for="lokasi">Lokasi</label>
                        <select id="lokasi" class="lokasi form-control" name="lokasi">
                            <option value="">Pilih Lokasi</option>
                            <option value="all">Semua Lokasi</option>
                            @foreach ($lokasi as $b)
                                <option value="{{ $b->id }}" {{ request()->get('lokasi') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-default ml-3 mr-3" type="submit">Filter</button>
                </div>
            </form>

            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover data-table" id="data-table"">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center;">No</th>
                                <th scope="col" style="text-align: center;">Tanggal</th>
                                <th scope="col" style="text-align: center;">No Nota</th>
                                <th scope="col" style="text-align: center;">Kontainer</th>
                                <th scope="col" style="text-align: center;">Total</th>
                                <th scope="col" style="text-align: center;">Lokasi</th>
                                <th scope="col" style="text-align: center;">Detail</th>
                                <th scope="col" style="text-align: center;">Akses</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal" style="display: none;" id="detailpembelianmodal" tabindex="-1" role="dialog"
        aria-labelledby="importModalLabel" aria-hidden="true">
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
    $('.lokasi').select2({
        width: '100%',
        placeholder: 'Pilih Lokasi',
    });
</script>

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
    $(document).ready(function() {
        dataPembelian();

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            reloadTable();
        })

    });

    function reloadTable() {
        $('#data-table').DataTable().clear().destroy();
        dataPembelian();
    }
</script>

<script>
    function dataPembelian() {

        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pembelian') }}",
                data: function (d) {
                    d.lokasi = $('select[name="lokasi"]').val();
                    d.daterange = $('input[name="daterange"]').val();
                }
            },
            lengthMenu: [
                10, 20
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'no_nota',
                    name: 'no_nota'
                },
                {
                    data: 'kontainer',
                    name: 'kontainer'
                },
                {
                    data: 'total',
                    name: 'total',
                    render: function (data, type, row) {
                        return numberFormat(data);
                    }
                },
                {
                    data: 'lokasi.nama',
                    name: 'lokasi.nama'
                },
                {
                    data: 'nama',
                    name: 'nama',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-primary btn-detail" id="btn-detail-${row.id}" data-id="${row.id}">
                                Detail
                            </button>`;
                    },

                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }

    function numberFormat(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>

<script>
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log('data id',id)
        let url = "/modal-detail-pembelian";
        $(this).prop('disabled', true)
        $.ajax({
            url,
            data: {
                id
            },
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                $('#detailpembelianmodal').html(data);
                $('#detailpembelianmodal').modal('show');
                $('.btn-detail').prop("disabled", false);
                $('.btn-detail').html('<span>Detail</span>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-detail').prop('disabled', false);
                $('.btn-detail').html('</i><span>Detail</span>');
            }
        })
    })
</script>

<script>
    $(document).on('click', '.btn-pembelian-edit', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/pembelian-edit/" + id;
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
                $('.btn-pembelian-edit').prop('disabled', false);
                $('.btn-pembelian-edit').html('<i class="anticon anticon-edit"></i>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-pembelian-edit').prop('disabled', false);
                $('.btn-pembelian-edit').html(' <i class="anticon anticon-edit"></i>');
            }
        })
    })
</script>

<script>
    $(document).on('click', '.btn-pembelian-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = "/delete-pembelian/" + id;
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
                            text: 'Data Pembelian Telah berhasil dihapus.',
                            icon: 'success',
                            timer: 2000

                        })
                    }
                })
            }
        })
    })
</script>

@endpush
