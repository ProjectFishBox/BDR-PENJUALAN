<div class="header">
    <div class="logo logo-dark" style="display: flex; justify-content: center; align-items: center; text-align: center">
        <a href="/dashboard">
            <h3>Logo</h3>
        </a>
    </div>
    <div class="logo logo-white">
        <a href="/dashboard">
            <h3>Logo</h3>
        </a>
    </div>
    <div class="nav-wrap">
        <ul class="nav-left">
            <li class="desktop-toggle">
                <a href="javascript:void(0);">
                    <img src="{{ asset('images/icons/outdent-solid.svg')}}" alt="" class="icon-nav">
                </a>
            </li>
            <li class="mobile-toggle" hidden>
                <a href="javascript:void(0);">
                    <i class="anticon"></i>
                </a>
            </li>
            <li hidden>
                <a href="javascript:void(0);" data-toggle="modal" data-target="#search-drawer">
                    <i class="anticon anticon-search"></i>
                </a>
            </li>
        </ul>
        <ul class="nav-right">
            <h4 style="margin-bottom: unset">Administrator</h4>
            <li class="dropdown dropdown-animated scale-left">
                <div class="pointer" data-toggle="dropdown">
                    <div class="avatar avatar-image  m-h-10 m-r-15">
                        <img src="{{ asset('images/avatars/thumb-1.jpg') }}" alt="Profile Image">
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
                    <a href="javascript:void(0);" class="dropdown-item d-block p-h-15 p-v-10">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="m-l-10">Logout</span>
                            </div>
                            <img src="{{ asset('images/icons/angle-right-solid.svg')}}" alt="" class="icon-size">
                        </div>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
