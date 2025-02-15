@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>
            <form action="{{ route('tambah-penjualan') }}" method="POST" id="form-pembelian">
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
                        <input type="text" class="form-control" id="no_nota" placeholder="No Nota" name="no_nota"
                            required>
                    </div>
                </div>
                <div class="form-row align-items-center" style="gap: 15px;">
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="pelanggan">Pelanggan</label>
                            <select id="pelanggan" class="form-control" name="pelanggan" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach ($pelanggan as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-icon btn-success btn-add-pelanggan" id="btn-add-pelanggan"">
                            <i class="anticon anticon-plus"></i>
                        </button>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" class="form-control" id="alamat" placeholder="Alamat" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="kota">Kota</label>
                            <input type="text" class="form-control" id="kota" placeholder="Kota" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="telepon">Telepon</label>
                            <input type="text" class="form-control" id="telepon" placeholder="Telepon" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-row align-items-center" style="gap: 15px;">
                    <div class="form-group col-md-4">
                        <label for="nama_barang">Barang</label>
                        <select id="nama_barang" class="select2 form-control">
                            <option value="">Pilih Barang</option>
                            @foreach ($barang->unique('kode_barang') as $b)
                                <option value="{{ $b->id }}" data-id="{{ $b->id }}" data-harga="{{ $b->harga_jual }}" data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek}}>({{$b->kode_barang}}) {{ $b->nama }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="kode_barang">Kode</label>
                        <input type="text" class="form-control" id="kode_barang" placeholder="Kode" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="merek">Merek</label>
                        <select id="merek" class="form-control" name="merek">
                            <option value="">Pilih Merek</option>
                        </select>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <div class="form-group col-md-2 ">
                        <label for="harga">Harga</label>
                        <input type="text" id="harga" readonly class="form-control-plaintext" style="font-size: x-large;">
                    </div>
                    <div class="form-group col-md-2 ">
                        <label for="diskon">Diskon</label>
                        <input type="text" class="form-control" id="diskon" placeholder="Diskon">
                    </div>
                    <div class="form-group ">
                        <label for="jumlah">Jumlah</label>
                        <input type="text" class="form-control" id="jumlah" placeholder="Jumlah">
                    </div>
                    <div class="form-group col-md-3 ">
                        <label for="sub_total">Sub Total</label>
                        <input type="text" id="sub_total" readonly class="form-control-plaintext" style="font-size: x-large;">
                    </div>
                    <div class="form-group mt-2 " style="gap: 10px;">
                        <button class="btn btn-danger mr-3"  type="button" id="resetButton">Batal</button>
                        <button class="btn btn-success" type="submit">Tambahkan</button>
                    </div>
                </div>

                <button class="btn btn-danger btn-import mb-3 btn-import" id="btn-import">Import</button>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Merek</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Diskon</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Sub Total</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" style="text-align: end">Total Penjualan</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="diskon_nota">Diskon Nota</label>
                            <input type="text" class="form-control" id="diskon_nota" placeholder="Diskon Nota" name="diskon_nota">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="bayar_input">Bayar</label>
                            <input type="text" class="form-control" id="bayar_input" placeholder="Bayar" name="bayar">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="total">Total</label>
                            <input type="text" class="form-control" id="total" placeholder="Total" name="total" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="kembali_input">Kembali</label>
                            <input type="text" class="form-control" id="kembali_input" placeholder="kembali" readonly>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="sisa_input">Sisa</label>
                            <input type="text" class="form-control" id="sisa_input" placeholder="Diskon Nota" name="sisa" readonly>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="/penjualan" class="btn btn-danger mr-3">Batal</a>
                    <button class="btn btn-success" type="submit">simpan</button>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade bd-example-modal-add" style="display: none;" id="pelangganmodal" tabindex="-1" role="dialog" aria-labelledby="pelangganModalLabel" aria-hidden="true"></div>

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"></div>
@endsection

@component('components.aset_datatable.aset_select2')@endcomponent


@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    document.getElementById('resetButton').addEventListener('click', function() {
        document.getElementById('nama_barang').value = '';
        document.getElementById('kode_barang').value = '';
        document.getElementById('diskon').value = '';
        document.getElementById('jumlah').value = '';
        document.getElementById('sub_total').value = '';
        $('#nama_barang').select2().val(null).trigger('change');
        $('#merek').select2().val(null).trigger('change');
        $('#harga').val('');
    });
</script>

<script>
    $(document).ready(function () {
        $('#pelangganmodal').on('shown.bs.modal', function () {
            $('.id_kota').select2({
                width: '100%',
                placeholder: 'Pilih Kota',
            });

            $("#btn-batal").on("click", function () {
                $('.modal').modal('hide');
            });

        });
    });

</script>
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
                    $('#merek').append('<option value="' + item.merek + '" data-harga="' + item.harga_jual + '">' + item.merek + '</option>');
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
            $('#harga').val(harga);
        });

        function formatNumber(value) {
                return new Intl.NumberFormat('id-ID').format(value);
        }
    </script>

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
        $(document).on('click', '.btn-add-pelanggan', function(e) {
            e.preventDefault();
            console.log('test modal add');
            let url = "/modal-tambah-pelanggan";
            $(this).prop('disabled', true)
            $.ajax({
                url,
                type: "GET",
                dataType: "HTML",
                success: function(data) {
                    $('#pelangganmodal').html(data);
                    $('#pelangganmodal').modal('show');
                    $('.btn-add-pelanggan').prop("disabled", false);
                },
                error: function(error) {
                    console.error(error);
                    $('.btn-import').prop('disabled', false);
                }
            })
        })
    </script>

    <script>
        $(document).ready(function () {
            $('#form-tambah-pelanggan').on('submit', function (e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if ($("#pelanggan option[value='" + response.id + "']").length === 0) {
                            $('#pelanggan').append(
                                `<option value="${response.id}">${response.nama}</option>`
                            );
                        }
                        $('#pelanggan').val(response.id).trigger('change');

                        $('#alamat').val(response.alamat);
                        $('#kota').val(response.kota);
                        $('#telepon').val(response.telepon);

                        $('#form-tambah-pelanggan')[0].reset();
                        $('.modal').modal('hide');
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan, silakan coba lagi.');
                    },
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const newPelanggan = @json(session('new_pelanggan'));
            if (newPelanggan) {
                const pelangganSelect = document.getElementById('pelanggan');
                if (!document.querySelector(`#pelanggan option[value="${newPelanggan.id}"]`)) {
                    const newOption = new Option(newPelanggan.nama, newPelanggan.id, true, true);
                    pelangganSelect.add(newOption);
                }

                $('#pelanggan').val(newPelanggan.id).trigger('change');
                document.getElementById('alamat').value = newPelanggan.alamat;
                document.getElementById('kota').value = newPelanggan.kota;
                document.getElementById('telepon').value = newPelanggan.telepon;
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pelangganData = @json($pelanggan);
            const pelangganSelect = document.getElementById('pelanggan');
            const alamatField = document.getElementById('alamat');
            const kotaField = document.getElementById('kota');
            const teleponField = document.getElementById('telepon');

            pelangganSelect.addEventListener('change', function () {
                const selectedId = this.value;

                if (!selectedId) {
                    alamatField.value = '';
                    kotaField.value = '';
                    teleponField.value = '';
                    return;
                }

                fetch(`/pelanggan-detail/${selectedId}`)
                    .then(response => response.json())
                    .then(data => {
                        alamatField.value = data.alamat || '';
                        kotaField.value = data.kota || '';
                        teleponField.value = data.telepon || '';
                    })
                    .catch(error => console.error('Error fetching pelanggan:', error));
            });

        });
    </script>

    <script>
        $(document).on('click', '.btn-import', function(e) {
            e.preventDefault();
            let url = "/modal-import-penjualan";
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
            const merekSelect = document.getElementById('merek');
            const kodeBarangInput = document.getElementById('kode_barang');
            const hargaInput = document.getElementById('harga');
            const jumlahInput = document.getElementById('jumlah');
            const diskonInput = document.getElementById('diskon');
            const subTotalInput = document.getElementById('sub_total');
            const semuaOption = [...namaBarangSelect.options];
            const tableBody = document.querySelector('table tbody');

            const addButton = document.querySelector('button[type="submit"]');

            function formatNumber(value) {
                return new Intl.NumberFormat('id-ID').format(value);
            }

            function formatCurrencyInput(input) {
                let value = input.value.replace(/[^\d]/g, '');
                if (value) {
                    input.value = formatNumber(value);
                }
            }

            // namaBarangSelect.addEventListener('change', function () {
            //     const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
            //     const kodeBarang = selectedOption.getAttribute('data-kode');
            //     const harga = selectedOption.getAttribute('data-harga');
            //     const merek = selectedOption.getAttribute('data-merek');
            //     const idBarang = selectedOption.getAttribute('data-id');

            //     kodeBarangInput.value = kodeBarang ? kodeBarang : '';
            //     hargaInput.value = harga ? formatNumber(harga) : '';
            //     merekSelect.innerHTML = '<option value="">Pilih Merek</option>';
            //     semuaOption.forEach(option => {
            //         if (option.getAttribute('data-kode') === kodeBarang) {
            //             const merekOption = document.createElement('option');
            //             merekOption.value = option.getAttribute('data-merek');
            //             merekOption.textContent = option.getAttribute('data-merek');
            //             merekSelect.appendChild(merekOption);
            //         }
            //     });
            // });

            merekSelect.addEventListener('change', function () {
                const selectedMerek = merekSelect.options[merekSelect.selectedIndex].value;
                console.log('Merek yang dipilih:', selectedMerek);
            });

            function calculateSubtotal() {
                const harga = parseFloat(hargaInput.value.replace(/\./g, '').replace(',', '.')) || 0;
                const jumlah = parseFloat(jumlahInput.value) || 0;

                const diskon = parseFloat(diskonInput.value.replace(/[^\d]/g, '')) || 0;

                const hargaSetelahDiskon = harga - diskon;
                const subTotal = hargaSetelahDiskon * jumlah;

                subTotalInput.value = formatNumber(subTotal);
            }

            diskonInput.addEventListener('input', function () {
                formatCurrencyInput(diskonInput);
                calculateSubtotal();
            });

            [hargaInput, jumlahInput, diskonInput].forEach(input => {
                input.addEventListener('input', calculateSubtotal);
            });



            let rowCount = 0;

            addButton.addEventListener('click', function(e) {
                e.preventDefault();

                const selectedMerek = merekSelect.options[merekSelect.selectedIndex].value;
                const namaBarang = namaBarangSelect.options[namaBarangSelect.selectedIndex].text;
                const cleanedNamaBarang = namaBarang.replace(/\(.*?\)\s*/, '');
                const kodeBarang = kodeBarangInput.value;
                const merek = selectedMerek;
                const harga = hargaInput.value;
                const jumlah = jumlahInput.value;
                const subTotal = subTotalInput.value;
                const diskon = diskonInput.value;

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

                const itemData = {
                    id_barang: idBarang,
                    kode_barang: kodeBarang,
                    nama_barang: cleanedNamaBarang,
                    merek: merek,
                    harga: harga,
                    diskon : diskon,
                    jumlah: jumlah,
                    subtotal: subTotal
                };



                const newRow = `
                <tr>
                    <td></td>
                    <td>${kodeBarang}</td>
                    <td>${cleanedNamaBarang}</td>
                    <td>${merek}</td>
                    <td>${harga}</td>
                    <td>${diskon}</td>
                    <td>${jumlah}</td>
                    <td class="subtotal">${subTotal}</td>
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
                    <input type="hidden" name="table_data[${rowCount}][subtotal]" value="${subTotal}">
                    <input type="hidden" name="table_data[${rowCount}][diskon]" value="${diskon}">

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
                merek.value = '';
                hargaInput.value = '';
                jumlahInput.value = '';
                diskonInput.value = '';
                subTotalInput.value = '';

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
                        const updatedResponse = response.map(item => {
                            const parsedItem = parsedData.find(parsed => parsed.kode_barang === item.kode_barang);
                            if (parsedItem) {
                                item.diskon = parsedItem.diskon
                            }
                            return item;
                        });
                        $('.modal').modal('hide');
                        importToTable(updatedResponse);
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
                url: '/validasi-detail-penjualan',
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
                const formattedHarga = formatNumber(item.harga);
                const formattedDiskon = formatNumber(item.diskon);
                const formattedSubtotal = formatNumber(item.subtotal);

                const newRow = `
                <tr>
                    <td></td>
                    <td>${item.kode_barang}</td>
                    <td>${item.nama_barang}</td>
                    <td>${item.merek}</td>
                    <td>${formattedHarga}</td>
                    <td>${formattedDiskon}</td> <!-- Tampilkan diskon dengan format rupiah -->
                    <td>${item.jumlah}</td>
                    <td class="subtotal">${formattedSubtotal}</td> <!-- Tampilkan subtotal dengan format rupiah -->
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
                    <input type="hidden" name="table_data[${rowCount}][diskon]" value="${item.diskon}">
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


        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }


        function parseCSV(data) {
            const lines = data.split('\n');
            const result = [];
            for (let i = 1; i < lines.length; i++) {
                const columns = lines[i].split(',');

                if (columns[0] && columns[0] !== undefined && columns[0].trim() !== '') {
                    result.push({
                        kode_barang: columns[0],
                        merek: columns[1],
                        jumlah: parseInt(columns[2], 10) || 0,
                        diskon: parseInt(columns[3], 10) || 0,
                    });
                }
            }
            console.log(result);
            return result;
        }


        function updateRowNumbers() {
            const tableBody = document.querySelector('table tbody');

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const diskonNotaInput = document.getElementById('diskon_nota');
            const bayarInput = document.getElementById('bayar_input');
            const kembaliInput = document.getElementById('kembali_input');
            const sisaInput = document.getElementById('sisa_input');
            const totalInput = document.getElementById('total');
            const tableBody = document.querySelector('table tbody');

            function formatRupiah(value) {
                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function parseRupiah(value) {
                return parseFloat(value.replace(/[^0-9]/g, '') || 0);
            }

            function updateTotalPembelian() {
                const subtotals = tableBody.querySelectorAll('.subtotal');
                let total = 0;

                subtotals.forEach(function (subtotalCell) {
                    const subtotalValue = parseRupiah(subtotalCell.textContent) || 0;
                    total += subtotalValue;
                });


                const diskonNota = parseRupiah(diskonNotaInput.value) || 0;
                total -= diskonNota;

                total = Math.max(total, 0);

                totalInput.value = formatRupiah(total);

                return total;
            }

            function calculateSisaDanKembali() {
                const totalPembelian = updateTotalPembelian();
                const bayar = parseRupiah(bayarInput.value) || 0;
                const sisa = Math.max(totalPembelian - bayar, 0);
                const kembali = Math.max(bayar - totalPembelian, 0);

                sisaInput.value = formatRupiah(sisa);
                kembaliInput.value = formatRupiah(kembali);
            }

            function formatInputAsRupiah(event) {
                const input = event.target;
                const value = parseRupiah(input.value);
                input.value = formatRupiah(value);
            }

            diskonNotaInput.addEventListener('input', function (event) {
                formatInputAsRupiah(event);
                calculateSisaDanKembali();
            });

            bayarInput.addEventListener('input', function (event) {
                formatInputAsRupiah(event);
                calculateSisaDanKembali();
            });

            tableBody.addEventListener('change', function () {
                calculateSisaDanKembali();
            });

            calculateSisaDanKembali();
        });
    </script>


@endpush
