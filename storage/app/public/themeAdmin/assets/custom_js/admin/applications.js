$(document).ready(function() {
    $(".single-select").select2();
});

$("body").on("click", ".delete", function(e){
    e.preventDefault();
    var th = $(this);
    if(confirm("Are you sure want to remove?")){
        th.parent("form").submit();
    }
});

$('body').on('change', '#selectallcheckbox', function() {
    if($(this).prop("checked") == true){
        $('.multidelete').prop("checked", true);
    }
    else if($(this).prop("checked") == false){
        $('.multidelete').prop("checked", false);
    }
});

// Send in not-interested
$('body').on('click', '#NotInterested', function() {
    var id = [];
    $('.multidelete:checked').each(function(){
        id.push($(this).val());
    });
    let apiUrl = $(this).data('link');
    if(id.length > 0) {
        $.ajax({
            url: apiUrl,
            method:"POST",
            context: $(this),
            data:{
                '_token': CSRF_TOKEN,
                'ids': id
            },
            // processData: false,
            // contentType: false,
            beforeSend: function() {
                $(this).attr('disabled', 'disabled');
                $(this).html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
            },
            success:function(data) {
                if(data.success) {
                    // console.log(data);
                    toastr.success('Selected applications move in not interested list.');
                    $(this).attr('disabled', false);
                    $(this).html('Not Interested');
                    window.setTimeout(
                        function(){
                            location.reload(true)
                        },
                        2000
                    );
                } else {
                    toastr.error('Something went wrong.');
                    $(this).attr('disabled', false);
                    $(this).html('Not Interested');
                    window.setTimeout(
                        function(){
                            location.reload(true)
                        },
                        2000
                    );
                }
                $(this).attr('disabled', false);
                $(this).html('Not Interested');
            }
        });
    } else {
        toastr.error('Please select atleast one records!');
    }
});

//submit multiple mail
$('body').on('click', '#submitSendMail', function(){
    var id = [];
    $('.multidelete:checked').each(function(){
        id.push($(this).val());
    });
    let apiUrl = $(this).data('link');
    var formData = new FormData($('#SendMailForm')[0]);
    formData.append('id', id);
    if(id.length > 0)
    {
        $.ajax({
            url:apiUrl,
            method:"POST",
            context: $(this),
            data:formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $(this).attr('disabled', 'disabled');
                $(this).html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
            },
            success:function(data) {
                console.log(data);
                if(data.errors) {
                    if(data.errors.subject){
                        $( '#er_subject' ).html( data.errors.subject[0] );
                    }
                    if(data.errors.bodycontent){
                        $( '#er_bodycontent' ).html( data.errors.bodycontent[0] );
                    }
                }

                if(data.success) {
                    $('#Send_email').modal('hide');
                    $('#subject').val('');
                    $('#bodycontent').val('');
                    toastr.success('Mail Send Successfully!');
                }
                $(this).attr('disabled', false);
                $(this).html('Submit');
                // window.setTimeout(
                //     function(){
                //         location.reload(true)
                //     },
                //     2000
                // );
            }
        });
    }
    else
    {
        toastr.error('Please select atleast one user!');
        $('#Send_email').modal('hide');
    }
});

// ReAssign Application
$('body').on('click', '#reassign', function(){
    var id = $(this).data('id');
    $('#reassignForm').append('<input type="hidden" id="reassignID" name="id" value="'+id+'">');
});
$('body').on('click', '#closeReassignForm', function(){
    $('#reassignForm').find( "input[id='reassignID']" ).remove();
    $( '#reassign_reason_error' ).html( "" );
    $( '#reassign_reason' ).val( "" );
});
$('body').on('click', '#submitReassignForm', function(){
    var reassignForm = $("#reassignForm");
    var formData = reassignForm.serialize()+ '&name=corporation_details';
    $( '#reassign_reason_error' ).html( "" );
    $( '#reassign_reason' ).val( "" );
    let apiUrl = $(this).data('link');
    $.ajax({
        url: apiUrl,
        type:'POST',
        data:formData,
        success:function(data) {
            console.log(data);
            if(data.errors) {
                if(data.errors.reassign_reason){
                    $( '#reassign_reason_error' ).html( data.errors.reassign_reason[0] );
                }
            }
            if(data.success == '1') {
                $('#reassignModel').modal('hide');
                toastr.success('Application Reassigned Successfully');
                $('#reassignForm').find( "input[id='reassignID']" ).remove();
                setInterval(function(){
                    location.reload();
                }, 2000);
            } else if (data.success == '0')   {
                toastr.error('Something went wrong, please try again!');
                $('#reassignForm').find( "input[id='reassignID']" ).remove();
            }
        },
    });
});


