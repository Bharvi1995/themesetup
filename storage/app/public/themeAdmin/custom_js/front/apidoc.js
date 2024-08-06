$("body").on("click", ".plus", function () {
    // i = $('#tab_logic tr').length;
    var i = $("#tab_logic tr:last").data("id");
    i = i + 1;
    $("#tab_logic").append(
        '<tr data-id="' +
            i +
            '">\
            <td>\
                <input placeholder="Enter here..." class="form-control" name="generate_apy_key[' +
            i +
            '][ip_address]" type="text">\
            </td>\
            <td class="text-center">\
                <button type="button" class="btn btn-primary btn-sm plus"> Plus </button>\
                <button type="button" class="btn btn-danger btn-sm minus"> Minus </button>\
            </td>\
        </tr>'
    );
    // i++;
});
$("body").on("click", ".minus", function () {
    $(this).closest("tr").remove();
    // i--;
});
