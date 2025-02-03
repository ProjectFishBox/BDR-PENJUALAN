@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>

            <form action="{{ route('tambah-akses')}}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama">Nama Akses  <span style="color: red">*</span></label>
                        <input type="text" class="form-control" id="nama" placeholder="Nama Akses" name="nama" required>
                    </div>
                </div>
                <h5 class="mb-3">Akses Menu  <span style="color: red">*</span></h5>
                <div class="form-group">
                    <div class="checkbox">
                        <input id="dashboard" type="checkbox" name="dashboard">
                        <label for="dashboard">Dashboard</label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <h5 class="mb-3">Master</h5>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="lokasi" type="checkbox" name="master_lokasi">
                                <label for="lokasi">Lokasi</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="akses" type="checkbox" name="master_akses">
                                <label for="akses">Akses</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="pengguna" type="checkbox" name="master_pengguna">
                                <label for="pengguna">Pengguna</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="pelanggan" type="checkbox" name="master_pelanggan">
                                <label for="pelanggan">Pelanggan</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="barang" type="checkbox" name="master_barang">
                                <label for="barang">Barang</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="setharga" type="checkbox" name="master_setharga">
                                <label for="setharga">Set Harga</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <h5 class="mb-3">Transaksi</h5>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="pembelian" type="checkbox" name="pembelian">
                                <label for="pembelian">Pembelian</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="pengeluaran" type="checkbox" name="pengeluaran">
                                <label for="pengeluaran">Pengeluaran</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="penjualan" type="checkbox" name="penjualan">
                                <label for="penjualan">Penjualan</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="gabungkan" type="checkbox" name="gabungkan">
                                <label for="gabungkan">Gabungkan</label>
                            </div>
                        </div>
                    </div>


                    <div class="form-group col-md-4">
                        <h5 class="mb-3">Laporan</h5>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="stok" type="checkbox" name="laporan_stok">
                                <label for="stok">Stok</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="lap_pembelian" type="checkbox" name="laporan_pembelian">
                                <label for="lap_pembelian">Pembelian</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="lap_penjualan" type="checkbox" name="laporan_penjualan">
                                <label for="lap_penjualan">Penjualan</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input id="pendapatan" type="checkbox" name="laporan_pendapatan">
                                <label for="pendapatan">Pendapatan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end">
                        <a href="/lokasi" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
