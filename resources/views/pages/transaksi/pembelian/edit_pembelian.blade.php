@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>
            <form action="{{ route('tambah-pembelian')}}" method="POST" id="form-pembelian">
                @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Tanggal <span style="color: red">*</span></label>
                            <div class="input-affix m-b-10">
                                <i class="prefix-icon anticon anticon-calendar"></i>
                                <input type="text" class="form-control datepicker-input" placeholder="Piih Tanggal" name="tanggal" required>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="no_nota">No Nota <span style="color: red">*</span></label>
                            <input type="text" class="form-control" id="no_nota" placeholder="No Nota" name="no_nota" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="kontainer">Kontainer <span style="color: red">*</span></label>
                            <input type="text" class="form-control" id="kontainer" placeholder="Kontainer" name="kontainer" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="nama_barang">Barang</label>
                            <select id="nama_barang" class="form-control">
                                <option value="">Pilih Barang</option>
                                @foreach ($barang as $b)
                                    <option value="{{ $b->id}}" data-id="{{ $b->id }}" data-nama="{{ $b->nama }}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek}}>{{ $b->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="kode_barang">Kode</label>
                            <input type="text" class="form-control" id="kode_barang"  readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="merek">Merek</label>
                            <input type="text" class="form-control" id="merek" readonly >
                        </div>
                        <div class="form-group col-md-2">
                            <label for="harga">Harga</label>
                            <input type="text" class="form-control" id="harga" readonly placeholder="Harga">
                        </div>
                        <div class="form-group col-md-1">
                            <label for="jumlah">Jumlah</label>
                            <input type="text" class="form-control" id="jumlah">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="sub_total">Sub Total</label>
                            <input type="text" id="sub_total" readonly style="border: none; font-size: x-large">
                        </div>
                    </div>
                    <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <button class="btn btn-danger m-r-5">Import</button>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="/setharga" class="btn btn-danger mr-3">Batal</a>
                            <button class="btn btn-success" type="submit">Tambahkan</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-body">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Kode Barang</th>
                                        <th scope="col">Nama Barang</th>
                                        <th scope="col">Merek</th>
                                        <th scope="col">Harga</th>
                                        <th scope="col">Jumlah</th>
                                        <th scope="col">Sub Total</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detailPembelian as $d)
                                        <td>No</td>
                                        <td>Kode Barang</td>
                                        <td>{{$d->nama}}</td>
                                        <td>{{$d->merek}}</td>
                                        <td>{{$d->harga}}</td>
                                        <td>{{$d->jumlah}}</td>
                                        <td>{{$d->subtotal}}</td>
                                    @endforeach
                                    <tr>
                                        <td colspan="6" style="text-align: end">Total Pembelian</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="bayar_input">Bayar</label>
                                <input type="text" class="form-control" id="bayar_input" placeholder="Bayar" name="bayar">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="kembali_input">Kembali</label>
                                <input type="text" class="form-control" id="kembali_input" placeholder="Kembali" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sisa_input">Sisa</label>
                                <input type="text" class="form-control" id="sisa_input" placeholder="Sisa" readonly>
                            </div>
                        </div>
                    </div>


                    <div class="d-flex justify-content-end">
                        <a href="/setharga" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Tambahkan</button>
                    </div>
            </form>
        </div>
    </div>
@endsection
<link href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet">
<style>
    .txt{
        text-align: center;
    }
</style>
@push('css')


@endpush

