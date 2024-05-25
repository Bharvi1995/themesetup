$(document).ready(function() {

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('.custom-file-img').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#iframe_logo").change(function(){
        readURL(this);
    });

    $('#iframe-form').submit(function(event) {
        event.preventDefault();
        var data = new FormData();

        var data = new FormData($('#iframe-form')[0]);
        
        let apiUrl = $(this).attr('action');
        $.ajax({
            url:apiUrl,
            type:'POST',
            data: data,
            processData: false,
            contentType: false,
            context: this,
            beforeSend: function(){
                $('#detailsContent').html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
            },
            success:function(data) {

                if (data.status == true) {
                    $('#iframe-code').html(data.iframe_tag);
                    $('#direct-link').html(data.iframe_code);
                    toastr.success("Payment link generated successfully.", "Success", {
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
                } else {
                    toastr.error(data.message, "Error", {
                        positionClass: "toast-top-right",
                        timeOut: 5e3,
                        closeButton: !0,
                        debug: !1,
                        newestOnTop: !0,
                        progressBar: !0,
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
                }
            },
        });


    });
});
