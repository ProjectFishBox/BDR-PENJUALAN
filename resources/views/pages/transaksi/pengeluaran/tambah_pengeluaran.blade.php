@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{ $title }}</h4>
            <form action="{{ route('tambah-pengeluaran')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="tanggal">Tanggal <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="tanggal" name="tanggal" placeholder="Pilih Tanggal" required/>
                </div>
                <div class="form-group">
                    <label for="uraian">Uraian <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="uraian" placeholder="uraian Barang" name="uraian" required>
                </div>
                <div class="form-group">
                    <label for="total">Total <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="total" placeholder="Total" name="total" required >
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end">
                        <a href="/pengeluaran" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function() {
        $('input[name="tanggal"]').daterangepicker({
            locale: {
                format: 'DD-MM-YYYY',
                cancelLabel: 'Clear'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalInput = document.getElementById('total');

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        function removeThousandSeparator(value) {
            return value.replace(/\./g, '');
        }
        [totalInput].forEach(input => {
            input.addEventListener('input', function () {
                const rawValue = removeThousandSeparator(input.value);
                const formattedValue = formatNumber(rawValue);
                input.value = formattedValue;
            });
        });
    });
</script>

@endpush
