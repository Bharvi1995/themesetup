// var datetime = null,
// date = null;

// var update = function() {
// date = moment.utc();
// datetime.html(date.format('dddd, DD/MM/YYYY, HH:mm:ss a'));
// };


$(document).ready(function () {
    $("#loading").hide()
    // datetime = $('#datetime')

    $('.select2').select2();

    $('#searchModal .select2').select2({
        dropdownParent: $('#searchModal')
    });

    // Set the flatpcker class 
    // $(".date-input input").addClass("flatpicker")

    $(".date-input input").flatpickr({
        dateFormat: "d-m-Y",
    });
 
});

$("body").on("click", ".rateAgree", function () {
    var id = $(this).data("id");
    var message = "NULL";
    if (id == "3") {
        $("#is_rate_reason").trigger("click");
        $(".bd-example-modal-lg").modal("hide");
    } else {
        $.ajax({
            url: "mid-rate-agree",
            type: "POST",
            context: $(this),
            data: { id: id, message: message, _token: CSRF_TOKEN },
            beforeSend: function () {
                $(this).attr("disabled", "disabled");
                $(this).html(
                    '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
                );
            },
            success: function (data) {
                if (data.success == "1") {
                    toastr.success("Submited successfully!");
                    setInterval(function () {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.error("Something went wrong.");
                }
            },
        });
    }
});

$("body").on("click", ".rateAgreeReason", function () {
    var id = "3";
    var message = $("textarea#reclineReason").val();

    if (message == "") {
        toastr.error("Please Enter Decline Reason");
    } else {
        $.ajax({
            url: "mid-rate-agree",
            type: "POST",
            data: { id: id, message: message, _token: CSRF_TOKEN },
            beforeSend: function () {
                $(this).attr("disabled", "disabled");
                $(this).html(
                    '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
                );
            },
            success: function (data) {
                if (data.success == "1") {
                    toastr.success("Submited successfully!");
                    setInterval(function () {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.error("Something went wrong.");
                }
            },
        });
    }
});

$("body").on("click", ".rateAgreeReasonBack", function () {
    $("#is_rate").trigger("click");
    $(".bd-example-modal-lg1").modal("hide");
});

// $(".select2").select2({});
// $(".modal .select2").select2({
//     dropdownParent: $(".modal"),
// });

jQuery("#header").prepend(
    '<div id="menu-icon"><span class="first"></span><span class="second"></span><span class="third"></span></div>'
);

jQuery("#menu-icon").on("click", function () {
    jQuery("nav").slideToggle();
    jQuery(this).toggleClass("active");
});

// Datepicker js start

// $(document).ready(function () {
   

//     // jQuery(".datepicker").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // $("#start_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#end_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#start_date_s").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#end_date_s").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#refund_start_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#refund_end_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#chargebacks_start_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#chargebacks_end_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#retrieval_start_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#retrieval_end_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#flagged_start_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#flagged_end_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#transaction_start_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#transaction_end_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#issue_date").datepicker({
//     //     todayHighlight: true,
//     // });
//     // jQuery(".payoutDateFields").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd/mm/yyyy",
//     // });
//     // jQuery("#prearbitration_start_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
//     // jQuery("#prearbitration_end_date").datepicker({
//     //     todayHighlight: true,
//     //     endDate: end,
//     //     format: "dd-mm-yyyy",
//     // });
// });

// Datepicker js end

// Back to top js start

$(document).ready(function () {
    /******************************
      BOTTOM SCROLL TOP BUTTON
   ******************************/

    // declare variable
    var scrollTop = $(".scrollTop");

    $(window).scroll(function () {
        // declare variable
        var topPos = $(this).scrollTop();

        // if user scrolls down - show scroll to top button
        if (topPos > 100) {
            $(scrollTop).css("opacity", "1");
        } else {
            $(scrollTop).css("opacity", "0");
        }
    }); // scroll END

    //Click event to scroll to top
    $(scrollTop).click(function () {
        $("html, body").animate(
            {
                scrollTop: 0,
            },
            800
        );
        return false;
    }); // click() scroll top EMD

    /*************************************
    LEFT MENU SMOOTH SCROLL ANIMATION
   *************************************/
    // declare variable
    var h1 = $("#h1").position();
    var h2 = $("#h2").position();
    var h3 = $("#h3").position();

    $(".link1").click(function () {
        $("html, body").animate(
            {
                scrollTop: h1.top,
            },
            500
        );
        return false;
    }); // left menu link2 click() scroll END

    $(".link2").click(function () {
        $("html, body").animate(
            {
                scrollTop: h2.top,
            },
            500
        );
        return false;
    }); // left menu link2 click() scroll END

    $(".link3").click(function () {
        $("html, body").animate(
            {
                scrollTop: h3.top,
            },
            500
        );
        return false;
    }); // left menu link3 click() scroll END
}); // ready() END

// Back to top js end

// date range picker js start

jQuery(document).ready(function () {
    jQuery(function () {
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            jQuery("#rangepicker span").html(
                start.format("MMMM D, YYYY") +
                    " - " +
                    end.format("MMMM D, YYYY")
            );
        }

        cb(start, end);
    });
});

$("#checkAll").on("change", function () {
    $("td input:checkbox, .custom-checkbox input:checkbox").prop(
        "checked",
        $(this).prop("checked")
    );
});

// * Bootstrap Loader
function appendLoader() {
    var html = ``;
    html += ` <div class="d-flex justify-content-center align-items-center">`;
    html += `<div class="spinner-grow text-secondary" role="status">`;
    html += `<span class="visually-hidden">Loading...</span>`;
    html += ` </div> </div>`;

    return html;
}
