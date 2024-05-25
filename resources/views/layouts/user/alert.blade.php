@if ($message = Session::get('success'))
<script type="text/javascript">	
    toastr.success('<?php echo $message; ?>', "Success", {
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        positionClass: "toast-top-right",
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    })
</script>
<?php Session::forget('success');?>
@endif

@if ($message = Session::get('successcustom'))
<script type="text/javascript"> 
    toastr.success('<?php echo $message; ?>', "Successful", {
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        positionClass: "toast-top-right",
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    })
</script>
<?php Session::forget('successcustom');?>
@endif

@if ($message = Session::get('error'))
<script type="text/javascript">
    toastr.error('<?php echo $message; ?>', "Error", {
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        positionClass: "toast-top-right",
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    })
</script>
<?php Session::forget('error');?>
@endif

@if ($message = Session::get('warning'))
<script type="text/javascript">
	toastr.warning('<?php echo $message; ?>', "Warning", {
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        positionClass: "toast-top-right",
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    })
</script>
<?php Session::forget('warning');?>
@endif

@if ($message = Session::get('info'))
<script type="text/javascript">
	toastr.info('<?php echo $message; ?>', "Info", {
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        positionClass: "toast-top-right",
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    })
</script>
<?php Session::forget('info');?>
@endif