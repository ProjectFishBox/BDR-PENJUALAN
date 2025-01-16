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
                                    <div class="d-flex align-items-center justify-content-center m-b-20">
                                        <h5 class="m-b-0">Silahkan Login</h5>
                                    </div>

                                    @if (session()->has('loginError'))
                                        <div class="alert alert-danger">
                                            <div class="d-flex align-items-center justify-content-start">
                                                <span>{{ session('loginError') }}</span>
                                            </div>
                                        </div>
                                    @endif


                                    <form action="{{ route('login_action') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="username">Username:</label>
                                            <div class="input-affix">
                                                <input type="text" class="form-control" id="username" name="username"
                                                    placeholder="username">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-semibold" for="password">Password:</label>
                                            <div class="input-affix m-b-10">
                                                <input type="password" class="form-control" id="password" name="password"
                                                    placeholder="Password">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="d-flex flex-column">
                                                <button class="btn btn-primary w-100" type="submit">Login</button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="d-flex flex-column">
                                                <span class="font-size-13 text-muted">
                                                    Tidak Punya Akun?
                                                    <a class="small" href="/register"> Signup</a>
                                                </span>
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
