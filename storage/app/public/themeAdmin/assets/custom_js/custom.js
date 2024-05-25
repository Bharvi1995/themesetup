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

$(".select2").select2({});

jQuery("#header").prepend(
    '<div id="menu-icon"><span class="first"></span><span class="second"></span><span class="third"></span></div>'
);

jQuery("#menu-icon").on("click", function () {
    jQuery("nav").slideToggle();
    jQuery(this).toggleClass("active");
});

// Datepicker js start

jQuery(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    var end = new Date(date.getFullYear(), date.getMonth(), date.getDate());

    jQuery("#start_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#end_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#start_date_s").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#end_date_s").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#refund_start_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#refund_end_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#chargebacks_start_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#chargebacks_end_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#retrieval_start_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#retrieval_end_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#flagged_start_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#flagged_end_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#transaction_start_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#transaction_end_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#issue_date").datepicker({
        todayHighlight: true,
    });
    jQuery(".payoutDateFields").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd/mm/yyyy",
    });
    jQuery("#prearbitration_start_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
    jQuery("#prearbitration_end_date").datepicker({
        todayHighlight: true,
        endDate: end,
        format: "dd-mm-yyyy",
    });
});

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

// table js start
jQuery(document).ready(function () {
    // jQuery("#latest_transactions").DataTable({
    //     responsive: false,
    //      "paging": false,
    //      "searching": false,
    //      "bInfo": false,
    //      "ordering": false,
    //   });
    // jQuery("#latest_refund").DataTable({
    //      responsive: false,
    //     "paging": false,
    //     "searching": false,
    //     "bInfo": false,
    //     "ordering": false,
    //  });
    // jQuery("#latest_chargebacks").DataTable({
    //     responsive: false,
    //    "paging": false,
    //    "searching": false,
    //    "bInfo": false,
    //    "ordering": false,
    // });
    // jQuery("#latest_flagged").DataTable({
    //     responsive: false,
    //    "paging": false,
    //    "searching": false,
    //    "bInfo": false,
    //    "ordering": false,
    // });
    // jQuery("#user_role_list").DataTable({
    //   responsive: false,
    //  "paging": true,
    //  "searching": true,
    //  "bInfo": true,
    //  "ordering": false,
    // });
    // jQuery("#agents_list").DataTable({
    //   responsive: false,
    // "paging": true,
    // "searching": true,
    // "bInfo": true,
    // "ordering": false,
    // });
    // jQuery("#Mid_list").DataTable({
    //   responsive: false,
    // "paging": true,
    // "searching": true,
    // "bInfo": true,
    // "ordering": false,
    // });
    // jQuery("#add_Gateway").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": true,
    //   "bInfo": true,
    //   "ordering": false,
    //  });
    //  jQuery("#merchant_List").DataTable({
    //   responsive: false,
    //   "paging": false,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#payout_Report").DataTable({
    //   responsive: false,
    //   "paging": false,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#applications_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#is_completed_applications_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#approved_applications_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#rejected_applications_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#not_interested_applications_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#terminated_applications_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#agreement_send_applications_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#agreement_received_applications_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#all_transactions").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#mids_list").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#user_notifications").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
    //  jQuery("#payout_schedule").DataTable({
    //   responsive: false,
    //   "paging": true,
    //   "searching": false,
    //   "bInfo": false,
    //   "ordering": false,
    //  });
});

// table js end

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

        jQuery("#rangepicker").daterangepicker(
            {
                startDate: start,
                endDate: end,
                ranges: {
                    Today: [moment(), moment()],
                    Yesterday: [
                        moment().subtract(1, "days"),
                        moment().subtract(1, "days"),
                    ],
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "Last 30 Days": [moment().subtract(29, "days"), moment()],
                    "This Month": [
                        moment().startOf("month"),
                        moment().endOf("month"),
                    ],
                    "Last Month": [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month"),
                    ],
                },
            },
            cb
        );

        cb(start, end);
    });
});

$("#checkAll").on("change", function () {
    $("td input:checkbox, .custom-checkbox input:checkbox").prop(
        "checked",
        $(this).prop("checked")
    );
});

// date range picker js end

// pie chart js start
// $(document).ready(function() {
//   var randomScalingFactor = function() {
// 			return Math.round(Math.random() * 100);
// 		};

