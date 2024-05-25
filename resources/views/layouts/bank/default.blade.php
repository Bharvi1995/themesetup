<!doctype html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | Bank @yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/select2.min.css') }}">
    
    @yield('customeStyle')
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">        
</head>

<body class="vertical-layout vertical-menu-modern navbar-floating footer-static menu-collapsed {{ Auth::guard('bankUser')->user()->theme == 0 ? 'dark-layout' : 'light-layout' }}" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <?php
        $currentPageURL = URL::current();
        $pageArray = explode('/', $currentPageURL);
        $pageActive = isset($pageArray[4]) ? $pageArray[4] : 'dashboard';    
        $pageActive1 = \Request::route()->getName();
    ?>

    @include('layouts.bank.header')
    @include('layouts.bank.sidebar')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper p-0">
            @yield('content')
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    @include('layouts.bank.footer')
    
    <script src="{{ storage_asset('NewTheme/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/app-menu.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/app.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>    
    <script src="{{ storage_asset('NewTheme/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/form-select2.js') }}"></script>    
    <?php /*<script src="{{ storage_asset('themeAdmin/js/moment.min.js') }}"></script> */ ?>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
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

    <script type="text/javascript">
        var current_page_url = "<?php echo URL::current(); ?>";
        var current_page_fullurl = "<?php echo URL::full(); ?>";
        var CSRF_TOKEN = '{{ csrf_token() }}';
    </script>

    <script>
        $(document).ready(function () {
            // For A Delete Record Popup
            $('body').on('click', '.delete_modal', function () {
                var id = $(this).attr('data-id');
                var url = $(this).attr('data-url');
                var token = CSRF_TOKEN;
                $(".remove-record-model").attr("action", url);
                $('body').find('.remove-record-model').append('<input name="_token" type="hidden" value="' + token + '">');
                $('body').find('.remove-record-model').append('<input name="_method" type="hidden" value="DELETE">');
                $('body').find('.remove-record-model').append('<input name="id" type="hidden" value="' + id + '">');
            });
            $('.remove-data-from-delete-form').click(function () {
                $('body').find('.remove-record-model').find("input").remove();
            });
            $('.modal').click(function () {
                // $('body').find('.remove-record-model').find( "input" ).remove();
            });
        });
    </script>

    <script type="text/javascript">
        $(".nav-control").on('click', function() {
            $(".logo-abbr").toggleClass("hide");
        });
        jQuery('.bell-link').on('click',function(){
            jQuery('.chatbox').addClass('active');
            jQuery('body').css('overflow','hidden');
            var height = (screen.height - 150);
            jQuery('.chatbox .tab-content').css('height',height+'px');
        });
        jQuery('.chatbox-close').on('click',function(){
            jQuery('.chatbox').removeClass('active');
            jQuery('body').css('overflow','auto');
        });

        $('.modal').on('hidden.bs.modal', function (e) {
                jQuery('.chatbox').removeClass('active');
                jQuery('body').css('overflow','auto');
            });

        $(document).ready(function() {
            $('.select2').select2();

            $('#searchModal .select2').select2({
              dropdownParent: $('#searchModal')
            });

            $(document).on("change", ".custom-file-input", function () {
                var file_count = $(this)[0].files.length;
                if(file_count == 1){
                    file = $(this)[0].files[0].name;
                    $(this).parent(".custom-file").find(".custom-file-label").html(file);
                }else{
                    $(this).parent(".custom-file").find(".custom-file-label").html(file_count+" files selected");
                }
            });
        });
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/custom.js') }}"></script>
    @include('layouts.bank.alert')
    @include('layouts.bank.deleteModal')
    @yield('customScript')
</body>

</html>