<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | White Label Agent - @yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    @yield('customeStyle')
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/custom.css') }}">

    <script type="text/javascript">
        var current_page_url = "<?php echo URL::current(); ?>";
        var current_page_fullurl = "<?php echo URL::full(); ?>";
        var CSRF_TOKEN = '{{ csrf_token() }}';
    </script>
</head>

<body
    class="vertical-layout vertical-menu-modern navbar-floating footer-static menu-collapsed {{ Auth::guard('agentUserWL')->user()->theme == 0 ? 'dark-layout' : 'light-layout' }}"
    data-open="click" data-menu="vertical-menu-modern" data-col="">
    <div id="loading">
        <p>Loading..</p>
    </div>
    @php
        $currentPageURL = URL::current();
        $pageArray = explode('/', $currentPageURL);
        $pageActive = isset($pageArray[5]) ? $pageArray[5] : 'merchant-management';
    @endphp
    @include('layouts.WLAgent.header')
    @include('layouts.WLAgent.sidebar')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper p-0">
            @yield('content')
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
    @include('layouts.WLAgent.footer')


    <script src="{{ storage_asset('NewTheme/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/app-menu.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/app.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script src="{{ storage_asset('NewTheme/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/form-select2.js') }}"></script>




    <!-- Flatpicker Js -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- <script src="{{ storage_asset('themeAdmin/assets/custom_js/custom.js') }}"></script> -->
    <?php /* <script src="{{ storage_asset('themeAdmin/js/moment.min.js') }}"></script> */ ?>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>



    <script type="text/javascript">
        var datetime = null,
            date = null;

        var update = function() {
            date = moment.utc();
            datetime.html(date.format('dddd, DD/MM/YYYY, HH:mm:ss a'));
        };

        $(document).ready(function() {
            $("#loading").hide();
            datetime = $('#datetime')
            update();
            setInterval(update, 500);

            // Set the flatpicker class
            $(".date-input input").addClass("flatpicker")
            $('.select2').select2();

            $('#searchModal .select2').select2({
                dropdownParent: $('#searchModal')
            });

            $(document).on("change", ".custom-file-input", function() {
                var file = $(this)[0].files[0].name;
                $(this).parent(".custom-file").find(".custom-file-label").html(file);
            });

            // Applied date picker
            $(".flatpicker").flatpickr({
                dateFormat: "d-m-Y",
            });
        });
    </script>

    @include('layouts.WLAgent.alert')
    @include('layouts.WLAgent.deleteModal')
    @yield('customScript')
</body>

</html>
