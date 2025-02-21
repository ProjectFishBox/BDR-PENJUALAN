@extends('components._partials.layout')

@section('content')
<div class="notification-toast top-right" id="notification-toast"></div>
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>
            <form action="{{ route('tambah-pembelian') }}" method="POST" id="form-pembelian">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Tanggal <span style="color: red">*</span></label>
                        <div class="input-affix m-b-10">
                            <i class="prefix-icon anticon anticon-calendar"></i>
                            <input type="text" class="form-control" id="tanggal" name="tanggal" placeholder="Pilih Tanggal" required/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="no_nota">No Nota <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="no_nota" placeholder="No Nota" name="no_nota"
                            required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="kontainer">Kontainer <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="kontainer" placeholder="Kontainer" name="kontainer"
                            required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="nama_barang">Barang</label>
                        <select id="nama_barang" class="select2 form-control">
                            <option value="">Pilih Barang</option>
                            @foreach ($barang->unique('kode_barang') as $b)
                                <option value="{{ $b->id }}" data-id="{{ $b->id }}"
                                    data-nama="{{ $b->nama }}" data-harga="{{ $b->harga }}"
                                    data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek }}>({{$b->kode_barang}}){{ $b->nama }}
                                </option>
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
                            <input type="text" class="form-control" id="bayar_input" placeholder="Bayar" name="bayar" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="kembali_input">Kembali</label>
                            <input type="text" class="form-control" id="kembali_input" placeholder="Kembali"
                                readonly>
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
                    <button class="btn btn-success" type="submit" id="submitButton">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1"
        role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"></div>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .txt {
            text-align: center;
        }
    </style>
@endpush

@component('components.aset_datatable.aset_select2')@endcomponent

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
                    fetch(`/tambah-pembelian/barang/${kodeBarang}`)
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

