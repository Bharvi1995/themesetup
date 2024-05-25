$('body').on('change', '#is_agreement', function() {
    var rp_id = $(this).attr('data-rp-id');
    let apiUrl = $(this).data('link');
    $.ajax({
        url:apiUrl,
        type:'POST',
        data:{'rp_id':rp_id, '_token' : CSRF_TOKEN },
        success:function(data) {
            if(data.success == '1') {
                swal("Done!", "Agreement Sent Successfully!", "success");
                setInterval(function(){
                    location.reload();
                }, 2000);
            } else {
                swal("Error!", "Something went wrong, try again!", "error");
            }
        },
    });
});

$('body').on('change', '#is_received', function() {
    var rp_id = $(this).attr('data-rp-id');
    let apiUrl = $(this).data('link');

    $.ajax({
        url: apiUrl,
        type:'POST',
        data:{'rp_id':rp_id, '_token' : CSRF_TOKEN },
        success:function(data) {
            if(data.success == '1') {
                swal("Done!", "Agreement Received Successfully!", "success");
                setInterval(function(){
                    location.reload();
                }, 2000);
            } else {
                swal("Error!", "Something went wrong, try again!", "error");
            }
        },
    });
});