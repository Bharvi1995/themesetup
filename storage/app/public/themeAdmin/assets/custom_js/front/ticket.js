$('#inputGroupFile03').change(function() {
    var i = $(this).prev('label').clone();
    var file = $('#inputGroupFile03')[0].files[0].name;
    //console.log(file);
    $('.custom-file-label').text(file);
});

$('#ticket-form').submit(function(){
    $(this).find('input:text').each(function(){
        $(this).val($.trim($(this).val()));
    });
});
