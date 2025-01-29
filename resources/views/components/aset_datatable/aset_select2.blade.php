@push('css')
    <link href="{{ asset('vendor/select2/css/select2.css') }}" rel="stylesheet">

    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 5px !important;
            padding: 6px 12px !important;
            display: flex !important;
            align-items: center !important;
        }


        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            font-size: 14px !important;
            color: #495057 !important;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endpush


@push('js')
    <script src="{{ asset('vendor/select2/js/select2.js') }}"></script>
@endpush
