@extends('components.auth.layout')

@section('content')
    <div class="app">
        <div class="container-fluid" style="background-image: url('assets/images/others/login-background.jpeg')">
            <div class="d-flex full-height p-v-20 flex-column justify-content-between">
                <div class="container">
                    <div class="row align-items-center justify-content-center">
                        <div style="text-align: center; font-family: Arial, sans-serif; background-color: none; padding: 20px;">
                            <h1 style="margin: 0; font-size: 2.5rem; color: white">Selamat Datang!</h1>
                            <p style="margin: 5px 0; font-size: 1.2rem; color: white">Sistem Informasi Penjualan Al-Badar</p>
                        </div>
                        <div class="col-md-12">
                            <div class="card" style="border-radius: 40px">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <img class="img-fluid mr-3" src="{{ asset('assets/images/logo/logo.png')}}" alt="Logo" style="width: 40%">
                                        <div class="w-100">
                                            <div class="d-flex align-items-center justify-content-center m-b-20">
                                                <h4 class="m-b-0"  style="font-size: 30px">Login</h4>
                                            </div>

                                            @if (session()->has('loginError'))
                                                <div class="alert alert-danger">
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="alert-icon">
                                                            <i class="anticon anticon-close-o"></i>
                                                        </span>
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
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
