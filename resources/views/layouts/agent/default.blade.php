<!doctype html>
<html lang="en">

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
        var current_page_url = "<?php echo URL::current(); ?>";
        var current_page_fullurl = "<?php echo URL::full(); ?>";
        var CSRF_TOKEN = '{{ csrf_token() }}';
    </script>
</head>

<body class="g-sidenav-show bg-gray-100">
    @php
        $currentPageURL = URL::current();
        $pageArray = explode('/', $currentPageURL);
        $pageActive = isset($pageArray[4]) ? $pageArray[4] : 'dashboard';
        $notifications = getNotifications(Auth::guard('agentUser')->user()->id, 'user', 5);
        $count_notifications = count($notifications);
    @endphp

    @include('layouts.agent.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @include('layouts.agent.header')
    
        <div class="container-fluid py-4">
            @yield('content')
            @include('layouts.agent.footer')
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
    <script src="{{ storage_asset('setup/vendors/js/forms/select/select2.full.min.js') }}"></script>

    <script type="text/javascript">
        $('.modal').on('hidden.bs.modal', function(e) {
            jQuery('.chatbox').removeClass('active');
            jQuery('body').css('overflow', 'auto');
        });

        $(document).ready(function() {
            $('.select2').select2();

            $('#searchModal .select2').select2({
                dropdownParent: $('#searchModal')
            });

            $(document).on("change", ".custom-file-input", function() {
                var file_count = $(this)[0].files.length;
                if (file_count == 1) {
                    file = $(this)[0].files[0].name;
                    $(this).parent(".custom-file").find(".custom-file-label").html(file);
                } else {
                    $(this).parent(".custom-file").find(".custom-file-label").html(file_count +
                        " files selected");
                }
            });
        });

        window.hostname = '{{ env('LARAVEL_ECHO_HOST') }}';
        window.laravel_echo_port = '{{ env('LARAVEL_ECHO_PORT') }}';
        window.user_id = {{ auth()->user()->id }};
        window.user_type = 'user';
    </script>

    @include('layouts.agent.alert')
    @include('layouts.agent.deleteModal')
    @yield('customScript')
</body>

</html>
