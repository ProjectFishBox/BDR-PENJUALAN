<div class="header">
    <div class="logo logo-dark" style="display: flex; justify-content: center; align-items: center; text-align: center">
        <a href="/dashboard">
            <img src="{{asset('assets/images/logo/logo.jpeg')}}" alt="" style="width: 40%">
        </a>
    </div>
    <div class="logo logo-white">
        <a href="/dashboard">
            <img src="{{asset('assets/images/logo/logo.jpeg')}}" alt="">
        </a>
    </div>
    <div class="nav-wrap">
        <ul class="nav-left">
            <li class="desktop-toggle">
                <a href="javascript:void(0);">
                    <i class="anticon anticon-bell notification-badge"></i>
                </a>
            </li>
            <li class="mobile-toggle">
                <a href="javascript:void(0);">
                    <i class="anticon"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" data-toggle="modal" data-target="#search-drawer">
                    <h5 style="margin-bottom: unset">{{ $title }}</h5>
                </a>
            </li>
        </ul>
        <ul class="nav-right">
            <h4 style="margin-bottom: unset">Administrator</h4>
            <li class="dropdown dropdown-animated scale-left">
                <div class="pointer" data-toggle="dropdown">
                    <div class="avatar avatar-image  m-h-10 m-r-15">
                        <img src="{{ asset('assets/images/avatars/thumb-1.jpg') }}" alt="Profile Image">
                    </div>
                </div>
                <div class="p-b-15 p-t-20 dropdown-menu pop-profile">
                    <a href="/profile" class="dropdown-item d-block p-h-15 p-v-10">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="m-l-10">Profile</span>
                            </div>
                            <img src="{{ asset('images/icons/angle-right-solid.svg')}}" alt="" class="icon-size">
                        </div>
                    </a>
                    <form action="{{route('logout')}}" method="POST">
                        @csrf
                        <button class="dropdown-item d-block p-h-15 p-v-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="anticon opacity-04 font-size-16 anticon-logout"></i>
                                    <span class="m-l-10">Logout</span>
                                </div>
                                <i class="anticon font-size-10 anticon-right"></i>
                            </div>
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>
