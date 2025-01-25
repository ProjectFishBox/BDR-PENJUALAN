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
                                <tbody>
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

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"></div>
@endsection

@push('css')
<link href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet">
<style>
    .txt{
        text-align: center;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script>
    $('.datepicker-input').datepicker();
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hargaInput = document.getElementById('harga');
        const jumlahInput = document.getElementById('jumlah');
        const bayarInput = document.getElementById('sub_total');
        const form = document.getElementById('form-pembelian');
        const tableBody = document.querySelector('#table-body');

        function hitungHargaJual() {

            const harga = hargaInput.value || 0;
            const jumlah = jumlahInput.value || 0;

            const hargaJual = harga * jumlah;
            bayarInput.value = 'Rp ' + hargaJual.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // Sisa dengan format Rp
        }

        hargaInput.addEventListener('input', hitungHargaJual);
        jumlahInput.addEventListener('input', hitungHargaJual);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bayarInput = document.getElementById('bayar_input');
        const kembaliInput = document.getElementById('kembali_input');
        const sisaInput = document.getElementById('sisa_input');
        const tableBody = document.querySelector('table tbody');

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

            return total;
        }

        function calculateSisaDanKembali() {
            const totalPembelian = updateTotalPembelian();
            const bayar = parseFloat(bayarInput.value) || 0;
            const sisa = totalPembelian - bayar;
            const kembali = bayar >= totalPembelian ? bayar - totalPembelian : 0;
            sisaInput.value = 'Rp ' + sisa.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            kembaliInput.value = 'Rp ' + kembali.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }


        bayarInput.addEventListener('input', function () {
            calculateSisaDanKembali();
        });

        tableBody.addEventListener('change', function () {
            calculateSisaDanKembali();
        });

        calculateSisaDanKembali();
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


