<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | @yield('title')</title>
    @if(config('app.env') == 'production')
        <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('softtheme/img/favicon.ico') }}">
    @endif

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- <link href="{{ storage_asset('softtheme/css/nucleo-icons.css')}}" rel="stylesheet" /> -->
    <link href="{{ storage_asset('softtheme/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link id="pagestyle" href="{{ storage_asset('softtheme/css/soft-ui-dashboard.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('softtheme/css/toastr.min.css') }}">
    <link rel=”stylesheet” href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    @yield('customeStyle')
    <style type="text/css">
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
    <script type="text/javascript">
        var DATE = "{{ date('d-m-Y') }}";
        var current_page_url = "<?php echo URL::current(); ?>";
        var current_page_fullurl = "<?php echo URL::full(); ?>";
        var CSRF_TOKEN = "{{ csrf_token() }}";
    </script>
</head>
<body class="g-sidenav-show bg-gray-100">
    <?php
    $currentPageURL = URL::current();
    $pageArray = explode('/', $currentPageURL);
    $pageActive = isset($pageArray[3]) ? $pageArray[3] : 'dashboardPage';
    if (\Auth::check()) {
        if (\Auth::user()->main_user_id != '0') {
            $userID = \Auth::user()->main_user_id;
        } else {
            $userID = \Auth::user()->id;
        }
        $notifications = getNotifications($userID, 'user', 5);
        $count_notifications = count($notifications);
    }
    ?>
    @include('layouts.user.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @include('layouts.user.header')
    
        <div class="container-fluid py-4">
            @yield('content')
            @include('layouts.user.footer')
        </div>
    </main>
    <!-- main content end -->
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
   <!--  <script src="{{ storage_asset('themesetup/assets/vendor/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{ storage_asset('themesetup/assets/vendor/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ storage_asset('themesetup/assets/vendor/js/jquery.overlayScrollbars.min.js')}}"></script>
    <script src="{{ storage_asset('themesetup/assets/vendor/js/apexcharts.js')}}"></script>
    <script src="{{ storage_asset('themesetup/assets/vendor/js/jquery.dataTables.min.js')}}"></script>
     -->
    <!-- <script src="{{ storage_asset('themesetup/assets/js/dashboard.js')}}"></script> -->
    <!-- <script src="{{ storage_asset('themesetup/assets/js/main.js')}}"></script> -->
    <!-- <script src="{{ storage_asset('themesetup/assets/js/bootstrap.min.js')}}"></script> -->
    <!-- <script src="{{ storage_asset('setup/js/form-select2.js') }}"></script> -->
    <!-- <script src="{{ storage_asset('themeAdmin/js/apexcharts.js') }}"></script> -->
    <!-- <script src="{{ storage_asset('setup/js/flatpickr.js') }}"></script> -->
    <!-- <script src="{{ storage_asset('setup/vendors/js/forms/select/select2.full.min.js') }}"></script> -->
    <!-- <script src="{{ storage_asset('themeAdmin/custom_js/custom.js') }}"></script> -->
    <!--  -->
    <script>
        window.hostname = '{{ env('LARAVEL_ECHO_HOST') }}';
        window.laravel_echo_port = '{{ env('LARAVEL_ECHO_PORT') }}';
        window.user_id = {{ auth()->user()->id }};
        window.user_type = 'user';
    </script>
    <script>
        var rtlReady = $('html').attr('dir', 'ltr');
        if (rtlReady !== undefined) {
            localStorage.setItem('layoutDirection', 'ltr');
        }

        $(function () { 
            $("#end_date").datepicker({  
                autoclose: true,  
                todayHighlight: true, 
            }).datepicker(); 

            $("#start_date").datepicker({  
                autoclose: true,  
                todayHighlight: true, 
            }).datepicker(); 
        }); 
    </script>
    @include('layouts.user.alert')
    @include('layouts.user.deleteModal')
    @yield('customScript')
    <!-- for demo purpose -->
</body>
