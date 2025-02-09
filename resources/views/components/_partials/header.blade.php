<div class="header">
    <div class="logo logo-dark" style="display: flex; justify-content: center; align-items: center; text-align: center">
        <a href="/dashboard">
            <img src="{{asset('assets/images/logo/logo.png')}}" alt="" style="width: 40%">
        </a>
    </div>
    <div class="logo logo-white">
        <a href="/dashboard">
            <img src="{{asset('assets/images/logo/logo.png')}}" alt="">
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
                <h5 style="margin-bottom: unset">{{ $title }}</h5>
            </li>
        </ul>
        <ul class="nav-right">
            <h4 style="margin-bottom: unset">{{auth()->user()->nama}}</h4>
            <li class="dropdown dropdown-animated scale-left" id="profile-dropdown">
                <div class="pointer" data-toggle="dropdown">
                    <div class="avatar avatar-image  m-h-10 m-r-15">
                        <img src="{{ asset('assets/images/avatars/thumb-1.jpg') }}" alt="Profile Image">
                    </div>
                </div>
                <div class="p-b-15 p-t-20 dropdown-menu pop-profile">
                    <a href="/profile" class="dropdown-item d-block p-h-15 p-v-10">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="anticon anticon-user opacity-04 font-size-16"></i>
                                <span class="m-l-10">Profile</span>
                            </div>
                            <img src="{{ asset('images/icons/angle-right-solid.svg')}}" alt="" class="icon-size">
                        </div>
                    </a>
                    <button class="dropdown-item d-block p-h-15 p-v-10 clearcache" id="clearcache">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="anticon opacity-04 font-size-16 anticon-sync"></i>
                                <span class="m-l-10">Clear Cache</span>
                            </div>
                        </div>
                    </button>
                    <form action="{{route('logout')}}" method="POST">
                        @csrf
                        <button class="dropdown-item d-block p-h-15 p-v-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="anticon opacity-04 font-size-16 anticon-logout"></i>
                                    <span class="m-l-10">Logout</span>
                                </div>
                            </div>
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.getElementById('profile-dropdown');
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
            dropdown.querySelector('.dropdown-menu').classList.toggle('show');
        });
    });
</script>

<script>
    document.getElementById('clearcache').addEventListener('click', function() {
        console.log('button cache is clicked');
        let url = "/clear-cache"
        $.ajax({
            url,
            type: "GET",
            success: function(data) {
                if(data.code == 200)
                {
                    Swal.fire({
                        title: 'Success',
                        text: data.success,
                        icon: "success",
                        timer: 2000
                    });
                }else if(data.code == 400)
                {
                    Swal.fire({
                        title: 'Failed',
                        icon: "error",
                        text: data.error,
                        showConfirmButton: true,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#DD6B55",
                    });
                }
            },
            error: function(error) {
                console.error(error);
                $('.btn-barang-edit').prop('disabled', false);
                $('.btn-barang-edit').html(' <i class="anticon anticon-edit"></i>');
            }
        })
    })
</script>

@endpush
