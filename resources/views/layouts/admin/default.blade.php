<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | Admin @yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('softtheme/img/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link id="pagestyle" href="{{ storage_asset('softtheme/css/soft-ui-dashboard.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('softtheme/css/toastr.min.css') }}">
    <link rel=”stylesheet” href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/select2.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> -->
    @yield('customeStyle')

    <style type="text/css">
        .navigation li ul li.active a {
            margin-left: 0px;
        }

        .main-menu.menu-dark .navigation>li ul li>a {
            padding: 5px 0px 5px 15px;
        }

        .vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content>li>a i {
            margin-right: 10px;
        }

        .main-menu.menu-dark .navigation li a {
            text-align: left;
        }

        .main-menu.menu-dark .navigation>li>ul li:not(.has-sub) {
            margin: 0px 15px 0px 5px;
        }

        .main-menu.menu-dark .navigation>li ul li ul a {
            padding: 10px 15px 10px 30px;
        }

        #searchModal .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        #searchModal .modal-body::-webkit-scrollbar {
            width: 7px;
        }

        #searchModal .modal-body::-webkit-scrollbar-track {
            background: #494949;
        }

        #searchModal .modal-body::-webkit-scrollbar-thumb {
            background: #80A1C2;
        }

        .navbar-vertical.navbar-expand-xs .navbar-collapse {
             height: auto !important; 
        }

        .navbar-expand-lg .navbar-collapse {
            display: block !important;
        }

        .navbar-vertical .navbar-brand-img, .navbar-vertical .navbar-brand>img {
          max-height: 3rem !important;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    <?php
    $currentPageURL = URL::current();
    $pageArray = explode('/', $currentPageURL);
    $pageActive = isset($pageArray[4]) ? $pageArray[4] : 'dashboard';
    $notifications = getNotificationsForAdmin();
    $count_notifications = count($notifications);
    
    $pageActive1 = \Request::route()->getName();
    ?>


    @include('layouts.admin.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @include('layouts.admin.header')
    
        <div class="container-fluid py-4">
            @yield('content')
            @include('layouts.admin.footer')
        </div>
    </main>


    <script src="{{ storage_asset('themesetup/assets/vendor/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/core/popper.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/core/bootstrap.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/plugins/smooth-scrollbar.min.js?v=1.0.7')}}"></script>
    <script src="{{ storage_asset('softtheme/js/soft-ui-dashboard.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/fontawesome.min.js"></script>
    <script src="{{ storage_asset('softtheme/js/toastr.min.js') }}"></script>
    <script src="{{ storage_asset('themesetup/assets/vendor/js/apexcharts.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="{{ storage_asset('themesetup/assets/vendor/js/moment.min.js')}}"></script>
    <!-- <script src="{{ storage_asset('themesetup/assets/vendor/js/daterangepicker.js')}}"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

   <!--  <script src="{{ storage_asset('setup/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ storage_asset('setup/js/app-menu.js') }}"></script>
    <script src="{{ storage_asset('setup/js/app.js') }}"></script>
    <script src="{{ storage_asset('setup/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ storage_asset('setup/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('setup/js/form-select2.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/custom.js') }}"></script> -->
    <?php /* <script src="{{ storage_asset('themeAdmin/js/moment.min.js') }}"></script> */ ?>
    <!-- <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script> -->

    <script type="text/javascript">
        var DATE = "{{ date('d-m-Y') }}";
        var current_page_url = "<?php echo URL::current(); ?>";
        var current_page_fullurl = "<?php echo URL::full(); ?>";
        var CSRF_TOKEN = "{{ csrf_token() }}";
    </script>

    <script>
        window.hostname = '{{ env('LARAVEL_ECHO_HOST') }}';
        window.laravel_echo_port = '{{ env('LARAVEL_ECHO_PORT') }}';
        window.user_id = {{ auth()->guard('admin')->user()->id }};
        window.user_type = 'admin';
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>

    @include('layouts.admin.alert')
    @include('layouts.admin.deleteModal')
    @yield('customScript')
</body>

</html>