// ReAssign Agreement
$('body').on('click', '#reassignAgreement', function(){
    var id = $(this).data('id');
    $('#reassignAgreementForm').append('<input type="hidden" id="reassignID" name="id" value="'+id+'">');
});
$('body').on('click', '#closeReassignAgreementForm', function(){
    $('#reassignAgreementForm').find( "input[id='reassignID']" ).remove();
    $( '#reassign_agreement_reason_error' ).html( "" );
    $( '#reassign_agreement_reason' ).val( "" );
});
$('body').on('click', '#submitReassignAgreementForm', function(){
    var reassignAgreementForm = $("#reassignAgreementForm");
    var formData = reassignAgreementForm.serialize();
    $( '#reassign_agreement_reason_error' ).html( "" );
    $( '#reassign_agreement_reason' ).val( "" );
    let apiUrl = $(this).data('link');
    $.ajax({
        url: apiUrl,
        type:'POST',
        data:formData,
        success:function(data) {
            console.log(data);
            if(data.errors) {
                if(data.errors.reassign_agreement_reason){
                    $( '#reassign_agreement_reason_error' ).html( data.errors.reassign_agreement_reason[0] );
                }
            }
            if(data.success == '1') {
                $('#reassignAgreementModel').modal('hide');
                toastr.success('Agreement Reassigned Successfully');
                $('#reassignAgreementForm').find( "input[id='reassignID']" ).remove();
                setInterval(function(){
                    location.reload();
                }, 2000);
            } else if (data.success == '0')   {
                toastr.error('Something went wrong, please try again!');
                $('#reassignAgreementForm').find( "input[id='reassignID']" ).remove();
            }
        },
    });
});

// Reject Application
$('body').on('click', '#reject', function(){
    var id = $(this).data('id');
    $('#rejectForm').append('<input type="hidden" id="rejectID" name="id" value="'+id+'">');
});
$('body').on('click', '#closeRejectForm', function(){
    $('#rejectForm').find( "input[id='rejectID']" ).remove();
    $( '#reject_reason_error' ).html( "" );
    $( '#reject_reason' ).val( "" );
});
$('body').on('click', '#submitRejectForm', function(){
    var rejectForm = $("#rejectForm");
    var formData = rejectForm.serialize()+ '&name=corporation_details';
    $( '#reject_reason_error' ).html( "" );
    $( '#reject_reason' ).val( "" );
    let apiUrl = $(this).data('link');

    $.ajax({
        url: apiUrl,
        type:'POST',
        data:formData,
        success:function(data) {
            console.log(data);
            if(data.errors) {
                if(data.errors.reject_reason){
                    $( '#reject_reason_error' ).html( data.errors.reject_reason[0] );
                }
            }
            if(data.success == '1') {
                $('#rejectModel').modal('hide');
                toastr.success('Application Rejected Successfully');
                $('#rejectForm').find( "input[id='rejectID']" ).remove();
                setInterval(function(){
                    location.reload();
                }, 2000);
            } else if (data.success == '0')   {
                toastr.error('Something went wrong, please try again!');
                $('#rejectForm').find( "input[id='rejectID']" ).remove();
            }
        },
    });
});

$('body').on('change', '#is_agreement', function() {
    var id = $(this).attr('data-app');
    var dataRateAccept = $(this).attr('data-rate-accept');
    var user_id = $(this).attr('data-user-id');
    let apiUrl = $(this).data('link');

    if(dataRateAccept == '1'){
        swal("Warning!", "Acceptance of rates from merchants' end is awaited.", "warning");
        setInterval(function(){
            location.reload();
        }, 4000);
    }else{
        swal({
            title: 'Are you sure?',
            text: "You want to agreement sent?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0CC27E',
            cancelButtonColor: '#FF586B',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            confirmButtonClass: 'btn btn-success btn-raised mr-5',
            cancelButtonClass: 'btn btn-danger btn-raised',
            buttonsStyling: false
        }).then(function () {
            $.ajax({
                url:apiUrl,
                type:'POST',
                data:{'id':id, 'user_id':user_id, '_token' : CSRF_TOKEN },
                beforeSend: function(msg){
                    $("#lodder").css('display','block');
                },
                success:function(data) {
                    console.log(data);
                    $("#lodder").css('display','none');
                    if(data.success == '1') {
                        swal("Done!", "Sent Successfully!", "success");
                        setInterval(function(){
                            location.reload();
                        }, 2000);
                    } else {
                        swal("Error!", "Something went wrong, try again!", "error");
                    }
                },
            });
        }, function (dismiss) {
            if (dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Agreement not sent :)',
                    'error'
                )
            }
        })
    }

});