// 		var config = {
// 			type: 'doughnut',
// 			data: {
// 				datasets: [{
// 					data: [
// 						randomScalingFactor(),
// 						randomScalingFactor(),
// 						randomScalingFactor(),
// 						randomScalingFactor(),
// 					],
// 					backgroundColor: [
// 						window.chartColors.red,
// 						window.chartColors.orange,
// 						window.chartColors.yellow,
// 						window.chartColors.green,
// 						window.chartColors.green,
// 						window.chartColors.green,
// 					],
// 					label: 'Dataset 1'
// 				}],
// 				labels: [
// 					'APPROVED',
// 					'DECLINED',
// 					'CHARGEBACK',
// 					'REFUND',
// 					'FLAGGED',
// 					'PENDING',
// 				]
// 			},
// 			options: {
// 				responsive: true,
// 				legend: {
// 					position: 'left',
// 				},
// 				title: {
// 					display: false,
// 					text: 'Total Transactions'
// 				},
// 				animation: {
// 					animateScale: true,
// 					animateRotate: true
// 				}
// 			}
// 		};

// 		window.onload = function() {
// 			var ctx = document.getElementById('chart-area').getContext('2d');
// 			window.myDoughnut = new Chart(ctx, config);
// 		};

// });

// pie chart js end

// chart line js start

// var ctx = document.getElementById("myChart");
// var myChart = new Chart(ctx, {
//   type: "line",
//   responsive: true,
//   data: {
//     labels: [
//       ["Jan 25, 2021"],
//       [""],
//       [""],
//       [""],
//       [""],
//       [""],
//       [""],
//       [""],
//       ["Feb 29, 2021"]
//     ],
//     datasets: [
//       {
//         label: "Antwerpen",
//         data: ["791", "871", "809", "791", "940", "746", "822", "1001", "1006"],
//         backgroundColor: "#20BF6B",
//         borderColor: "#20BF6B",
//         borderWidth: 1,
//         fill: false,
//         lineTension: 0,
//         datalabels: {
//           align: "end",
//           anchor: "end",
//           display: "auto",
//           borderRadius: 0,
//           color: "black",
//           font: {
//             size: "12"
//           }
//         }
//       },

//       {
//         label: "Aarschot",
//         data: ["591", "471", "409", "491", "540", "446", "522", "801", "806"],
//         backgroundColor: "#FA8231",
//         borderColor: "#FA8231",
//         borderWidth: 1,
//         fill: false,
//         lineTension: 0,
//         datalabels: {
//           align: "end",
//           anchor: "end",
//           display: "auto",
//           borderRadius: 0,
//           color: "black",
//           font: {
//             size: "12"
//           }
//         }
//       },

//       {
//          label: "Aarschot",
//          data: ["900", "400", "600", "900", "550", "880", "760", "400", "802"],
//          backgroundColor: "#00A9E0",
//          borderColor: "#00A9E0",
//          borderWidth: 1,
//          fill: false,
//          lineTension: 0,
//          datalabels: {
//            align: "end",
//            anchor: "end",
//            display: "auto",
//            borderRadius: 0,
//            color: "black",
//            font: {
//              size: "12"
//            }
//          }
//        }
//     ]
//   },
//   options: {
//     title: { display: false, text: "Title" },
//     legend: { display: false },
//     scales: {
//       yAxes: [
//         {
//           ticks: {
//             maxTicksLimit: 8,
//             max: 1006 * 1.2,
//             beginAtZero: false
//           }
//         }
//       ]
//     },
//   }
// });

// chart line js end

// chart bar js start
// var ctx = document.getElementById("barChart").getContext("2d");
// var barChart = new Chart(ctx, {
//   type: "bar",
//   data: {
//     labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
//     datasets: [
//       {
//         label: "Success",
//         backgroundColor: "#546D85",
//         borderColor: "#546D85",
//         data: [15, 10, 21, 14, 12],
//         fill: false
//       },
//       {
//         label: "Failed",
//         fill: false,
//         backgroundColor: "#FDE428",
//         borderColor: "#FDE428",
//         data: [17, 15, 27, 14, 15]
//       },
//       {
//         label: "Chargebacks",
//         fill: false,
//         backgroundColor: "#000000",
//         borderColor: "#000000",
//         data: [12, 2, 8, 3, 5]
//       },
//       {
//          label: "Refund",
//          fill: false,
//          backgroundColor: "#32CD32",
//          borderColor: "#32CD32",
//          data: [9, 12, 2, 5, 14]
//        },
//        {
//          label: "Flagged",
//          fill: false,
//          backgroundColor: "#f30000",
//          borderColor: "#f30000",
//          data: [14, 2, 5, 10, 8]
//        }
//     ]
//   },
// });

// chart bar js end
