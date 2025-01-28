@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>
        <form action="{{ route('update-pembelian', $pembelian->id) }}" method="POST" id="form-pembelian">
                @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Tanggal <span style="color: red">*</span></label>
                            <div class="input-affix m-b-10">
                                <i class="prefix-icon anticon anticon-calendar"></i>
                                <input type="text" class="form-control" id="tanggal" name="tanggal" placeholder="Pilih Tanggal" value="{{ $pembelian->tanggal }}" required/>

                                {{-- <input type="text" class="form-control datepicker-input" placeholder="Piih Tanggal" name="tanggal" required  value="{{ $pembelian->tanggal }}"> --}}
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="no_nota">No Nota <span style="color: red">*</span></label>
                            <input type="text" class="form-control" id="no_nota" placeholder="No Nota" name="no_nota" required value="{{ $pembelian->no_nota }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="kontainer">Kontainer <span style="color: red">*</span></label>
                            <input type="text" class="form-control" id="kontainer" placeholder="Kontainer" name="kontainer" required value="{{ $pembelian->kontainer }}">
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
                            <button class="btn btn-danger m-r-5 btn-import" id="btn-import">Import</button>
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
                                <tbody id="table-body-content">

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" style="text-align: end">Total Pembelian</td>
                                        <td id="total-pembelian">Rp 0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
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

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"></div>

@endsection
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .txt{
        text-align: center;
    }
</style>
@push('css')


@endpush

@push('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(function() {
        $('input[name="tanggal"]').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
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
        const hargaInput = document.getElementById('harga');
        const jumlahInput = document.getElementById('jumlah');
        const bayarInput = document.getElementById('sub_total');
        const form = document.getElementById('form-pembelian'); // Form utama
        const tableBody = document.querySelector('#table-body'); // Tabel untuk barang

        function hitungHargaJual() {

            const harga = hargaInput.value || 0;
            const jumlah = jumlahInput.value || 0;

            const hargaJual = harga * jumlah;
            bayarInput.value = hargaJual.toFixed(2); // Sisa dengan format Rp
            // bayarInput.value = hargaJual;
        }

        hargaInput.addEventListener('input', hitungHargaJual);
        jumlahInput.addEventListener('input', hitungHargaJual);
    });
</script>

<script>
    $(document).on('click', '.btn-import', function(e) {
        e.preventDefault();
        let url = "/modal-import-pembelian";
        $(this).prop('disabled', true)
        $.ajax({
            url,
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                $('#importmodal').html(data);
                $('#importmodal').modal('show');
                $('.btn-import').prop("disabled", false);
                $('.btn-import').html('<span>Import</span>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-import').prop('disabled', false);
                $('.btn-import').html('<span>Import</span>');
            }
        })
    })
</script>

