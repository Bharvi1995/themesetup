$('body').on('click','.delete_modal',function() {
    var id = $(this).attr('data-id');
    var url = $(this).attr('data-url');
    var token = CSRF_TOKEN;
    $(".remove-record-model").attr("action",url);
    $('body').find('.remove-record-model').append('<input name="_token" type="hidden" value="'+ token +'">');
    $('body').find('.remove-record-model').append('<input name="_method" type="hidden" value="DELETE">');
    $('body').find('.remove-record-model').append('<input name="id" type="hidden" value="'+ id +'">');
});
$('.remove-data-from-delete-form').click(function() {
    $('body').find('.remove-record-model').find( "input" ).remove();
});
$('.modal').click(function() {
// $('body').find('.remove-record-model').find( "input" ).remove();
});
$('#search-form').submit(function(){
    $(this).find('input:text').each(function(){
        $(this).val($.trim($(this).val()));
    });
    $(this).find('#start_date').each(function(){
        var st_date = $(this).val();
        if(st_date != ''){
            var finalDate = st_date.split("-").reverse().join("-");
            var temp = " 00:00:00";
            var finalDateString = finalDate.concat(temp);
            $(this).val(finalDateString);
        }
    });
    $(this).find('#end_date').each(function(){
        var end_date = $(this).val();
        if(end_date != ''){
            var finalDate = end_date.split("-").reverse().join("-");
            var temp = " 23:59:59";
            var finalDateString = finalDate.concat(temp);
            $(this).val(finalDateString);
        }
    });
    //refund date filter
    $(this).find('#refund_start_date').each(function(){
        var refund_st_date = $(this).val();
        if(refund_st_date != ''){
            var reFinalDate = refund_st_date.split("-").reverse().join("-");
            var temp = " 00:00:00";
            var reFinalDateString = reFinalDate.concat(temp);
            $(this).val(reFinalDateString);
        }
    });
    $(this).find('#refund_end_date').each(function(){
        var refund_end_date = $(this).val();
        if(refund_end_date != ''){
            var refinalDate = refund_end_date.split("-").reverse().join("-");
            var temp = " 23:59:59";
            var reFinalDateString = refinalDate.concat(temp);
            $(this).val(reFinalDateString);
        }
    });
    //chargebacks date filter
    $(this).find('#chargebacks_start_date').each(function(){
        var chargebacks_st_date = $(this).val();
        if(chargebacks_st_date != ''){
            var chFinalDate = chargebacks_st_date.split("-").reverse().join("-");
            var temp = " 00:00:00";
            var chFinalDateString = chFinalDate.concat(temp);
            $(this).val(chFinalDateString);
        }
    });
    $(this).find('#chargebacks_end_date').each(function(){
        var chargebacks_end_date = $(this).val();
        if(chargebacks_end_date != ''){
            var chfinalDate = chargebacks_end_date.split("-").reverse().join("-");
            var temp = " 23:59:59";
            var chFinalDateString = chfinalDate.concat(temp);
            $(this).val(chFinalDateString);
        }
    });
    //retrieval date filter
    $(this).find('#retrieval_start_date').each(function(){
        var retrieval_st_date = $(this).val();
        if(retrieval_st_date != ''){
            var reFinalDate = retrieval_st_date.split("-").reverse().join("-");
            var temp = " 00:00:00";
            var reFinalDateString = reFinalDate.concat(temp);
            $(this).val(reFinalDateString);
        }
    });
    $(this).find('#retrieval_end_date').each(function(){
        var retrieval_end_date = $(this).val();
        if(retrieval_end_date != ''){
            var refinalDate = retrieval_end_date.split("-").reverse().join("-");
            var temp = " 23:59:59";
            var reFinalDateString = refinalDate.concat(temp);
            $(this).val(reFinalDateString);
        }
    });
    //flagged date filter
    $(this).find('#flagged_start_date').each(function(){
        var flagged_st_date = $(this).val();
        if(flagged_st_date != ''){
            var flagFinalDate = flagged_st_date.split("-").reverse().join("-");
            var temp = " 00:00:00";
            var flagFinalDateString = flagFinalDate.concat(temp);
            $(this).val(flagFinalDateString);
        }
    });
    $(this).find('#flagged_end_date').each(function(){
        var flagged_end_date = $(this).val();
        if(flagged_end_date != ''){
            var flagfinalDate = flagged_end_date.split("-").reverse().join("-");
            var temp = " 23:59:59";
            var flagFinalDateString = flagfinalDate.concat(temp);
            $(this).val(flagFinalDateString);
        }
    });
});

// Downlod application excel
$(document).on('click' , '#ExcelLink',function() {
    var ids = [];
    $('.multidelete:checked').map(function () {
        ids.push($(this).val());
    });
    var url = $(this).data('link');
    var fileName = $(this).data('filename') + DATE + '.xlsx';
    $.ajax({
        url: url,
        method: "post",
        xhrFields: {
            responseType: 'blob'
        },
        data: {
            _token: CSRF_TOKEN,
            ids: ids
        },
        success: function (data) {
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = fileName;
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        }
    });
});
