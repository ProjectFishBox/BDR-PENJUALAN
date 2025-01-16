@extends('components.auth.layout')

@section('content')
    <div class="app">
        <div class="container-fluid p-h-0 p-v-20 bg full-height d-flex"
            style="background-image: url('assets/images/others/login-3.png')">
            <div class="d-flex flex-column justify-content-between w-100">
                <div class="container d-flex h-100">
                    <div class="row align-items-center w-100">
                        <div class="col-md-7 col-lg-5 m-h-auto">
                            <div class="card shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center m-b-10">
                                        <img class="img-fluid" alt="Logo" src="{{ asset('images/logo/logo.png') }}">
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center m-b-20">
                                        <h4 class="m-b-0">Sistem Informasi Penjualan</h4>
                                    </div>
                                    <form action="{{ route('register_action') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="nama">Nama:</label>
                                            <div class="input-affix">
                                                <input type="text" class="form-control" id="nama" name="nama"
                                                    placeholder="Nama" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="userName">Username:</label>
                                            <div class="input-affix">
                                                <input type="text" class="form-control" id="userName" name="username"
                                                    placeholder="Username" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="password">Password:</label>
                                            <div class="input-affix m-b-10">
                                                <input type="password" class="form-control" id="password" name="password"
                                                    placeholder="Password" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="jabatan">Jabatan:</label>
                                            <div class="input-affix m-b-10">
                                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                                    placeholder="jabatan" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="lokasi">Lokasi:</label>
                                            <div class="m-b-10">
                                                <select class="select2" name="id_lokasi" id="lokasi" required>
                                                    <option value="1">Makassar</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="akses">Akses:</label>
                                            <div class="m-b-10">
                                                <select class="select2" name="id_akses" id="akses" required>
                                                    <option value="1">Admin</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="createby">Create By:</label>
                                            <div class="input-affix">
                                                <input type="text" class="form-control" id="createby" name="create_by"
                                                    placeholder="createby" value="1">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="last user">last user:</label>
                                            <div class="input-affix">
                                                <input type="text" class="form-control" id="lastuser" name="last_user"
                                                    placeholder="last user" value="1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="d-flex flex-column">
                                                <button class="btn btn-primary w-100" type="submit">Signup</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
