<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BDR - PENJUALAN</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <!-- page css -->

    <!-- Core css -->
    <link href="{{ asset('assets/css/app.min.css')}}" rel="stylesheet">

    <!-- Datatables css -->
    <style>
        .pagination .page-item {
            margin-top: 30px;
            margin-bottom: 20px;
        }
        #data-table_info{
            margin-top: 30px;
            margin-right: 10px;
            margin-bottom: 20px;
        }

        .data-table td, .data-table th {
            text-align: center;
            vertical-align: middle;
        }

        .data-table_wrapper {
            display: flex;
            justify-content: center;
        }

        #dt-length-0{
            padding-right: 30px
        }

    </style>

    @stack('css')

</head>

<body>
    @include('sweetalert::alert')
    <div class="app">
        <div class="layout">
            <!-- Header START -->
            @include('components._partials.header')
            <!-- Header END -->

            <!-- Side Nav START -->
            @include('components._partials.sidebar')
            <!-- Side Nav END -->

            <!-- Page Container START -->
            <div class="page-container">
                <!-- Content Wrapper START -->
                <div class="main-content">
                    @yield('content')
                    <!-- Content goes Here -->
                </div>
                <!-- Content Wrapper END -->

                <!-- Footer START -->
                @include('components._partials.footer')
                <!-- Footer END -->

            </div>
            <!-- Page Container END -->

            <!-- Search Start-->
            @include('components._partials.searchnav')
            <!-- Search End-->
        </div>
    </div>


    <script src="{{ asset('assets/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    @stack('js')

</body>

</html>
