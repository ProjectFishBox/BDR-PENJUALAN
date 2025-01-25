@extends('components._partials.layout')

@section('content')
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
                            <input type="text" class="form-control datepicker-input" placeholder="Piih Tanggal"
                                name="tanggal" required>
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
                            <select id="pelanggan" class="form-control">
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
                        <select id="nama_barang" class="form-control">
                            <option value="">Pilih Barang</option>
                            @foreach ($barang as $b)
                                <option value="{{ $b->id }}" data-harga="{{ $b->harga }}" data-kode="{{ $b->kode_barang }}" data-merek={{ $b->merek}}>{{ $b->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="kode_barang">Kode</label>
                        <input type="text" class="form-control" id="kode_barang" placeholder="Kode" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="merek">Merek</label>
                        <select id="merek" class="form-control">
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
                        <a href="/setharga" class="btn btn-danger">Batal</a>
                        <button class="btn btn-success" type="submit">Tambahkan</button>
                    </div>
                </div>

                <button class="btn btn-danger btn-import" id="btn-import">Import</button>

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
                            <label for="bayar">Bayar</label>
                            <input type="text" class="form-control" id="bayar" placeholder="Bayar">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="total">Total</label>
                            <input type="text" class="form-control" id="total" placeholder="Diskon Nota" name="total">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="kembali">Kembali</label>
                            <input type="text" class="form-control" id="kembali" placeholder="kembali">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sisa">Sisa</label>
                            <input type="text" class="form-control" id="sisa" placeholder="Diskon Nota" name="sisa">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade bd-example-modal-add" style="display: none;" id="pelangganmodal" tabindex="-1" role="dialog" aria-labelledby="pelangganModalLabel" aria-hidden="true"></div>
@endsection

@push('css')
    <link href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <style>
        .txt {
            text-align: center;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $('.datepicker-input').datepicker();
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
            // Event submit form modal
            $('#form-tambah-pelanggan').on('submit', function (e) {
                e.preventDefault(); // Mencegah reload halaman

                let formData = $(this).serialize(); // Mengambil data dari form modal

                $.ajax({
                    url: $(this).attr('action'), // URL yang didefinisikan pada atribut action
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        // Tambahkan data pelanggan baru ke dropdown
                        $('#pelanggan').append(
                            `<option value="${response.id}" selected>${response.nama}</option>`
                        );

                        // Isi otomatis alamat, kota, dan telepon
                        $('#alamat').val(response.alamat);
                        $('#kota').val(response.kota);
                        $('#telepon').val(response.telepon);

                        // Tutup modal
                        $('#form-tambah-pelanggan')[0].reset(); // Reset form
                        $('.modal').modal('hide'); // Tutup modal
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan, silakan coba lagi.');
                    },
                });
            });
        });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const newPelanggan = @json(session('new_pelanggan'));
            if (newPelanggan) {
                // Set pelanggan yang baru ditambahkan di select
                const pelangganSelect = document.getElementById('pelanggan');
                const newOption = new Option(newPelanggan.nama, newPelanggan.id, true, true);
                pelangganSelect.add(newOption);

                // Isi field alamat, kota, dan telepon
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

                // AJAX Request
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
    document.addEventListener('DOMContentLoaded', function () {
    const namaBarangSelect = document.getElementById('nama_barang');
    const merekSelect = document.getElementById('merek');
    const kodeBarangInput = document.getElementById('kode_barang');
    const semuaOption = [...namaBarangSelect.options];

    namaBarangSelect.addEventListener('change', function () {
        const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
        const kodeBarang = selectedOption.getAttribute('data-kode');

        merekSelect.innerHTML = '<option value="">Pilih Merek</option>';
        kodeBarangInput.value = kodeBarang  ? kodeBarang  : '';

        semuaOption.forEach(option => {
            if (option.getAttribute('data-kode') === kodeBarang) {
                const merek = option.getAttribute('data-merek');
                const merekOption = document.createElement('option');
                merekOption.value = merek;
                merekOption.textContent = merek;
                merekSelect.appendChild(merekOption);
            }
        });
    });
});

</script>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const namaBarangSelect = document.getElementById('nama_barang');
        const kodeBarangInput = document.getElementById('kode_barang');

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        namaBarangSelect.addEventListener('change', function () {
            const selectedOption = namaBarangSelect.options[namaBarangSelect.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga');
            const kodeBarang = selectedOption.getAttribute('data-kode');
            const  merek= selectedOption.getAttribute('data-merek');

            hargaInput.value = harga ? formatNumber(harga) : '';
            kodeBarangInput.value = kodeBarang  ? kodeBarang  : '';
            merekInput.value = merek  ? merek  : '';

            merekSelect.innerHTML = '<option value="">Pilih Merek</option>';

        });
    });
</script> --}}

@endpush
