$('body').on('click', '.showTransaction', function(){
    $('body').addClass('right-bar-enabled');
    var id = $(this).data('id');
    $('#detailsContent').html('');
    var apiUrl = $(this).data('link');

    $.ajax({
        url: apiUrl,
        type:'POST',
        data:{ "_token": CSRF_TOKEN, 'id' : id},
        beforeSend: function(){
            $('#detailsContent').html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
        },
        success:function(data) {
            $('#detailsContent').html(data.html);
        },
    });
});

$('body').on('click', '.chargebacks-show-documents', function(){
    $('body').addClass('right-bar-enabled');
    var id = $(this).data('id');
    $('#detailsContent').html('');
    var apiUrl = $(this).data('link');

    $.ajax({
        url: apiUrl,
        type:'POST',
        data:{ "_token": CSRF_TOKEN, 'id' : id},
        beforeSend: function(){
            $('#detailsContent').html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
        },
        success:function(data) {
            $('#detailsContent').html(data.html);
        },
    });
});

$('body').on('click','.remove-record',function(){
    var url = $(this).data('url');
    $('#removeTransactionForm').attr('action', url);
});

$('body').on('click', '.remove-record', function(){
    var id = $(this).attr('data-id');
    var url = $(this).attr('data-url');
    if (confirm('Are you sure you want to delete this record?')) {
        $.ajax({
            type: 'GET',
            context: $(this),
            url: url,
            data: {
                '_token': CSRF_TOKEN,
                'id': id,
            },
            beforeSend: function() {
                $(this).attr('disabled', 'disabled');
            },
            success: function(data) {
                if(data.success == true) {
                    toastr.success('Document deleted Successfully !!')
                    location.reload();
                }
                else {
                    toastr.warning('Something went wrong !!');
                }
                $(this).attr('disabled', false);
            },
        });
    }
});

$('body').on('click', '.flagged-show-documents', function(){
    $('body').addClass('right-bar-enabled');
    var id = $(this).data('id');
    $('#detailsContent').html('');
    var apiUrl = $(this).data('link');

    $.ajax({
        url: apiUrl,
        type:'POST',
        data:{ "_token": CSRF_TOKEN, 'id' : id},
        beforeSend: function(){
            $('#detailsContent').html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
        },
        success:function(data) {
            $('#detailsContent').html(data.html);
        },
    });
});

$('body').on('click', '.refundTransaction', function(){
    var id = $(this).data('id');
    $('#refundTransactionForm').append('<input type="hidden" id="transactionsID" name="id" value="'+id+'">');
});

$('body').on('click', '#closeRefundForm', function(){
    $('#refundTransactionForm').find( "input[id='transactionsID']" ).remove();
    $( '#refund_reason_error' ).html( "" );
    $( '#refund_reason' ).val( "" );
});

$('body').on('click', '#submitRefundTtransactionForm', function(){
    var refundTransactionForm = $("#refundTransactionForm");
    var formData = refundTransactionForm.serialize();
    $( '#refund_reason_error' ).html( "" );
    var apiUrl = $(this).data('link');

    $.ajax({
        url: apiUrl,
        type:'POST',
        context: $(this),
        data:formData,
        beforeSend: function() {
            $(this).attr('disabled', 'disabled');
        },
        success:function(data) {
            console.log(data);
            if(data.errors) {
                if(data.errors.refund_reason){
                    $( '#refund_reason_error' ).html( data.errors.refund_reason[0] );
                }
            }
            if(data.success == '1') {
                $('#refundTransaction').modal('hide');
                toastr.success("Your transaction has been submitted for a refund. The refund will be processed within next 24 hours.");
                $('#refundTransactionForm').find( "input[id='transactionsID']" ).remove();
            } else if (data.success == '0')   {
                toastr.error("Your transaction is not refund, please try again !!");
                $('#refundTransactionForm').find( "input[id='transactionsID']" ).remove();
            }
            $( '#refund_reason' ).val( "" );
            setTimeout(function() {
                window.location.reload(1);
            }, 2000);
        },
    });
});

//Send email
$('.sendEmailTransaction').on('click',function(){
    var id = $(this).data('id');
    $('#sendEmailTransactionForm').append('<input type="hidden" id="transactionsID" name="id" value="'+id+'">');
});

$('#submitSendEmailForm').on('click', function(){
    $('#sendEmailTransactionForm').find( "input[id='transactionsID']" ).remove();
    $( '#email_address_error' ).html( "" );
    $( '#email_address' ).val( "" );
});

$('#submitSendEmailFormSend').on('click', function(){
    var sendEmailTransactionForm = $("#sendEmailTransactionForm");
    var formData = sendEmailTransactionForm.serialize();
    $( '#email_address_error' ).html( "" );
    var apiUrl = $(this).data('link');

    $.ajax({
        url: apiUrl,
        type:'POST',
        context: $(this),
        data:formData,
        success:function(data) {
            if(data.errors) {
                if(data.errors.email_address){
                    $( '#email_address_error' ).html( data.errors.email_address[0] );
                }
            }
            if(data.success == '1') {
                $('#sendEmailTransaction').modal('hide');
                toastr.success("Mail Sent successfully.");
                $('#sendEmailTransactionForm').find( "input[id='transactionsID']" ).remove();
            } else if (data.success == '0')   {
                toastr.error("Please try again !!");
                $('#sendEmailTransactionForm').find( "input[id='transactionsID']" ).remove();
            }
            $( '#email_address' ).val( "" );
            setTimeout(function() {
                window.location.reload(1);
            }, 2000);
        },
    });
});

$('body').on('click', '.retrieval-show-documents', function(){
    $('body').addClass('right-bar-enabled');
    var id = $(this).data('id');
    $('#detailsContent').html('');
    var apiUrl = $(this).data('link');

    $.ajax({
        url:apiUrl,
        type:'POST',
        data:{ "_token": CSRF_TOKEN, 'id' : id},
        beforeSend: function(){
            $('#detailsContent').html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
        },
        success:function(data) {
            $('#detailsContent').html(data.html);
        },
    });
});
