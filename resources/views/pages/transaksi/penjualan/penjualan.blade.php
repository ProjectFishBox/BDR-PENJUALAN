@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="/tambah-penjualan">
                    <button class="btn btn-primary m-r-5 mt-2 mb-2">Tambah</button>
                </a>

                <form action="{{ url()->current() }}" method="GET" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Cari" class="form-control" style="width: 250px; margin-left: 10px;" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn btn-secondary ml-2">Cari</button>
                </form>
            </div>


            <form action="{{ url()->current() }}" method="GET">
                <div class="form-group mt-3">
                    <div class="d-flex align-items-center m-2">
                        <input type="text" class="form-control datepicker-input" name="start" placeholder="From" value="{{ request()->get('start') }}">
                        <span class="p-h-10">to</span>
                        <input type="text" class="form-control datepicker-input" name="end" placeholder="To" value="{{ request()->get('end') }}">


                        <div class="form-group col-md-4">
                            <label for="pelanggan">Pelanggan</label>
                            <select id="pelanggan" class="form-control" name="pelanggan">
                                <option value="">Pilih Pelanggan</option>
                                @foreach ($pelanggan as $p)
                                    <option value="{{ $p->id }}" {{ request()->get('pelanggan') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="lokasi">Lokasi</label>
                            <select id="lokasi" class="form-control" name="lokasi">
                                <option value="">Pilih Lokasi</option>
                                @foreach ($lokasi as $b)
                                    <option value="{{ $b->id }}" {{ request()->get('lokasi') == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-default mr-3" type="submit">Filter</button>
                    </div>
                </div>
            </form>


            <div class="m-t-25">
                <div class="table-responsive">
                    <table class="table table-bordered data-table" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">No</th>
                                <th scope="col" style="text-align: center; width: 15%;">Tanggal</th>
                                <th scope="col" style="text-align: center; width: 40%;">No. Nota</th>
                                <th scope="col" style="text-align: center; width: 40%;">Pelanggan</th>
                                <th scope="col" style="text-align: center; width: 20%;">Total</th>
                                <th scope="col" style="text-align: center; width: 15%;">Lokasi</th>
                                <th scope="col" style="text-align: center; width: 15%;">Detail</th>
                                <th scope="col" style="text-align: center; width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengeluaran as $index => $p)
                                <tr>
                                    <th scope="row" style="text-align: center;">{{ $index + 1 }}</th>
                                    <td style="text-align: center;">{{ $p->tanggal }}</td>
                                    <td style="text-align: center;">{{ $p->no_nota }}</td>
                                    <td style="text-align: center;">{{ $p->pelanggan->nama }}</td>
                                    <td style="text-align: right;">{{ number_format($p->total_penjualan, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">{{ $p->lokasi->nama }}</td>
                                    <td style="text-align: center;">
                                        <button class="btn btn-primary btn-detail" id="btn-detail" data-id="{{ $p->id}}">
                                            Detail
                                        </button>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="btn-group" style="display: flex; gap: 5px; justify-content: center;">
                                            <a href="{{ route('pengeluaran-edit', $p->id) }}">
                                                <button class="btn btn-icon btn-primary">
                                                    <i class="anticon anticon-edit"></i>
                                                </button>
                                            </a>
                                            <button class="btn-barang-delete btn btn-icon btn-danger" data-id="{{ $p->id }}">
                                                <i class="anticon anticon-delete"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal" style="display: none;" id="detailpenjualanmodal" tabindex="-1" role="dialog"
        aria-labelledby="importModalLabel" aria-hidden="true">
    </div>
@endsection

@component('components.aset_datatable.aset_datatable')@endcomponent


@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script>
    $('.datepicker-input').datepicker();
</script>

<script>
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log('data id',id)
        let url = "/modal-detail-penjualan";
        $(this).prop('disabled', true)
        $.ajax({
            url,
            data: {
                id
            },
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                $('#detailpenjualanmodal').html(data);
                $('#detailpenjualanmodal').modal('show');
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
@endpush