<script>
    $(document).on('submit', '#form-imporbarang', function(e) {
        e.preventDefault();

        const fileInput = $('#customFile')[0];
        const file = fileInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const data = event.target.result;

                const parsedData = parseCSV(data);

                searchInDatabase(parsedData, function(response) {
                    importToTable(response);
                });

                fileInput.value = '';
            };
            reader.readAsText(file);
        } else {
            alert("Silakan pilih file terlebih dahulu.");
        }
    });

    let rowCount = 0;

    function searchInDatabase(data, callback) {
        $.ajax({
            url: '/validasi-detail-pembelian',
            type: 'POST',
            data: { items: data },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    callback(response.data);
                }
            },
            error: function(xhr) {
                if (xhr.status === 404) {
                    const errorMessage = xhr.responseJSON.message;
                    alert(errorMessage);
                } else {
                    alert('Terjadi kesalahan saat memvalidasi data.');
                }
            }
        });
    }

    function updateTotalPembelian() {
        const subtotals = document.querySelectorAll('.subtotal');
        let total = 0;
        subtotals.forEach(function(subtotal) {
            total += parseFloat(subtotal.textContent.replace(/[^\d.-]/g, '') || 0);
        });
        document.getElementById('total-pembelian').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function importToTable(data) {
        const tableBody = document.getElementById('table-body-content');

        data.forEach((item) => {
            const formattedHarga = item.harga.toString();
            const formattedSubtotal = item.subtotal.toString();
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td></td> <!-- Indeks akan diperbarui -->
                <td>${item.kode_barang}</td>
                <td>${item.nama_barang}</td>
                <td>${item.merek}</td>
                <td>${formattedHarga}</td>
                <td>${item.jumlah}</td>
                <td class="subtotal">${formattedSubtotal}</td>
                <td>
                    <button class="btn btn-icon btn-danger btn-rounded remove-row">
                        <i class="anticon anticon-close"></i>
                    </button>
                </td>
                <input type="hidden" name="table_data[${rowCount}][id_barang]" value="${item.id_barang}">
                <input type="hidden" name="table_data[${rowCount}][kode_barang]" value="${item.kode_barang}">
                <input type="hidden" name="table_data[${rowCount}][nama_barang]" value="${item.nama_barang}">
                <input type="hidden" name="table_data[${rowCount}][merek]" value="${item.merek}">
                <input type="hidden" name="table_data[${rowCount}][harga]" value="${item.harga}">
                <input type="hidden" name="table_data[${rowCount}][jumlah]" value="${item.jumlah}">
                <input type="hidden" name="table_data[${rowCount}][subtotal]" value="${item.subtotal}">
            `;

            rowCount++;
            tableBody.appendChild(newRow);
        });

        updateRowNumbers(); // Update indeks setelah data ditambahkan
        setRemoveRowEvent();
        updateTotalPembelian();
    }

    function parseCSV(data) {
        const lines = data.split('\n');
        const result = [];
        for (let i = 1; i < lines.length; i++) { // skip header
            const columns = lines[i].split(',');
            result.push({
                kode_barang: columns[0],
                merek: columns[1],
                jumlah: columns[2],
            });
        }
        return result;
    }

    function setRemoveRowEvent() {
        const removeButtons = document.querySelectorAll('.remove-row');
        removeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const row = button.closest('tr');
                row.remove();
                updateRowNumbers(); // Update indeks setelah baris dihapus
                updateTotalPembelian();
            });
        });
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll('#table-body-content tr');
        rows.forEach((row, index) => {
            const indexCell = row.querySelector('td:first-child');
            if (indexCell) {
                indexCell.textContent = index + 1; // Perbarui indeks
            }
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const namaBarangSelect = document.getElementById('nama_barang');
        const hargaInput = document.getElementById('harga');
        const kodeBarangInput = document.getElementById('kode_barang');
        const merekInput = document.getElementById('merek');
        const jumlahInput = document.getElementById('jumlah');
        const addButton = document.querySelector('button[type="submit"]');
        const tableBody = document.querySelector('table tbody');
        let rowCount = 0;

        const itemsFromDB = @json($detailPembelian);
        const bayarFromDB = @json($bayar);

        console.log(itemsFromDB);

        document.getElementById('bayar_input').value = bayarFromDB;

        // Render data dari database
        itemsFromDB.forEach(function (item) {
            addRowToTable(item);
        });

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        // Update input ketika pilihan barang berubah
        namaBarangSelect.addEventListener('change', function () {
            const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga');
            const kodeBarang = selectedOption.getAttribute('data-kode');
            const merek = selectedOption.getAttribute('data-merek');

            hargaInput.value = harga ? harga : '';
            kodeBarangInput.value = kodeBarang ? kodeBarang : '';
            merekInput.value = merek ? merek : '';
        });

        // Tambahkan data baru ke tabel
        addButton.addEventListener('click', function (e) {
            e.preventDefault();

            const namaBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].text;
            const kodeBarang = kodeBarangInput.value;
            const merek = merekInput.value;
            const harga = hargaInput.value;
            const jumlah = jumlahInput.value;
            const idBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].getAttribute('data-id');

            if (!namaBarang || !kodeBarang || !merek || !harga || !jumlah || !idBarang) {
                alert('Mohon lengkapi semua data sebelum menambahkan!');
                return;
            }

            const calculatedSubTotal = parseFloat(harga) * parseFloat(jumlah);

            const itemData = {
                id_barang: idBarang,
                kode_barang: kodeBarang,
                nama_barang: namaBarang,
                merek: merek,
                harga: harga,
                jumlah: jumlah,
                subtotal: calculatedSubTotal.toFixed(0) // Tanpa desimal
            };

            addRowToTable(itemData);
            resetForm();
            updateTotalPembelian();
        });

        // Fungsi untuk menambahkan baris ke tabel
        function addRowToTable(itemData) {
            const newRow = `
                <tr>
                    <td>${++rowCount}</td>
                    <td>${itemData.id_barang}</td>
                    <td>${itemData.nama_barang}</td>
                    <td>${itemData.merek}</td>
                    <td>${itemData.harga}</td>
                    <td>${itemData.jumlah}</td>
                    <td class="subtotal">${itemData.subtotal}</td>
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
            tableBody.insertAdjacentHTML('beforeend', newRow);
        }

        // Reset form setelah data ditambahkan
        function resetForm() {
            namaBarangSelect.value = '';
            kodeBarangInput.value = '';
            merekInput.value = '';
            hargaInput.value = '';
            jumlahInput.value = '';
        }

        // Event delegation untuk tombol hapus
        tableBody.addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                const row = e.target.closest('tr');
                row.remove();
                updateRowNumbers();
                updateTotalPembelian();
            }
        });

        // Update nomor baris setelah penghapusan
        function updateRowNumbers() {
            const rows = tableBody.querySelectorAll('tr');
            rowCount = 0;
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').innerText = index + 1;
                rowCount++;
            });
        }

        // Update total pembelian
        function updateTotalPembelian() {
            const subtotals = tableBody.querySelectorAll('.subtotal');
            let total = 0;

            subtotals.forEach(function (subtotalCell) {
                const subtotalValue = subtotalCell.textContent.replace(/[^\d.-]/g, '');
                if (!isNaN(subtotalValue) && subtotalValue.trim() !== '') {
                    total += parseFloat(subtotalValue);
                }
            });

            document.getElementById('total-pembelian').textContent = 'Rp ' + total.toFixed(0);
            // document.getElementById('bayar_input').value = total.toFixed(0);
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bayarInput = document.getElementById('bayar_input');
        const kembaliInput = document.getElementById('kembali_input');
        const sisaInput = document.getElementById('sisa_input');
        const tableBody = document.querySelector('table tbody');

        function updateTotalPembelian() {
            const subtotalCells = tableBody.querySelectorAll('.subtotal');
            let total = 0;

            // Menghitung total dari semua subtotal
            subtotalCells.forEach(function (subtotalCell) {
                const subtotalValue = parseFloat(subtotalCell.textContent.replace(/[^\d.-]/g, '')) || 0;
                total += subtotalValue;
            });

            // Menampilkan total pembelian pada kolom terakhir
            const totalPembelianCell = document.getElementById('total-pembelian');
            if (totalPembelianCell) {
                totalPembelianCell.textContent = 'Rp ' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            return total;
        }

        function calculateSisaDanKembali() {
            const totalPembelian = updateTotalPembelian(); // Tetap hitung total pembelian
            const bayar = parseFloat(bayarInput.value.replace(/[^\d.-]/g, '')) || 0;
            const sisa = totalPembelian - bayar;
            const kembali = bayar >= totalPembelian ? bayar - totalPembelian : 0;

            // Memperbarui input sisa dan kembali
            sisaInput.value = sisa > 0 ? 'Rp ' + sisa.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : 'Rp 0';
            kembaliInput.value = kembali > 0 ? 'Rp ' + kembali.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : 'Rp 0';
        }

        // Event listener untuk input pembayaran
        bayarInput.addEventListener('input', function () {
            calculateSisaDanKembali(); // Hitung ulang hanya sisa dan kembali
        });

        // Event listener untuk perubahan pada tabel
        tableBody.addEventListener('input', function () {
            updateTotalPembelian(); // Hanya perbarui total pembelian
        });

        // Inisialisasi perhitungan saat halaman dimuat
        updateTotalPembelian(); // Hitung total pembelian awal
        calculateSisaDanKembali(); // Hitung sisa dan kembali awal
    });
</script>


@endpush