$('body').on('change', '#is_received', function() {
    var id = $(this).attr('data-app');
    let apiUrl = $(this).data('link');

    swal({
        title: 'Are you sure?',
        text: "You want to agreement received?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0CC27E',
        cancelButtonColor: '#FF586B',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        confirmButtonClass: 'btn btn-success btn-raised mr-5',
        cancelButtonClass: 'btn btn-danger btn-raised',
        buttonsStyling: false
    }).then(function () {
            $.ajax({
                url: apiUrl,
                type:'POST',
                data:{'id':id, '_token' : CSRF_TOKEN },
                beforeSend: function(msg){
                    $("#lodder").css('display','block');
                },
                success:function(data) {
                    console.log(data);
                    $("#lodder").css('display','none');
                    if(data.success == '1') {
                        swal("Done!", "Received Successfully!", "success");
                        setInterval(function(){
                            location.reload();
                        }, 2000);
                    } else {
                        swal("Error!", "Something went wrong, try again!", "error");
                    }
                },
            });
    }, function (dismiss) {
        if (dismiss === 'cancel') {
            swal(
                'Cancelled',
                'Agreement not received :)',
                'error'
            )
        }
    })
});

$('#sa-params').on('click', function () {
    var agent = $('#selectAgent').val();
    var agentC = document.getElementById('commission').value;
    var agentMC = document.getElementById('commission_master').value;
    let apiUrl = $(this).data('link');

    var merchant_discount_rate = document.getElementById('merchant_discount_rate').value;
    if(merchant_discount_rate == ''){
        $('#merchant_discount_rate_error').html('This field is required.');
    }else{
        $('#merchant_discount_rate_error').html('');
    }

    var merchant_discount_rate_master_card = document.getElementById('merchant_discount_rate_master_card').value;
    if(merchant_discount_rate_master_card == ''){
        $('#merchant_discount_rate_master_card_error').html('This field is required.');
    }else{
        $('#merchant_discount_rate_master_card_error').html('');
    }

    var rolling_reserve_paercentage = document.getElementById('rolling_reserve_paercentage').value;
    if(rolling_reserve_paercentage == ''){
        $('#rolling_reserve_paercentage_error').html('This field is required.');
    }else{
        $('#rolling_reserve_paercentage_error').html('');
    }

    var transaction_fee = document.getElementById('transaction_fee').value;
    if(transaction_fee == ''){
        $('#transaction_fee_error').html('This field is required.');
    }else{
        $('#transaction_fee_error').html('');
    }

    var setup_fee = document.getElementById('setup_fee').value;
    if(setup_fee == ''){
        $('#setup_fee_error').html('This field is required.');
    }else{
        $('#setup_fee_error').html('');
    }

    var setup_fee_master_card = document.getElementById('setup_fee_master_card').value;
    if(setup_fee_master_card == ''){
        $('#setup_fee_master_card_error').html('This field is required.');
    }else{
        $('#setup_fee_master_card_error').html('');
    }

    var refund_fee = document.getElementById('refund_fee').value;
    if(refund_fee == ''){
        $('#refund_fee_error').html('This field is required.');
    }else{
        $('#refund_fee_error').html('');
    }

    var chargeback_fee = document.getElementById('chargeback_fee').value;
    if(chargeback_fee == ''){
        $('#chargeback_fee_error').html('This field is required.');
    }else{
        $('#chargeback_fee_error').html('');
    }

    var flagged_fee = document.getElementById('flagged_fee').value;
    if(flagged_fee == ''){
        $('#flagged_fee_error').html('This field is required.');
    }else{
        $('#flagged_fee_error').html('');
    }

    var retrieval_fee = document.getElementById('retrieval_fee').value;
    if(retrieval_fee == ''){
        $('#retrieval_fee_error').html('This field is required.');
    }else{
        $('#retrieval_fee_error').html('');
    }    
    
    if(agent == ''){
        $('#agent_error').html('This field is required.');
    }else{
        if(agent != '0' && agentC == ''){
            $('#agent_error').html('');
            $('#commission_master_error').html('');
            $('#commission_error').html('This field is required.');
        }else if(agent != '0' && agentMC == ''){
            $('#agent_error').html('');
            $('#commission_error').html('');
            $('#commission_master_error').html('This field is required.');

        }else{
            if(merchant_discount_rate != '' && rolling_reserve_paercentage != ''
                && transaction_fee != '' && refund_fee != '' && retrieval_fee != ''
                && setup_fee_master_card != '' && merchant_discount_rate_master_card != ''
                && chargeback_fee != '' && flagged_fee != '' && setup_fee != ''
            ){
                if(agentC == ''){
                    agentC = '0.00';
                }
                if(agentMC == ''){
                    agentMC = '0.00';
                }
                if(agent == '0'){
                    agent = '0';
                }

                $('#agent_error').html('');
                $('#commission_error').html('');
                $('#commission_master_error').html('');

                var id = $(this).data('id');
                swal({
                    title: 'Are you sure?',
                    text: "This application will be approved.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0CC27E',
                    cancelButtonColor: '#FF586B',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    confirmButtonClass: 'btn btn-success btn-raised mr-5',
                    cancelButtonClass: 'btn btn-danger btn-raised',
                    buttonsStyling: false
                }).then(function () {
                    $.ajax({
                        url:apiUrl,
                        type:'POST',
                        data:{'id':id, '_token' : CSRF_TOKEN, 
                            'commission':agentC, 'commission_master':agentMC ,'agent':agent,
                            'merchant_discount_rate':merchant_discount_rate, 'rolling_reserve_paercentage':rolling_reserve_paercentage,
                            'transaction_fee':transaction_fee, 'refund_fee':refund_fee,
                            'chargeback_fee':chargeback_fee, 'flagged_fee':flagged_fee,
                            'retrieval_fee':retrieval_fee,'setup_fee':setup_fee,
                            'merchant_discount_rate_master_card':merchant_discount_rate_master_card,'setup_fee_master_card':setup_fee_master_card
                        },
                        success:function(data) {
                            console.log(data);
                            if(data.success == '1') {
                                swal("Done!", "Application approved successfully!", "success");
                                setInterval(function(){
                                    location.reload();
                                }, 2000);
                            } else {
                                swal("Error!", "Something went wrong, try again!", "error");
                            }
                        },
                    });
                }, function (dismiss) {
                    if (dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your Applications is safe :)',
                            'error'
                        )
                    }
                })
            }else{
                swal("Error!", "Something went wrong, try again!", "error");
            }
        }
    }
});


