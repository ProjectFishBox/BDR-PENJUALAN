<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BDR - PENJUALAN</title>

    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/select2/select2.css') }}" rel="stylesheet">

    <style>
        .select2-container{
            display: inline
        }
        .select2-container-active.select2-container{
            border: none
        }
    </style>
</head>

<body>
    @yield('content')
    <script src="{{ asset('js/vendors.min.js') }}"></script>
    <script src="{{ asset('js/app.min.js') }}"></script>
    <script src="{{ asset('vendors/select2/select2.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('#lokasi').select2();
        });
        $(document).ready(function() {
            $('#akses').select2();
        });
    </script>
</body>

</html>
