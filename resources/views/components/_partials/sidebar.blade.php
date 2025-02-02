@php
    $user = Auth::user();
    use App\Helpers\AccessHelper;
    $allowedMenuIds = \App\Models\Access_menu::where('id_akses', $user->id_akses)->pluck('id_menu')->toArray();
    $allowedMenus = \App\Models\Menu::whereIn('id', $allowedMenuIds)->get();

    // dd($allowedMenus);

    $simpleMenuItems = [
        '/dashboard' => 'Dashboard',
    ];

    $masterMenus = [
        '/lokasi' => 'Lokasi',
        '/akses' => 'Akses',
        '/pengguna' => 'Pengguna',
        '/pelanggan' => 'Pelanggan',
        '/barang' => 'Barang',
        '/setharga' => 'Set Harga',
    ];

    $additionalMenus = [
        '/pembelian' => 'Pembelian',
        '/pengeluaran' => 'Pengeluaran',
        '/penjualan' => 'Penjualan',
        '/gabungkan' => 'Gabungkan',
    ];

    $laporanMenus = [
        '/stok' => 'Stok',
        '/laporan-pembelian' => 'Pembelian',
        '/laporan-penjualan' => 'Penjualan',
        '/pendapatan' => 'Pendapatan',
    ];
@endphp

<div class="side-nav">
    <div class="side-nav-inner">
        <ul class="side-nav-menu scrollable">
            @foreach ($simpleMenuItems as $url => $title)
                @if ($allowedMenus->contains('url', $url))
                    <li class="nav-item dropdown">
                        <a href="{{ $url }}">
                            <span class="icon-holder">
                                <i class="anticon anticon-{{ strtolower($title) }}"></i>
                            </span>
                            <span class="title">{{ $title }}</span>
                        </a>
                    </li>
                @endif
            @endforeach

            @if (collect($masterMenus)->keys()->some(fn($url) => $allowedMenus->contains('url', $url)))
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                        <span class="icon-holder">
                            <i class="anticon anticon-folder-open"></i>
                        </span>
                        <span class="title">Master</span>
                        <span class="arrow">
                            <i class="arrow-icon"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach ($masterMenus as $url => $menu)
                            @if ($allowedMenus->contains('url', $url))
                                <li>
                                    <a href="{{ $url }}">{{ $menu }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endif

            @foreach ($additionalMenus as $url => $title)
                @if ($allowedMenus->contains('url', $url))
                    @php
                        $menuItem = $allowedMenus->firstWhere('url', $url);
                        $icon = is_object($menuItem) ? $menuItem->icon : '';
                    @endphp
                    <li class="nav-item dropdown">
                        <a href="{{ $url }}">
                            <span class="icon-holder">
                                <i class="{{ $icon }}"></i>
                            </span>
                            <span class="title">{{ $title }}</span>
                        </a>
                    </li>
                @endif
            @endforeach

            @if (collect($laporanMenus)->keys()->some(fn($url) => $allowedMenus->contains('url', $url)))
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="javascript:void(0);">
                        <span class="icon-holder">
                            <i class="anticon anticon-read"></i>
                        </span>
                        <span class="title">Laporan</span>
                        <span class="arrow">
                            <i class="arrow-icon"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach ($laporanMenus as $url => $menu)
                            @if ($allowedMenus->contains('url', $url))
                                <li>
                                    <a href="{{ $url }}">{{ $menu }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>



