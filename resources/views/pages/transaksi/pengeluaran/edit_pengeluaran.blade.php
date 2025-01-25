@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>
             <form action="{{ route('update-pengeluaran', $pengeluaran->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="tanggal">Tanggal <span style="color: red">*</span></label>
                    <input type="text" class="form-control datepicker-input" placeholder="Piih Tanggal" name="tanggal" value="{{ $pengeluaran->tanggal}}">
                </div>
                <div class="form-group">
                    <label for="uraian">Uraian <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="uraian" placeholder="uraian Barang" name="uraian" value="{{ $pengeluaran->uraian}}" >
                </div>
                <div class="form-group">
                    <label for="total">Total <span style="color: red">*</span></label>
                    <input
                    type="text"
                    class="form-control"
                    id="total"
                    placeholder="Total"
                    name="total"
                    value="{{ isset($pengeluaran->total) ? number_format($pengeluaran->total, 0, ',', '.') : '' }}"
                >

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

@push('js')
<script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script>
    $('.datepicker-input').datepicker();
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalInput = document.getElementById('total');

        // Fungsi untuk memformat angka ke format ribuan (Indonesia)
        function formatNumber(value) {
            if (!value) return '';
            const number = parseInt(value.replace(/\./g, ''), 10);
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Fungsi untuk menghapus separator ribuan sebelum pengolahan angka
        function removeThousandSeparator(value) {
            return value.replace(/\./g, '');
        }

        // Format ulang angka saat halaman dimuat
        if (totalInput.value) {
            const rawValue = removeThousandSeparator(totalInput.value);
            totalInput.value = formatNumber(rawValue);
        }

        // Tambahkan event listener untuk memformat angka saat pengguna mengetik
        totalInput.addEventListener('input', function () {
            const rawValue = removeThousandSeparator(totalInput.value);
            if (!isNaN(rawValue)) {
                totalInput.value = formatNumber(rawValue);
            }
        });
    });
</script>


@endpush

