$(".select2").select2({});
$("#accept_card").select2({
    placeholder: "Select",
    allowClear: true,
});
$("#processing_country").select2({
    placeholder: "Select",
    maximumSelectionLength: 5,
    allowClear: true,
});
$("#processing_currency").select2({
    placeholder: "Select",
    maximumSelectionLength: 5,
    allowClear: true,
});
$("#technology_partner_id").select2({
    placeholder: "Select",
    maximumSelectionLength: 3,
    allowClear: true,
});
$(".form-control").on("select2:open", function (e) {
    var y = $(window).scrollTop();
    $(window).scrollTop(y + 0.1);
});
$(document).on("change", ".custom-file-input", function () {
    var file = $(this)[0].files[0].name;
    $(this).parent(".custom-file").find(".custom-file-label").html(file);
});
// $('.extra-document').change(function() {
//     var file = $(this)[0].files[0].name;
//     $(this).parent('.custom-file').find('.custom-file-label').html(file);
// });

$(".form").submit(function (e) {
    var isFormvalid = jQuery(".form").valid();
    var customValid = null;
    $(".passportFile").each(function (index, item) {
        var value = $(item).val();
        if (value == "") {
            customValid = false;
            $(item)
                .parent(".custom-file")
                .find("span")
                .text("This field is required.");
        } else {
            customValid = true;
            $(item).parent(".custom-file").find("span").text("");
        }
    });
    $(".utilityFile").each(function (index, item) {
        var value = $(item).val();
        if (value == "") {
            customValid = false;
            $(item)
                .parent(".custom-file")
                .find("span")
                .text("This field is required.");
        } else {
            customValid = true;
            $(item).parent(".custom-file").find("span").text("");
        }
    });
    $(".bankStatementFile").each(function (index, item) {
        var value = $(item).val();
        if (value == "") {
            customValid = false;
            $(item)
                .parent(".custom-file")
                .find("span")
                .text("This field is required.");
        } else {
            customValid = true;
            $(item).parent(".custom-file").find("span").text("");
        }
    });

    if (isFormvalid == 0 || customValid == false) {
        return false;
    } else if (isFormvalid == 1 && customValid == true) {
        $(".btn-raised").prop("disabled", true);
        return true;
    }
});

jQuery(".form").validate({
    rules: {
        business_type: {
            required: !0,
        },
        accept_card: {
            required: !0,
        },
        business_name: {
            required: !0,
        },
        website_url: {
            required: !0,
        },
        business_contact_first_name: {
            required: !0,
        },
        business_contact_last_name: {
            required: !0,
        },
        business_address1: {
            required: !0,
        },
        residential_address: {
            required: !0,
        },
        monthly_volume: {
            required: !0,
        },
        country: {
            required: !0,
        },
        phone_no: {
            required: !0,
        },
        skype_id: {
            required: !0,
        },
        "processing_currency[]": {
            required: !0,
        },
        technology_partner_id: {
            required: !0,
        },
        "processing_country[]": {
            required: !0,
        },
        category_id: {
            required: !0,
        },
        company_license: {
            required: !0,
        },
        "passport[]": {
            required: true,
        },
        "latest_bank_account_statement[]": {
            required: !0,
        },
        "utility_bill[]": {
            required: !0,
        },
        company_incorporation_certificate: {
            required: !0,
        },

        domain_ownership: {
            required: !0,
        },

        other_processing_country: {
            required: function (element) {
                return !$(".otherProcessingInput").hasClass("d-none");
            },
        },
        other_industry_type: {
            required: function (element) {
                return !$(".showOtherIndustryInput").hasClass("d-none");
            },
        },
        licence_document: {
            required: function (element) {
                return !$(".toggleLicenceDocs").hasClass("d-none");
            },
        },
        board_of_directors: {
            required: function (element) {
                if (element.value <= 0) {
                    return true;
                }
            },
        },
    },
    messages: {
        business_type: " The business category field is required.",
        accept_card: " The accept card field is required.",
        business_name: "The company name field is required.",
        website_url: "The website url field is required.",
        business_contact_first_name: "The first name field is required.",
        business_contact_last_name: "The last name field is required.",
        business_address1: "The company address field is required.",
        residential_address: "The residential address field is required.",
        monthly_volume: "The monthly volume field is required.",
        country: "The country field is required.",
        phone_no: "The phone number field is required.",
        skype_id: "The contact details field is required.",
        "processing_currency[]": "The preferred currency field is required.",
        technology_partner_id:
            "The integration preference id field is required.",
        "processing_country[]": "The processing country field is required.",
        category_id: "The industry type id field is required.",
        company_license: "The license field is required.",
        company_incorporation_certificate:
            "The articles of incorporation field is required.",
        latest_bank_account_statement:
            "The bank account statement of company field is required.",
        utility_bill: "The utility bill field is required.",
        domain_ownership: "The domain ownership field is required.",
        other_processing_country: "Please enter your processing country",
        other_industry_type: "Please enter your industry type",
        licence_document: "Please upload your licence document",
        board_of_directors: "Number of board of directors field is required.",
    },

    debug: false,
    ignore: [],
    errorClass: "invalid-feedback animated fadeInUp",
    errorElement: "div",
    errorPlacement: function (e, a) {
        jQuery(a).parents(".form-group > div").append(e);
    },
    highlight: function (e) {
        jQuery(e)
            .closest(".form-group")
            .removeClass("is-invalid")
            .addClass("is-invalid");
    },
    success: function (e) {
        jQuery(e).closest(".form-group").removeClass("is-invalid"),
            jQuery(e).remove();
    },
});