@push('js')
<script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
{{-- <script>
    $('.datepicker-input').datepicker();
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hargaInput = document.getElementById('harga');
        const jumlahInput = document.getElementById('jumlah');
        const bayarInput = document.getElementById('sub_total');
        const form = document.getElementById('form-pembelian'); // Form utama
        const tableBody = document.querySelector('#table-body'); // Tabel untuk barang

        function hitungHargaJual() {

            const harga = hargaInput.value || 0;
            const jumlah = jumlahInput.value || 0;

            const hargaJual = harga * jumlah;
            bayarInput.value = 'Rp ' + hargaJual.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // Sisa dengan format Rp
            // bayarInput.value = hargaJual;
        }

        hargaInput.addEventListener('input', hitungHargaJual);
        jumlahInput.addEventListener('input', hitungHargaJual);
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    const namaBarangSelect = document.getElementById('nama_barang');
    const hargaInput = document.getElementById('harga');
    const kodeBarangInput = document.getElementById('kode_barang');
    const merekInput = document.getElementById('merek');
    const jumlahInput = document.getElementById('jumlah');
    const bayarInput = document.getElementById('sub_total');
    const addButton = document.querySelector('button[type="submit"]');
    const tableBody = document.querySelector('table tbody');

    namaBarangSelect.addEventListener('change', function () {
        const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
        const harga = selectedOption.getAttribute('data-harga');
        const kodeBarang = selectedOption.getAttribute('data-kode');
        const merek = selectedOption.getAttribute('data-merek');
        const idBarang = selectedOption.getAttribute('data-id');

        hargaInput.value = harga ? harga : '';
        kodeBarangInput.value = kodeBarang ? kodeBarang : '';
        merekInput.value = merek ? merek : '';
    });

    addButton.addEventListener('click', function (e) {
        e.preventDefault();

        const namaBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].text;
        const kodeBarang = kodeBarangInput.value;
        const merek = merekInput.value;
        const harga = hargaInput.value;
        const jumlah = jumlahInput.value;
        const subTotal = bayarInput.value;
        const idBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].getAttribute('data-id');

        if (!namaBarang || !kodeBarang || !merek || !harga || !jumlah || !subTotal || !idBarang) {

            console.error("Debugging data kosong:");
            if (!namaBarang) console.error("Nama Barang kosong.");
            if (!kodeBarang) console.error("Kode Barang kosong.");
            if (!merek) console.error("Merek kosong.");
            if (!harga) console.error("Harga kosong.");
            if (!jumlah) console.error("Jumlah kosong.");
            if (!subTotal) console.error("Sub Total kosong.");
            if (!idBarang) console.error("ID Barang kosong.");

            alert('Mohon lengkapi semua data sebelum menambahkan!');
            return;
        }


        const calculatedSubTotal = parseFloat(harga) * parseFloat(jumlah);

        let rowCount = 0;

        const itemData = {
            id_barang: idBarang,
            kode_barang: kodeBarang,
            nama_barang: namaBarang,
            merek: merek,
            harga: harga,
            jumlah: jumlah,
            subtotal: calculatedSubTotal.toFixed(2)
        };

        const newRow = `
            <tr>
                <td>${rowCount + 1}</td>
                <td>${kodeBarang}</td>
                <td>${namaBarang}</td>
                <td>${merek}</td>
                <td>${harga}</td>
                <td>${jumlah}</td>
                <td class="subtotal">${calculatedSubTotal.toFixed(2)}</td>
                <td>
                    <button class="btn btn-icon btn-danger btn-rounded remove-row">
                        <i class="anticon anticon-close"></i>
                    </button>
                </td>

                <input type="hidden" name="table_data[${rowCount}][id_barang]" value="${itemData.id_barang}">
                <input type="hidden" name="table_data[${rowCount}][kode_barang]" value="${itemData.kode_barang}">
                <input type="hidden" name="table_data[${rowCount}][nama_barang]" value="${itemData.nama_barang}">
                <input type="hidden" name="table_data[${rowCount}][merek]" value="${itemData.merek}">
                <input type="hidden" name="table_data[${rowCount}][harga]" value="${itemData.harga}">
                <input type="hidden" name="table_data[${rowCount}][jumlah]" value="${itemData.jumlah}">
                <input type="hidden" name="table_data[${rowCount}][subtotal]" value="${itemData.subtotal}">
            </tr>
        `;

        rowCount++;

        const totalPembelianRow = tableBody.querySelector('tr:last-child');
        totalPembelianRow.insertAdjacentHTML('beforebegin', newRow);

        namaBarangSelect.value = '';
        kodeBarangInput.value = '';
        merekInput.value = '';
        hargaInput.value = '';
        jumlahInput.value = '';
        bayarInput.value = '';

        updateTotalPembelian();
        setRemoveRowEvent();
    });

    function setRemoveRowEvent() {
        const removeButtons = document.querySelectorAll('.remove-row');
        removeButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const row = button.closest('tr');
                row.remove();
                updateRowNumbers();

                updateTotalPembelian();
            });
        });
    }

    function updateRowNumbers() {
        const rows = tableBody.querySelectorAll('tr:not(:last-child)');
        rows.forEach((row, index) => {
            row.children[0].textContent = index + 1;
        });
    }

    function updateTotalPembelian() {
        const subtotals = tableBody.querySelectorAll('.subtotal');
        let total = 0;

        subtotals.forEach(function (subtotalCell) {
            const subtotalValue = subtotalCell.textContent;
            if (!isNaN(subtotalValue)) {
                total += parseFloat(subtotalValue);
            }
        });

        const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');
        if (totalPembelianCell) {
            totalPembelianCell.textContent = total.toFixed(2);
        }
    }
});

</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bayarInput = document.getElementById('bayar_input');
        const kembaliInput = document.getElementById('kembali_input');
        const sisaInput = document.getElementById('sisa_input');
        const tableBody = document.querySelector('table tbody'); // Tabel body

        // Function to update the total pembelian and calculations
        function updateTotalPembelian() {
            const subtotals = tableBody.querySelectorAll('.subtotal');
            let total = 0;

            subtotals.forEach(function (subtotalCell) {
                const subtotalValue = subtotalCell.textContent;
                if (!isNaN(subtotalValue)) {
                    total += parseFloat(subtotalValue);
                }
            });

            // Update total pembelian in the last row
            const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');
            if (totalPembelianCell) {
                totalPembelianCell.textContent = total.toFixed(2); // Total pembelian tanpa format
            }

            return total;
        }

        // Function to calculate sisa and kembali based on bayar and total
        function calculateSisaDanKembali() {
            const totalPembelian = updateTotalPembelian();
            const bayar = parseFloat(bayarInput.value) || 0;
            const sisa = totalPembelian - bayar;
            const kembali = bayar >= totalPembelian ? bayar - totalPembelian : 0;

            // Update sisa and kembali
            sisaInput.value = 'Rp ' + sisa.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // Sisa dengan format Rp
            kembaliInput.value = 'Rp ' + kembali.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // Kembali dengan format Rp
        }

        // Event listener for the bayar input
        bayarInput.addEventListener('input', function () {
            calculateSisaDanKembali();
        });

        // Recalculate values when there are changes to the items in the table
        tableBody.addEventListener('change', function () {
            calculateSisaDanKembali();
        });

        // Initial calculation
        calculateSisaDanKembali();
    });
</script> --}}

@endpush
