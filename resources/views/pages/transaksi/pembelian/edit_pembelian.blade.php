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
                                <input type="text" class="form-control" id="tanggal" name="tanggal" placeholder="Pilih Tanggal" value="{{ date('d-m-Y', strtotime($pembelian->tanggal)) }}" required/>

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
                            <select id="nama_barang" class="select2 form-control">
                                <option value="">Pilih Barang</option>
                                @foreach ($barang->unique('kode_barang') as $b)
                                    <option value="{{ $b->id}}" data-id="{{ $b->id }}" data-nama="{{ $b->nama }}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek}}>({{$b->kode_barang}}) {{ $b->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="kode_barang">Kode</label>
                            <input type="text" class="form-control" id="kode_barang">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="merek">Merek</label>
                            <select id="merek" class="form-control" >
                                <option value="">Pilih Barang</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="harga">Harga</label>
                            <input type="text" class="form-control" id="harga" placeholder="Harga">
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
                            <button class="btn btn-danger mr-3"  type="button" id="resetButton">Batal</button>
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
                        <a href="/pembelian" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Tambahkan</button>
                    </div>
            </form>
        </div>
    </div>

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"></div>

@endsection

@component('components.aset_datatable.aset_select2')@endcomponent


@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .txt{
        text-align: center;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kodeBarangInput = document.getElementById('kode_barang');
        const namaBarangSelect = document.getElementById('nama_barang');
        const merekSelect = document.getElementById('merek');
        const hargaInput = document.getElementById('harga');

        var filteredMerek = @json($barang);

        kodeBarangInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const kodeBarang = kodeBarangInput.value.trim();

                if (kodeBarang) {
                    fetch(`/api/barang/${kodeBarang}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const barangList = data.barang;

                                console.log(barangList);

                                const option = document.createElement('option');
                                option.value = barangList[0].id;
                                option.text = `(${barangList[0].kode_barang}) ${barangList[0].nama}`;
                                option.setAttribute('data-id', barangList[0].id);
                                option.setAttribute('data-nama', barangList[0].nama);
                                option.setAttribute('data-harga', barangList[0].harga);
                                option.setAttribute('data-kode', barangList[0].kode_barang);
                                option.setAttribute('data-merek', barangList[0].merek);
                                namaBarangSelect.innerHTML = '';
                                namaBarangSelect.appendChild(option);
                                $('#nama_barang').val(barangList[0].id).trigger('change');

                                merekSelect.innerHTML = '';
                                barangList.forEach(barang => {
                                    const merekOption = document.createElement('option');
                                    merekOption.value = barang.merek;
                                    merekOption.text = barang.merek;
                                    merekOption.setAttribute('data-harga', barang.harga);
                                    merekSelect.appendChild(merekOption);
                                });
                                $('#merek').val('').trigger('change');

                                hargaInput.value = '';
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Kode barang tidak ditemukan!',
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching barang:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Kode barang tidak ditemukan!',
                            });
                        });
                }
            }
        });

        merekSelect.addEventListener('change', function() {
            const selectedMerek = merekSelect.options[merekSelect.selectedIndex];
            const harga = selectedMerek.getAttribute('data-harga');
            hargaInput.value = harga;
        });

        kodeBarangInput.addEventListener('input', function() {
            if (kodeBarangInput.value.trim() === '') {
                namaBarangSelect.innerHTML = '<option value="">Pilih Barang</option>';
                filteredMerek.forEach(barang => {
                    const option = document.createElement('option');
                    option.value = barang.id;
                    option.text = `(${barang.kode_barang}) ${barang.nama}`;
                    option.setAttribute('data-id', barang.id);
                    option.setAttribute('data-nama', barang.nama);
                    option.setAttribute('data-harga', barang.harga);
                    option.setAttribute('data-kode', barang.kode_barang);
                    option.setAttribute('data-merek', barang.merek);
                    namaBarangSelect.appendChild(option);
                });

                $('#nama_barang').val('').trigger('change');
                merekSelect.innerHTML = '<option value="">Pilih Barang</option>';
                hargaInput.value = '';
            }
        });
    });
</script>

<script>
    document.getElementById('resetButton').addEventListener('click', function() {
        document.getElementById('nama_barang').value = '';
        document.getElementById('kode_barang').value = '';
        document.getElementById('harga').value = '';
        document.getElementById('jumlah').value = '';
        document.getElementById('sub_total').value = '';
        $('#nama_barang').select2().val(null).trigger('change');
        $('#merek').select2().val(null).trigger('change');
    });
</script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    function updateFileName() {
        var input = document.getElementById('customFile');
        var label = document.querySelector('.custom-file-label');
        label.textContent = input.files[0] ? input.files[0].name : 'Choose file';
    }
</script>
<script>
    $('.select2').select2({
        width: '100%',
        placeholder: 'Pilih Barang',
    });

    $('#nama_barang').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var kodeBarang = selectedOption.data('kode');
        var namaBarang = selectedOption.data('nama');

        $('#harga').val('');

        $('#kode_barang').val(kodeBarang);
        var filteredMerek = @json($barang);

        $('#merek').empty().append('<option value="">Pilih Merek</option>');

        filteredMerek.forEach(function(item) {
            if (item.kode_barang === kodeBarang) {
                $('#merek').append('<option value="' + item.merek + '" data-harga="' + item.harga + '">' + item.merek + '</option>');
            }
        });

        $('#merek').select2({
            width: '100%',
            placeholder: 'Pilih Merek'
        });
    });

    $('#merek').on('change', function() {
        var selectedMerek = $(this).find('option:selected');
        var harga = formatNumber(selectedMerek.data('harga'));
    });

    $('#harga').on('input', function() {
        var value = $(this).val().replace(/[^\d]/g, '');
        $(this).val(formatNumber(value));
    });

    function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
    }
</script>

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
    document.addEventListener('DOMContentLoaded', function() {
        const hargaInput = document.getElementById('harga');
        const jumlahInput = document.getElementById('jumlah');
        const bayarInput = document.getElementById('sub_total');
        const form = document.getElementById('form-pembelian');
        const tableBody = document.querySelector('#table-body');

        function hitungHargaJual() {

            const harga = parseFloat(hargaInput.value.replace(/[^\d]/g, '')) || 0;
            const jumlah = jumlahInput.value || 0;

            const hargaJual = harga * jumlah;
            bayarInput.value = 'Rp ' + hargaJual.toFixed(2).replace(/\d(?=(\d{3})+\.)/g,'$&,');
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
                    $('.modal').modal('hide');
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

            function formatRibuan(value) {
                    return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function formatToRupiah(value) {
                return 'Rp ' + parseFloat(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            const formattedHarga = formatRibuan(item.harga.toString());
            const formattedSubtotal = formatToRupiah(item.subtotal.toString());
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

        updateRowNumbers();
        setRemoveRowEvent();
        updateTotalPembelian();
    }

    function parseCSV(data) {
        const lines = data.split('\n');
        const result = [];
        for (let i = 1; i < lines.length; i++) {
            const columns = lines[i].split(',');
            result.push({
                kode_barang: columns[0],
                merek: columns[1],
                jumlah: columns[2],
                harga: columns[3],
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
                updateRowNumbers();
                updateTotalPembelian();
            });
        });
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll('#table-body-content tr');
        rows.forEach((row, index) => {
            const indexCell = row.querySelector('td:first-child');
            if (indexCell) {
                indexCell.textContent = index + 1;
            }
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const namaBarangSelect = document.getElementById('nama_barang');
        const hargaInput = document.getElementById('harga');
        const kodeBarangInput = document.getElementById('kode_barang');
        const bayarInput = document.getElementById('bayar_input');
        const subTotalInput = document.getElementById('sub_total');
        const merekInput = document.getElementById('merek');
        const jumlahInput = document.getElementById('jumlah');
        const addButton = document.querySelector('button[type="submit"]');
        const tableBody = document.querySelector('table tbody');
        let rowCount = 0;

        const itemsFromDB = @json($detailPembelian);
        const bayarFromDB = @json($bayar);

        function formatRupiah(value) {
            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        bayarInput.value = formatRupiah(bayarFromDB);

        itemsFromDB.forEach(function (item) {
            addRowToTable(item);
        });

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }
        // namaBarangSelect.addEventListener('change', function () {
        //     const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
        //     const harga = selectedOption.getAttribute('data-harga');
        //     const kodeBarang = selectedOption.getAttribute('data-kode');

        //     const merek = selectedOption.getAttribute('data-merek');

        //     function formatRibuan(value) {
        //             return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        //     }

        //     hargaInput.value = harga ? formatRibuan(harga) : '';
        //     kodeBarangInput.value = kodeBarang ? kodeBarang : '';
        //     merekInput.value = merek ? merek : '';
        // });

        addButton.addEventListener('click', function (e) {
            e.preventDefault();

            const namaBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].text;
            const cleanedNamaBarang = namaBarang.replace(/\(.*?\)\s*/, '');
            const kodeBarang = kodeBarangInput.value;
            const merek = merekInput.value;
            const harga = hargaInput.value;
            const cleanHarga = parseFloat(harga.replace(/[^\d]/g, '')) || 0;
            const jumlah = jumlahInput.value;
            const idBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].getAttribute('data-id');


            if (!namaBarang || !kodeBarang || !merek || !harga || !jumlah || !idBarang) {

            console.error("Debugging data kosong:");
            if (!namaBarang) console.error("Nama Barang kosong.");
            if (!kodeBarang) console.error("Kode Barang kosong.");
            if (!merek) console.error("Merek kosong.");
            if (!harga) console.error("Harga kosong.");
            if (!jumlah) console.error("Jumlah kosong.");
            if (!idBarang) console.error("ID Barang kosong.");

            alert('Mohon lengkapi semua data sebelum menambahkan!');
            return;
            }

            const calculatedSubTotal = cleanHarga * jumlah;

            const itemData = {
                id_barang: idBarang,
                kode_barang: kodeBarang,
                nama_barang: cleanedNamaBarang,
                merek: merek,
                harga: harga,
                jumlah: jumlah,
                subtotal: calculatedSubTotal.toFixed(0)
            };

            addRowToTable(itemData);
            resetForm();
            updateTotalPembelian();
        });


        function formatToRupiah(value) {
            return 'Rp ' + parseFloat(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function formatRibuan(value) {
            const numericValue = value.toString().replace(/[^0-9]/g, '');
            return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function addRowToTable(itemData) {
            const formattedHarga = formatRibuan(itemData.harga);
            const formattedSubtotal = formatToRupiah(itemData.subtotal);

            const kodeBarang = itemData.barang ? itemData.barang.kode_barang : itemData.kode_barang;

            const newRow = `
                <tr>
                    <td>${++rowCount}</td>
                    <td>${kodeBarang}</td> <!-- Gunakan kode_barang dari itemData -->
                    <td>${itemData.nama_barang}</td>
                    <td>${itemData.merek}</td>
                    <td>${formattedHarga}</td> <!-- Format harga -->
                    <td>${itemData.jumlah}</td>
                    <td class="subtotal">${formattedSubtotal}</td> <!-- Format subtotal -->
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

        function resetForm() {
            namaBarangSelect.value = '';
            kodeBarangInput.value = '';
            merekInput.value = '';
            hargaInput.value = '';
            jumlahInput.value = '';
            subTotalInput.value = '';
        }

        tableBody.addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                const row = e.target.closest('tr');
                row.remove();
                updateRowNumbers();
                updateTotalPembelian();
            }
        });

        function updateRowNumbers() {
            const rows = tableBody.querySelectorAll('tr');
            rowCount = 0;
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').innerText = index + 1;
                rowCount++;
            });
        }


        function updateTotalPembelian() {
            const subtotals = tableBody.querySelectorAll('.subtotal');
            let total = 0;

            subtotals.forEach(function (subtotalCell) {
                const subtotalValue = subtotalCell.textContent.replace(/[^\d]/g, '');

                if (!isNaN(subtotalValue) && subtotalValue.trim() !== '') {
                    total += parseFloat(subtotalValue);
                }
            });

            document.getElementById('total-pembelian').textContent = 'Rp ' + total.toFixed(0);
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const bayarInput = document.getElementById('bayar_input');
    const kembaliInput = document.getElementById('kembali_input');
    const sisaInput = document.getElementById('sisa_input');
    const tableBody = document.querySelector('table tbody');

    function formatRupiah(value) {
        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseRupiah(value) {
        return parseFloat(value.replace(/[^\d.-]/g, '')) || 0;
    }

    function parseBayar(value) {
        return parseFloat(value.replace(/[^0-9]/g, '')) || 0;
    }

    function updateTotalPembelian() {
        const subtotalCells = tableBody.querySelectorAll('.subtotal');
        let total = 0;

        subtotalCells.forEach(function (subtotalCell) {
            const subtotalValue = parseRupiah(subtotalCell.textContent);
            total += subtotalValue;
        });

        const totalPembelianCell = document.getElementById('total-pembelian');
        if (totalPembelianCell) {
            totalPembelianCell.textContent = formatRupiah(total.toFixed(0));
        }

        return total;
    }

    function calculateSisaDanKembali() {
        const totalPembelian = updateTotalPembelian();
        const bayar = parseBayar(bayarInput.value);

        const sisa = totalPembelian - bayar;
        const kembali = bayar >= totalPembelian ? bayar - totalPembelian : 0;

        sisaInput.value = sisa > 0 ? formatRupiah(sisa.toFixed(0)) : formatRupiah(0);
        kembaliInput.value = kembali > 0 ? formatRupiah(kembali.toFixed(0)) : formatRupiah(0);
    }


    bayarInput.addEventListener('input', function () {
        const cursorPosition = bayarInput.selectionStart;
        const bayarValue = bayarInput.value.replace(/[^\d]/g, '');
        bayarInput.value = formatRupiah(bayarValue);
        bayarInput.setSelectionRange(cursorPosition, cursorPosition);
        calculateSisaDanKembali();
    });


    tableBody.addEventListener('input', function () {
        updateTotalPembelian();
        calculateSisaDanKembali();
    });

    updateTotalPembelian();
    calculateSisaDanKembali();
});

</script>




@endpush
