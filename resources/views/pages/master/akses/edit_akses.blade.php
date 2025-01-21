@extends('components._partials.layout')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="mb-3">{{ $title }}</h4>

        <form action="{{ route('update-akses', $akses->id) }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nama">Nama Akses <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="nama" placeholder="Nama Akses" name="nama"
                        value="{{ old('nama', $akses->nama) }}" required>
                </div>
            </div>

            <h5 class="mb-3">Akses Menu <span style="color: red">*</span></h5>

            @php
                $dashboardMenu = $menus->firstWhere('nama', 'Dashboard');
            @endphp
            <div>
                <div class="form-group">
                    <div class="checkbox">
                        <input id="menu_dashboard" type="checkbox" name="menus[{{ $dashboardMenu->id ?? '' }}]"
                            {{ $dashboardMenu && in_array($dashboardMenu->id, $akses->menus->pluck('id')->toArray()) ? 'checked' : '' }}>
                        <label for="menu_dashboard">{{ $dashboardMenu->nama ?? 'Dashboard' }}</label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <h5 class="mb-3">Master</h5>
                    @foreach ($menus as $menu)
                        @if ($menu->parent == 1)
                            <div class="form-group">
                                <div class="checkbox">
                                    <input id="menu_{{ $menu->id }}" type="checkbox" name="menus[{{ $menu->id }}]"
                                        {{ in_array($menu->id, $akses->menus->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    <label for="menu_{{ $menu->id }}">{{ $menu->nama }}</label>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="form-group col-md-4">
                    <h5 class="mb-3">Transaksi</h5>
                    @foreach ($menus as $menu)
                        @if ($menu->parent == null && $menu->nama != 'Dashboard')
                            <div class="form-group">
                                <div class="checkbox">
                                    <input id="menu_{{ $menu->id }}" type="checkbox" name="menus[{{ $menu->id }}]"
                                        {{ in_array($menu->id, $akses->menus->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    <label for="menu_{{ $menu->id }}">{{ $menu->nama }}</label>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="form-group col-md-4">
                    <h5 class="mb-3">Transaksi</h5>
                    @foreach ($menus as $menu)
                        @if ($menu->parent == 2)
                            <div class="form-group">
                                <div class="checkbox">
                                    <input id="menu_{{ $menu->id }}" type="checkbox" name="menus[{{ $menu->id }}]"
                                        {{ in_array($menu->id, $akses->menus->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    <label for="menu_{{ $menu->id }}">{{ $menu->nama }}</label>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-end">
                    <a href="/akses" class="btn btn-danger mr-3">Batal</a>
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