<script>
    document.getElementById('submitButton').addEventListener('click', function (event) {

        let tableBody = document.querySelector("#table-body tbody");
        let rows = tableBody.getElementsByTagName("tr");

        if (rows.length === 1) {
            event.preventDefault();
            console.log("Toast Harus Muncul!");
            showToast("Harap tambahkan data terlebih dahulu sebelum submit!");
        }
    });

    function showToast(message) {

        var toastHTML = `<div class="toast fade hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
            <div class="toast-header">
                <i class="anticon anticon-info-circle text-danger m-r-5"></i>
                <strong class="mr-auto">Peringatan</strong>
                <small>Baru saja</small>
                <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">${message}</div>
        </div>`;

        $('#notification-toast').append(toastHTML);

        let $toast = $('#notification-toast .toast:last-child');
        console.log("Toast Element:", $toast);

        $toast.toast('show');

        setTimeout(function(){
            $toast.remove();
        }, 3000);
    }
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
                bayarInput.value = 'Rp ' + hargaJual.toFixed(2).replace(/\d(?=(\d{3})+\.)/g,'$&,'); // Sisa dengan format Rp
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
                    const totalFormat = parseFloat(subtotalValue.replace(/[^\d]/g, ''));


                    if (!isNaN(totalFormat)) {
                        total += totalFormat;
                    }
                });

                let totalValueFormat = 0;
                const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');

                if (totalPembelianCell) {
                    const totalValue = totalPembelianCell.textContent;
                    totalValueFormat = parseFloat(totalValue.replace(/[^\d]/g, '')) || 0;
                }

                return totalValueFormat;
            }

            function formatToRupiah(value) {
                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            function parseRupiah(value) {
                return parseFloat(value.replace(/[^\d]/g, '')) || 0;
            }

            function calculateSisaDanKembali() {
                const totalPembelian = updateTotalPembelian();
                const bayar = parseRupiah(bayarInput.value);

                if (bayar > totalPembelian) {
                    const kembali = bayar - totalPembelian;
                    kembaliInput.value = formatToRupiah(kembali.toFixed(2));
                    sisaInput.value = formatToRupiah(0);
                    console.warn('Sisa tidak boleh minus. Uang kembali dihitung.');
                } else {
                    const sisa = totalPembelian - bayar;
                    const kembali = 0;
                    sisaInput.value = formatToRupiah(sisa.toFixed(2));
                    kembaliInput.value = formatToRupiah(kembali.toFixed(2));
                }
            }

            bayarInput.addEventListener('input', function (e) {
                const rawValue = parseRupiah(e.target.value);
                bayarInput.value = formatToRupiah(rawValue);

                if (rawValue > updateTotalPembelian()) {
                    console.warn('Jumlah bayar lebih besar dari total pembelian.');
                }

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const namaBarangSelect = document.getElementById('nama_barang');
            const hargaInput = document.getElementById('harga');
            const kodeBarangInput = document.getElementById('kode_barang');
            const merekInput = document.getElementById('merek');
            const jumlahInput = document.getElementById('jumlah');
            const bayarInput = document.getElementById('sub_total');
            const addButton = document.querySelector('button[type="submit"]');
            const tableBody = document.querySelector('table tbody');

            let rowCount = 0;

            addButton.addEventListener('click', function(e) {
                e.preventDefault();

                const namaBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].text;
                const cleanedNamaBarang = namaBarang.replace(/\(.*?\)\s*/, '');
                const kodeBarang = kodeBarangInput.value;
                const merek = merekInput.value;
                const harga = hargaInput.value;

                const cleanHarga = parseFloat(harga.replace(/[^\d]/g, '')) || 0;
                const jumlah = jumlahInput.value;
                const subTotal = parseFloat(bayarInput.value.replace(/[^\d]/g, ''));
                const idBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].getAttribute(
                    'data-id');

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


                const calculatedSubTotal = cleanHarga * jumlah;
                const subTotalCurrency = 'Rp ' + calculatedSubTotal.toFixed(0).replace(/\d(?=(\d{3})+(?!\d))/g, '$&.') ;

                const itemData = {
                    id_barang: idBarang,
                    kode_barang: kodeBarang,
                    nama_barang: cleanedNamaBarang,
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
                    <td class="subtotal">${subTotalCurrency}</td>
                    <td>
                        <button class="btn btn-icon btn-danger btn-rounded remove-row">
                            <i class="anticon anticon-close"></i>
                        </button>
                    </td>
                    <input type="hidden" name="table_data[${rowCount}][id_barang]" value="${idBarang}">
                    <input type="hidden" name="table_data[${rowCount}][kode_barang]" value="${kodeBarang}">
                    <input type="hidden" name="table_data[${rowCount}][nama_barang]" value="${cleanedNamaBarang}">
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
                updateRowNumbers();


            });

            function resetForm() {
                namaBarangSelect.value = '';
                kodeBarangInput.value = '';
                merekInput.value = '';
                hargaInput.value = '';
                jumlahInput.value = '';
                bayarInput.value = '';
                $('#nama_barang').select2().val(null).trigger('change');
                $('#merek').select2().val(null).trigger('change');
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
                const rows = tableBody.querySelectorAll(
                    'tr:not(:last-child)');
                rows.forEach((row, index) => {
                    row.querySelector('td:first-child').innerText = index + 1;
                    const inputs = row.querySelectorAll('input');
                    inputs.forEach(input => {
                        const inputName = input.name.replace(/\[\d+\]/, `[${index}]`);
                        input.setAttribute('name', inputName);
                    });
                });
            }

            function updateTotalPembelian() {
                const subtotals = tableBody.querySelectorAll('.subtotal');
                let total = 0;

                subtotals.forEach(function(subtotalCell) {
                    const subtotalValue = subtotalCell.textContent.replace(/[^\d]/g, '');

                    console.log('jumlah subtotal',subtotalValue)

                    if (!isNaN(subtotalValue) && subtotalValue.trim() !== '') {
                        total += parseFloat(subtotalValue);
                    }
                });

                const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');
                if (totalPembelianCell) {
                    totalPembelianCell.textContent = 'Rp ' + total.toLocaleString('id-ID');
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

        function setRemoveRowEvent() {
            const removeButtons = document.querySelectorAll('.remove-row');
            removeButtons.forEach(button => {
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
                url: '/validasi-detail-pembelian',
                type: 'POST',
                data: {
                    items: data
                },
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

        function importToTable(data) {
            const tableBody = document.querySelector('table tbody');

            data.forEach(item => {

                function formatRibuan(value) {
                    return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
                const formattedHarga = item.harga.toString();
                const hargaFormatThousand = formatRibuan(formattedHarga)
                const formattedSubtotal = item.subtotal.toString();
                const subTotalCurrency = 'Rp ' + formattedSubtotal.replace(/\d(?=(\d{3})+(?!\d))/g, '$&.') ;



                const newRow = `
        <tr>
            <td></td>
            <td>${item.kode_barang}</td>
            <td>${item.nama_barang}</td>
            <td>${item.merek}</td>
            <td>${hargaFormatThousand}</td>
            <td>${item.jumlah}</td>
            <td class="subtotal">${subTotalCurrency}</td>
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
                    jumlah: parseInt(columns[2], 10) || 0,
                    harga: parseInt(columns[3], 10) || 0
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

            subtotals.forEach(function(subtotalCell) {
                const subtotalValue = subtotalCell.textContent.replace(/[^\d]/g, '');

                console.log('data subtotal import', subtotalValue);
                if (!isNaN(subtotalValue) && subtotalValue.trim() !== '') {
                    total += parseFloat(subtotalValue);

                    console.log('data subtotal import', subtotalValue);

                }
            });

            const totalPembelianCell = tableBody.querySelector('tr:last-child td:nth-child(2)');
            if (totalPembelianCell) {
                    totalPembelianCell.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            return total;
        }
    </script>
@endpush
