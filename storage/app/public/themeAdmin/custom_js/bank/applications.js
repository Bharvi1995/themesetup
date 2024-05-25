
    $('body').on('click', '.approved', function () {
        var id = $(this).data('id');
        var current_users_id = $(this).data('current_users_id');
        let apiUrl = $(this).data('link');
        swal({
            title: 'Are you sure?',
            text: "Do you want to approved this application ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0CC27E',
            cancelButtonColor: '#FF586B',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success mr-5',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false
        }).then(function () {
            
            $.ajax({
                url:apiUrl,
                type:'POST',
                data:{'applications_id':id, 'bank_users_id':current_users_id, '_token' : CSRF_TOKEN },
                success:function(data) {
                    console.log(data);
                    if(data.success == '1') {
                        toastr.success('Application Approved Successfully');
                        setInterval(function(){ 
                            location.reload();
                        }, 2000);
                    }
                },
            })
        },  function (dismiss) {
            if (dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your request cancelled. !!',
                    'error'
                )
            }
        })
    });

    $('body').on('click', '#declined', function(){
        var id = $(this).data('id');
        var current_users_id = $(this).data('current_users_id');
        $('#declinedForm').append('<input type="hidden" id="declinedID" name="applications_id" value="'+id+'">');
        $('#declinedForm').append('<input type="hidden" id="bankID" name="bank_users_id" value="'+current_users_id+'">');
    });
    $('body').on('click', '#closeDeclinedForm', function(){
        $('#declinedForm').find( "input[id='declinedID']" ).remove();
        $('#declinedForm').find( "input[id='bankID']" ).remove();
        $( '#declined_reason_error' ).html( "" );
        $( '#declined_reason' ).val( "" );
    });

    $('body').on('click', '#submitDeclinedForm', function(){
        var declinedForm = $("#declinedForm");
        var formData = declinedForm.serialize();
        $( '#declined_reason_error' ).html( "" );
        $( '#declined_reason' ).val( "" );
        let apiUrl = $(this).data('link');

        $.ajax({
            url: apiUrl,
            type:'POST',
            data:formData,
            beforeSend: function(){
                $(this).attr('disabled', true);
                $(this).html('<span id="wait-spin"><i class="fa fa-spinner fa-spin"></i>  Please Wait...</span>');
            },
            success:function(data) {
                console.log(data);
                if(data.errors) {
                    if(data.errors.declined_reason){
                        $( '#declined_reason_error' ).html( data.errors.declined_reason[0] );
                    }
                }
                if(data.success == '1') {
                    $('#declinedModel').modal('hide');
                    toastr.success('Application Declined Successfully');
                    $('#declinedForm').find( "input[id='declinedID']" ).remove();
                    $('#declinedForm').find( "input[id='bankID']" ).remove();
                    setInterval(function(){
                        location.reload();
                    }, 2000);
                } else if (data.success == '0')   {
                    toastr.error('Something went wrong, please try again!');
                    $('#declinedForm').find( "input[id='declinedID']" ).remove();
                    $('#declinedForm').find( "input[id='bankID']" ).remove();
                }
            },
        });
    });

    $('body').on('click', '#referred', function(){
        var id = $(this).data('id');
        var current_users_id = $(this).data('current_users_id');
        $('#referredForm').append('<input type="hidden" id="referredID" name="applications_id" value="'+id+'">');
        $('#referredForm').append('<input type="hidden" id="bankID" name="bank_users_id" value="'+current_users_id+'">');
    });
    $('body').on('click', '#closeReferredForm', function(){
        $('#referredForm').find( "input[id='referredID']" ).remove();
        $('#referredForm').find( "input[id='bankID']" ).remove();
        $( '#referred_note_error' ).html( "" );
        $( '#referred_note' ).val( "" );
    });

    $('body').on('click', '#submitReferredForm', function(){
        var referredForm = $("#referredForm");
        var formData = referredForm.serialize();
        $( '#referred_note_error' ).html( "" );
        $( '#referred_note' ).val( "" );
        let apiUrl = $(this).data('link');

        $.ajax({
            url: apiUrl,
            type:'POST',
            data:formData,
            beforeSend: function(){
                $(this).attr('disabled', true);
                $(this).html('<span id="wait-spin"><i class="fa fa-spinner fa-spin"></i>  Please Wait...</span>');
            },
            success:function(data) {
                console.log(data);
                if(data.errors) {
                    if(data.errors.referred_note){
                        $( '#referred_note_error' ).html( data.errors.referred_note[0] );
                    }
                }
                if(data.success == '1') {
                    $('#referredModel').modal('hide');
                    toastr.success('Application successfully marked as referred');
                    $('#referredForm').find( "input[id='referredID']" ).remove();
                    $('#referredForm').find( "input[id='bankID']" ).remove();
                    setInterval(function(){
                        location.reload();
                    }, 2000);
                } else if (data.success == '0')   {
                    toastr.error('Something went wrong, please try again!');
                    $('#referredForm').find( "input[id='referredID']" ).remove();
                    $('#referredForm').find( "input[id='bankID']" ).remove();
                }
            },
        });
    });


    $('body').on('click', '.referred_note_reply', function(){
        var id = $(this).data('id');
        var bank_id = $(this).data('bank_id');
        $('#referredReplyForm').append('<input type="hidden" id="referredID" name="applications_id" value="'+id+'">');
        $('#referredReplyForm').append('<input type="hidden" id="bankID" name="bank_users_id" value="'+bank_id+'">');
    });
    $('body').on('click', '#closeReferredReplyForm', function(){
        $('#referredReplyForm').find( "input[id='referredID']" ).remove();
        $('#referredReplyForm').find( "input[id='bankID']" ).remove();
        $( '#referred_note_reply_error' ).html( "" );
        $( '#referred_note_reply' ).val( "" );
    });

    $('body').on('click', '#submitReferredReplyForm', function(e){
        e.preventDefault();
        if(!$('#referred_note_reply').val()){
            swal(
                    'Error',
                    'Please provide Note. !!',
                    'error'
                )
            return false;
        }else{

            $(this).attr('disabled', true);
            $(this).html('<span id="wait-spin"><i class="fa fa-spinner fa-spin"></i>  Please Wait...</span>');
            $('#referredReplyForm').submit();
        }
    });