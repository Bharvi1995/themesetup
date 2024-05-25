$('.custom-file-input').change(function() {
    var file = $(this)[0].files[0].name;
    $(this).parent('.custom-file').find('.custom-file-label').html(file);
});