$('#applicationTerminate').on('click', function () {
    let apiUrl = $(this).data('link');
    var id = $(this).data('id');

    swal({
        title: 'Are you sure?',
        text: "You want to terminate this application?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0CC27E',
        cancelButtonColor: '#FF586B',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        confirmButtonClass: 'btn btn-success btn-raised mr-5',
        cancelButtonClass: 'btn btn-danger btn-raised',
        buttonsStyling: false
    }).then(function () {
        $.ajax({
            url:apiUrl,
            type:'POST',
            data:{'id':id, '_token' : CSRF_TOKEN
            },
            success:function(data) {
                console.log(data);
                if(data.success == '1') {
                    swal("Done!", "Application Terminated Successfully!", "success");
                    setInterval(function(){
                        location.reload();
                    }, 2000);
                } else {
                    swal("Error!", "Something went wrong, try again!", "error");
                }
            },
        });
    }, function (dismiss) {
        if (dismiss === 'cancel') {
            swal(
                'Cancelled',
                'Your Applications is safe :)',
                'error'
            )
        }
    })
});



// ReAssign Agreement
$('body').on('click', '.DeleteDocument', function(){
    let id      = $(this).data('id');
    let type    = $(this).data('type');
    let file    = $(this).data('file');
    $('#DeleteDocumentForm').append('<input type="hidden" id="Doc_ID" name="id" value="'+id+'">');
    $('#DeleteDocumentForm').append('<input type="hidden" id="Doc_Type" name="type" value="'+type+'">');
    $('#DeleteDocumentForm').append('<input type="hidden" id="Doc_File" name="file" value="'+file+'">');
});
$('body').on('click', '#closeDeleteDocForm', function(){
    $('#DeleteDocumentForm').find( "input[id='Doc_ID']" ).remove();
    $('#DeleteDocumentForm').find( "input[id='Doc_Type']" ).remove();
    $('#DeleteDocumentForm').find( "input[id='Doc_File']" ).remove();
    $( '#reassign_agreement_reason_error' ).html( "" );
    $( '#reassign_agreement_reason' ).val( "" );
});
$('body').on('click', '#submitDeleteDocForm', function(){
    var DeleteDocumentForm = $("#DeleteDocumentForm");
    var formData = DeleteDocumentForm.serialize();
    $( '#reassign_agreement_reason_error' ).html( "" );
    $( '#reassign_agreement_reason' ).val( "" );
    let apiUrl = $(this).data('link');
    $.ajax({
        url: apiUrl,
        type:'POST',
        data:formData,
        success:function(data) {
            if(data.success == '1') {
                $('#delete_doc_modal').modal('hide');
                toastr.success('Application Document Deleted Successfully');
                setInterval(function(){ location.reload(); }, 1000);
            } else if (data.success == '0')   {
                toastr.error('Something went wrong, please try again!');
            }
        },
    });
});