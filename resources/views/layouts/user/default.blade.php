<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | @yield('title')</title>
    @if(config('app.env') == 'production')
        <!-- <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('setup/images/favicon.ico') }}"> -->
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/select2.min.css') }}">

    @yield('customeStyle')
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script type="text/javascript">
        var DATE = "{{ date('d-m-Y') }}";
        var current_page_url = "<?php echo URL::current(); ?>";
        var current_page_fullurl = "<?php echo URL::full(); ?>";
        var CSRF_TOKEN = "{{ csrf_token() }}";
    </script>
</head>

<body oncontextmenu="return false"
    class="pace-done  vertical-layout vertical-menu-modern navbar-floating footer-static menu-expanded {{ Auth::user()->theme == 0 ? 'dark-layout' : 'light-layout' }} "
    data-open="click" data-menu="vertical-menu-modern" data-col="">
    <div id="loading">
        <p>Loading..</p>
    </div>
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

    @include('layouts.user.header')
    @include('layouts.user.sidebar')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper p-0">
            @yield('content')
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    @include('layouts.user.footer')

    <script src="{{ storage_asset('setup/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ storage_asset('setup/js/app-menu.js') }}"></script>
    <script src="{{ storage_asset('setup/js/lordicon.js') }}"></script>
    <script src="{{ storage_asset('setup/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ storage_asset('setup/js/app.js') }}"></script>
    <script src="{{ storage_asset('setup/js/chart.js') }}"></script>
    <script src="{{ storage_asset('setup/js/flatpickr.js') }}"></script>
    <script src="{{ storage_asset('setup/js/jquery.peity.min.js') }}"></script>
    <script src="{{ storage_asset('setup/vendors/js/extensions/toastr.min.js') }}"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> -->
    <script src="{{ storage_asset('setup/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('setup/js/form-select2.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/js/apexcharts.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.nav-item.has-sub.sidebar-group-active').removeClass('open');
            // Set the flatpcker class 
            $(".date-input input").addClass("flatpicker")
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

            $(document).on('select2:open', () => {
                $(this).closest('.select2-search__field').focus();
            });

            // on first focus (bubbles up to document), open the menu
            $(document).on('focus', '.select2-selection.select2-selection--single', function(e) {
                $(this).closest(".select2-container").siblings('select:enabled').select2('open');
            });

            // steal focus during close - only capture once and stop propogation
            $('select.select2').on('select2:closing', function(e) {
                $(e.target).data("select2").$selection.one('focus focusin', function(e) {
                    e.stopPropagation();
                });
            });
        });
    </script>

    <script src="{{ storage_asset('themeAdmin/custom_js/custom.js') }}"></script>
    <?php /* <script src="{{ storage_asset('themeAdmin/js/moment.min.js') }}"></script>*/ ?>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script>
        window.hostname = '{{ env('LARAVEL_ECHO_HOST') }}';
        window.laravel_echo_port = '{{ env('LARAVEL_ECHO_PORT') }}';
        window.user_id = {{ auth()->user()->id }};
        window.user_type = 'user';
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>

    @if (auth()->user()->is_rate_sent == 1)
        <script type="text/javascript">
            $(document).ready(function() {
                $("#is_rate").trigger("click");
            });
        </script>
    @endif
    <script> 
        document.addEventListener('contextmenu', event=> event.preventDefault()); 
        document.onkeydown = function(e) { 
            if(event.keyCode == 123) { 
                return false; 
            } 
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){ 
                return false; 
            } 
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){ 
                return false; 
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)){
                return false;
            }
            if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){ 
                return false; 
            } 
        } 
      </script> 
    @if (auth()->user()->is_rate_sent == 1)
        <!-- <button type="button" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg" id="is_rate"
            style="display: none;"></button>
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="min-width: 1040px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong> Fee Schedule</strong></h4>
                    </div>
                    <div class="modal-body">
                        <p><strong>Congratulations.!</strong></p>
                        <p>
                            Your account has been 'Approved' with the below mentioned rates. <br>Click 'Accept' to
                            proceed.
                        </p>
                        @include('partials.user.user_fee')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success rateAgree btn-sm" data-id="2">Accept</button>
                        <button type="button" class="btn btn-danger rateAgree btn-sm" data-id="3">Decline</button>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg1" id="is_rate_reason"
            style="display: none;"></button>
        <div class="modal fade bd-example-modal-lg1" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Decline Reason</h5>
                    </div>
                    <div class="modal-body">
                        <textarea id="reclineReason" class="form-control" name="reclineReason"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger rateAgreeReason btn-sm">Decline</button>
                        <button type="button" class="btn btn-warning rateAgreeReasonBack btn-sm">Back</button>
                    </div>
                </div>
            </div>
        </div> -->
    @endif

    @include('layouts.user.alert')
    @include('layouts.user.deleteModal')
    @yield('customScript')
</body>

</html>
