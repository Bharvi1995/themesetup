$('#subuser-form').submit(function(){
    $(this).find('input:text').each(function(){
        $(this).val($.trim($(this).val()));
    });
});
