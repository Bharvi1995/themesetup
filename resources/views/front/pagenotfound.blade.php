<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7.5
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>Payment Demo | User Dashboard</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Preview page of Metronic Admin Theme #2 for 404 page option 2" name="description" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
        type="text/css" />
    <link href="{!! asset('adminTheme/plugins/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet"
        type="text/css" />
    <link href="{!! asset('adminTheme/plugins/simple-line-icons/simple-line-icons.min.css') !!}" rel="stylesheet"
        type="text/css" />
    <link href="{!! asset('adminTheme/plugins/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('adminTheme/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet') !!}"
        type="text/css" />
    <link href="{!! asset('adminTheme/plugins/bootstrap-toastr/toastr.min.css') !!}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{!! asset('adminTheme/css/components.min.css') !!}" rel="stylesheet" id="style_components"
        type="text/css" />
    <link href="{!! asset('adminTheme/css/plugins.min.css') !!}" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{!! asset('adminTheme/css/error.min.css') !!}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<!-- END HEAD -->

<body class=" page-500-full-page">
    <div class="row">
        <div class="col-md-12 page-500">
            <div class="number font-red"> 404 </div>
            <div class="details">
                <h3>Oops! You're lost.</h3>
                <p> We can not find the page you're looking for.
                    <br />
                </p>
                <p>
                    <a href="{{url('dashboardPage')}}" class="btn red btn-outline"> Return home </a>
                    <br>
                </p>
            </div>
        </div>
    </div>
    <!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<script src="../assets/global/plugins/ie8.fix.min.js"></script> 
<![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="{!! asset('themeAdmin/js/jquery-latest.min.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('adminTheme/plugins/bootstrap/js/bootstrap.min.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('adminTheme/plugins/js.cookie.min.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('adminTheme/plugins/jquery-slimscroll/jquery.slimscroll.min.js') !!}" type="text/javascript">
    </script>
    <script src="{!! asset('adminTheme/plugins/jquery.blockui.min.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('adminTheme/plugins/bootstrap-switch/js/bootstrap-switch.min.js') !!}"
        type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{!! asset('adminTheme/js/app.min.js') !!}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <!-- END THEME LAYOUT SCRIPTS -->
    <script>
        $(document).ready(function()
            {
                $('#clickmewow').click(function()
                {
                    $('#radio1003').attr('checked', 'checked');
                });
            })
    </script>
</body>

</html>