{{-- <script>
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

        let rowCount = 0;

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
                    <td></td>
                    <td>${kodeBarang}</td>
                    <td>${namaBarang}</td>
                    <td>${merek}</td>
                    <td>${harga}</td>
                    <td>${jumlah}</td>
                    <td class="subtotal">${calculatedSubTotal}</td>
                    <td>
                        <button class="btn btn-icon btn-danger btn-rounded remove-row">
                            <i class="anticon anticon-close"></i>
                        </button>
                    </td>
                    <input type="hidden" name="table_data[${rowCount}][id_barang]" value="${idBarang}">
                    <input type="hidden" name="table_data[${rowCount}][kode_barang]" value="${kodeBarang}">
                    <input type="hidden" name="table_data[${rowCount}][nama_barang]" value="${namaBarang}">
                    <input type="hidden" name="table_data[${rowCount}][merek]" value="${merek}">
                    <input type="hidden" name="table_data[${rowCount}][harga]" value="${harga}">
                    <input type="hidden" name="table_data[${rowCount}][jumlah]" value="${jumlah}">
                    <input type="hidden" name="table_data[${rowCount}][subtotal]" value="${calculatedSubTotal.toFixed(2)}">
                </tr>
            `;

            rowCount++;

            const totalPembelianRow = tableBody.querySelector('tr:last-child');
            totalPembelianRow.insertAdjacentHTML('beforebegin', newRow);

            resetForm();
            updateTotalPembelian();
            setRemoveRowEvent();


        });

        function resetForm() {
            namaBarangSelect.value = '';
            kodeBarangInput.value = '';
            merekInput.value = '';
            hargaInput.value = '';
            jumlahInput.value = '';
            bayarInput.value = '';
        }

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
            rowCount = 0; // Reset rowCount
            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input');
                row.querySelector('td:first-child').innerText = index + 1;

                inputs.forEach(input => {
                    const inputName = input.name.replace(/\[\d+\]/, `[${index}]`);
                    input.setAttribute('name', inputName);
                });
                rowCount++;
            });
        }

        function updateTotalPembelian() {
            const tableBody = document.querySelector('table tbody');

            const subtotals = tableBody.querySelectorAll('.subtotal');
            let total = 0;

            subtotals.forEach(function (subtotalCell) {
                // Mengambil nilai subtotal, menghapus karakter yang bukan angka
                const subtotalValue = subtotalCell.textContent.replace(/[^\d.-]/g, ''); // Menghapus simbol lainnya
                if (!isNaN(subtotalValue) && subtotalValue.trim() !== '') {
                    total += parseFloat(subtotalValue);
                }
            });

            const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');
            if (totalPembelianCell) {
                // Menampilkan total dalam format angka bulat tanpa angka desimal
                totalPembelianCell.textContent = Math.floor(total); // Menggunakan Math.floor untuk menghilangkan desimal
            }

            return total;
        }

    });
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

    function setRemoveRowEvent() {
        const removeButtons = document.querySelectorAll('.remove-row');
        removeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const row = button.closest('tr');
                row.remove();
                updateRowNumbers();
                updateTotalPembelian();
            });
        });
    }

    function searchInDatabase(data, callback) {
        $.ajax({
            url: '/validasi-detail-pembelian', // URL controller Anda
            type: 'POST',
            data: { items: data },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Token CSRF
            },
            success: function(response) {
                // Jika respons berhasil, panggil callback
                if (response.status === 'success') {
                    callback(response.data);
                }
            },
            error: function(xhr) {
                // Jika ada error, tampilkan alert dengan pesan dari server
                if (xhr.status === 404) {
                    const errorMessage = xhr.responseJSON.message;
                    alert(errorMessage);
                } else {
                    alert('Terjadi kesalahan saat memvalidasi data.');
                }
            }
        });
    }

    function importToTable(data) {
        const tableBody = document.querySelector('table tbody');

        data.forEach(item => {
            const formattedHarga = item.harga.toString(); // Tidak ada format
            const formattedSubtotal = item.subtotal.toString(); // Tidak ada format

            console.log(item);
            const newRow = `
                <tr>
                    <td></td>
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
                </tr>
            `;

            rowCount++;
            const totalPembelianRow = tableBody.querySelector('tr:last-child');
            totalPembelianRow.insertAdjacentHTML('beforebegin', newRow);

            setRemoveRowEvent();
            updateTotalPembelian();
        });
    }

    function parseCSV(data) {
        const lines = data.split('\n');
        const result = [];
        for (let i = 1; i < lines.length; i++) { // skip header
            const columns = lines[i].split(',');
            result.push({
                kode_barang: columns[0],
                merek: columns[1],
                jumlah: parseInt(columns[2], 10) || 0,
            });
        }
        return result;
    }

    function updateRowNumbers() {
        const tableBody = document.querySelector('table tbody');

        const rows = tableBody.querySelectorAll('tr:not(:last-child)');
        rowCount = 0; // Reset rowCount
        rows.forEach((row, index) => {
            const inputs = row.querySelectorAll('input');
            row.querySelector('td:first-child').innerText = index + 1;

            inputs.forEach(input => {
                const inputName = input.name.replace(/\[\d+\]/, `[${index}]`);
                input.setAttribute('name', inputName);
            });
            rowCount++;
        });
    }

    function updateTotalPembelian() {
        const tableBody = document.querySelector('table tbody');

        const subtotals = tableBody.querySelectorAll('.subtotal');
        let total = 0;

        subtotals.forEach(function (subtotalCell) {
            // Mengambil nilai subtotal, menghapus karakter yang bukan angka
            const subtotalValue = subtotalCell.textContent.replace(/[^\d.-]/g, ''); // Menghapus simbol lainnya
            if (!isNaN(subtotalValue) && subtotalValue.trim() !== '') {
                total += parseFloat(subtotalValue);
            }
        });

        const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');
        if (totalPembelianCell) {
            // Menampilkan total dalam format angka bulat tanpa angka desimal
            totalPembelianCell.textContent = Math.floor(total); // Menggunakan Math.floor untuk menghilangkan desimal
        }

        return total;
    }
</script> --}}


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

        let formRowCount = 0;

        addButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Ambil data dari form
            const namaBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].text;
            const kodeBarang = kodeBarangInput.value;
            const merek = merekInput.value;
            const harga = hargaInput.value;
            const jumlah = jumlahInput.value;
            const subTotal = bayarInput.value;
            const idBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].getAttribute('data-id');

            if (!namaBarang || !kodeBarang || !merek || !harga || !jumlah || !subTotal || !idBarang) {
                console.error("Debugging data kosong:");
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
                subtotal: calculatedSubTotal.toFixed(0)
            };

            const newRow = `
                <tr>
                    <td></td>
                    <td>${kodeBarang}</td>
                    <td>${namaBarang}</td>
                    <td>${merek}</td>
                    <td>${harga}</td>
                    <td>${jumlah}</td>
                    <td class="subtotal">${itemData.subtotal}</td>
                    <td>
                        <button class="btn btn-icon btn-danger btn-rounded remove-row">
                            <i class="anticon anticon-close"></i>
                        </button>
                    </td>
                    <input type="hidden" name="table_data[${formRowCount}][id_barang]" value="${idBarang}">
                    <input type="hidden" name="table_data[${formRowCount}][kode_barang]" value="${kodeBarang}">
                    <input type="hidden" name="table_data[${formRowCount}][nama_barang]" value="${namaBarang}">
                    <input type="hidden" name="table_data[${formRowCount}][merek]" value="${merek}">
                    <input type="hidden" name="table_data[${formRowCount}][harga]" value="${harga}">
                    <input type="hidden" name="table_data[${formRowCount}][jumlah]" value="${jumlah}">
                    <input type="hidden" name="table_data[${formRowCount}][subtotal]" value="${itemData.subtotal}">
                </tr>
            `;

            formRowCount++; // Increment formRowCount untuk form

            const totalPembelianRow = tableBody.querySelector('tr:last-child');
            totalPembelianRow.insertAdjacentHTML('beforebegin', newRow);

            resetForm();
            updateTotalPembelian();
            setRemoveRowEvent();
        });

        function resetForm() {
            namaBarangSelect.value = '';
            kodeBarangInput.value = '';
            merekInput.value = '';
            hargaInput.value = '';
            jumlahInput.value = '';
            bayarInput.value = '';
        }

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
            rowCount = 0;
            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input');
                row.querySelector('td:first-child').innerText = index + 1;

                inputs.forEach(input => {
                    const inputName = input.name.replace(/\[\d+\]/, `[${index}]`);
                    input.setAttribute('name', inputName);
                });
                rowCount++;
            });
        }

        function updateTotalPembelian() {
            const tableBody = document.querySelector('table tbody');
            const subtotals = tableBody.querySelectorAll('.subtotal');
            let total = 0;

            subtotals.forEach(function (subtotalCell) {
                const subtotalValue = subtotalCell.textContent.replace(/[^\d.-]/g, '');
                if (!isNaN(subtotalValue) && subtotalValue.trim() !== '') {
                    total += parseFloat(subtotalValue);
                }
            });

            const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');
            if (totalPembelianCell) {
                totalPembelianCell.textContent = total.toFixed(0); // Pastikan total tanpa desimal
            }

            return total;
        }
    });
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

    let rowCountImport = 0;

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

    // function importToTable(data) {
    //     const tableBody = document.querySelector('table tbody');

    //     data.forEach(item => {
    //         const formattedHarga = item.harga.toString();
    //         const formattedSubtotal = item.subtotal.toString();

    //         const newRow = `
    //             <tr>
    //                 <td></td>
    //                 <td>${item.kode_barang}</td>
    //                 <td>${item.nama_barang}</td>
    //                 <td>${item.merek}</td>
    //                 <td>${formattedHarga}</td>
    //                 <td>${item.jumlah}</td>
    //                 <td class="subtotal">${formattedSubtotal}</td>
    //                 <td>
    //                     <button class="btn btn-icon btn-danger btn-rounded remove-row">
    //                         <i class="anticon anticon-close"></i>
    //                     </button>
    //                 </td>
    //                 <input type="hidden" name="table_data[${importRowCount}][id_barang]" value="${item.id_barang}">
    //                 <input type="hidden" name="table_data[${importRowCount}][kode_barang]" value="${item.kode_barang}">
    //                 <input type="hidden" name="table_data[${importRowCount}][nama_barang]" value="${item.nama_barang}">
    //                 <input type="hidden" name="table_data[${importRowCount}][merek]" value="${item.merek}">
    //                 <input type="hidden" name="table_data[${importRowCount}][harga]" value="${item.harga}">
    //                 <input type="hidden" name="table_data[${importRowCount}][jumlah]" value="${item.jumlah}">
    //                 <input type="hidden" name="table_data[${importRowCount}][subtotal]" value="${item.subtotal}">
    //             </tr>
    //         `;

    //         importRowCount++; // Increment importRowCount untuk import

    //         const totalPembelianRow = tableBody.querySelector('tr:last-child');
    //         totalPembelianRow.insertAdjacentHTML('beforebegin', newRow);

    //         setRemoveRowEvent();
    //         updateTotalPembelian();
    //     });
    // }

    function importToTable(data) {
        const tableBody = document.querySelector('table tbody');
        data.forEach((item, index) => {
            const formattedHarga = item.harga.toString();
            const formattedSubtotal = item.subtotal.toString();

            const newRow = `
                <tr>
                    <td>${index + 1}</td> <!-- Menambahkan nomor urut -->
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
                    <input type="hidden" name="table_data[${rowCountImport}][id_barang]" value="${item.id_barang}">
                    <input type="hidden" name="table_data[${rowCountImport}][kode_barang]" value="${item.kode_barang}">
                    <input type="hidden" name="table_data[${rowCountImport}][nama_barang]" value="${item.nama_barang}">
                    <input type="hidden" name="table_data[${rowCountImport}][merek]" value="${item.merek}">
                    <input type="hidden" name="table_data[${rowCountImport}][harga]" value="${item.harga}">
                    <input type="hidden" name="table_data[${rowCountImport}][jumlah]" value="${item.jumlah}">
                    <input type="hidden" name="table_data[${rowCountImport}][subtotal]" value="${item.subtotal}">
                </tr>
            `;

            rowCountImport++;

            tableBody.insertAdjacentHTML('beforeend', newRow);
        });

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
            removeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const row = button.closest('tr');
                    row.remove();
                    updateRowNumbers();
                    updateTotalPembelian();
                });
            });
        }

    function updateTotalPembelian() {
            const tableBody = document.querySelector('table tbody');
            const subtotals = tableBody.querySelectorAll('.subtotal');
            let total = 0;

            subtotals.forEach(function (subtotalCell) {
                const subtotalValue = subtotalCell.textContent.replace(/[^\d.-]/g, '');
                if (!isNaN(subtotalValue) && subtotalValue.trim() !== '') {
                    total += parseFloat(subtotalValue);
                }
            });

            const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');
            if (totalPembelianCell) {
                totalPembelianCell.textContent = total.toFixed(0); // Pastikan total tanpa desimal
            }

            return total;
        }
</script>


@endpush
