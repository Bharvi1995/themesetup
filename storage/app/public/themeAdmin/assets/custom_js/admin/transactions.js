jQuery(".datepicker-here").datepicker({});

$("body").on("click", ".showTransaction", function () {
    var id = $(this).data("id");
    var pathname = window.location.pathname;
    if(pathname == '/admin/transactions'){
        var tab = "all";
    }else{
        var tab = "";
    }
    const apiUrl = $(this).data("link");
    $("#detailsContent").html("");
    $.ajax({
        url: apiUrl,
        type: "POST",
        data: { _token: CSRF_TOKEN, id: id, tab: tab },
        beforeSend: function () {
            $("#detailsContent").html(
                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
            );
        },
        success: function (data) {
            $("#detailsContent").html(data.html);
        },
        error: function(xhr, status, error) {
            $("#detailsContent").html(error);
        },
    });
});

$("body").on("click", ".clRefund", function () {
    var id = $(this).data("id");
    $("#refundForm").append(
        '<input type="hidden" name="id" value="' + id + '">'
    );
    $("#refundForm").append('<input type="hidden" name="status" value="1">');
});

$("body").on("click", "#submitRefund", function (e) {
    e.preventDefault();
    $("#refund_error").html("");
    var formdata = $("#refundForm").serialize();
    const apiUrl = $(this).data("link");
    $.ajax({
        type: "POST",
        context: $(this),
        url: apiUrl,
        data: formdata,
        beforeSend: function () {
            $(this).attr("disabled", "disabled");
            $(this).html(
                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
            );
        },
        success: function (data) {
            if (data.errors) {
                if (data.errors.refund_date) {
                    $("#refund_error").html(data.errors.refund_date[0]);
                }
            }
            if (data.success == true) {
                toastr.success("Refund Updated Successfully!");
            } else {
                toastr.error("Something Went Wrong!");
            }

            $(this).attr("disabled", false);
            $(this).html("Submit");
            if (data.success == true || data.success == false) {
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
    });
});

$("body").on("click", ".clRefundChecked", function (e) {
    var id = $(this).data("id");
    if (confirm("Are you sure you want to remove the refund?")) {
        $.ajax({
            type: "POST",
            context: $(this),
            url: changeTransactionUnRefund,
            data: {
                _token: CSRF_TOKEN,
                id: id,
            },
            beforeSend: function () {
                $(this).attr("disabled", "disabled");
            },
            success: function (data) {
                if (data.success == true) {
                    toastr.success("Refund Removed Successfully!");
                    window.setTimeout(function () {
                        location.reload(true);
                    }, 2000);
                } else {
                    toastr.error("Something Went Wrong!");
                }
                $(this).attr("disabled", false);
            },
        });
    }
});

$("body").on("click", ".clChargeback", function () {
    var id = $(this).data("id");
    $("#chargebacksForm").append(
        '<input type="hidden" name="id" value="' + id + '">'
    );
    $("#chargebacksForm").append(
        '<input type="hidden" name="status" value="1">'
    );
});

$("body").on("click", "#submitChargebacks", function (e) {
    e.preventDefault();
    $("#chargebacks_error").html("");
    var formdata = $("#chargebacksForm").serialize();
    const apiUrl = $(this).data("link");
    $.ajax({
        type: "POST",
        context: $(this),
        url: apiUrl,
        data: formdata,
        beforeSend: function () {
            $(this).attr("disabled", "disabled");
            $(this).html(
                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
            );
        },
        success: function (data) {
            if (data.errors) {
                if (data.errors.changebanks_date) {
                    $("#chargebacks_error").html(
                        data.errors.changebanks_date[0]
                    );
                }
            }

            if (data.success == true) {
                toastr.success("Chargeback Updated Successfully!");
            } else {
                toastr.error("Something Went Wrong!");
            }

            $(this).attr("disabled", false);
            $(this).html("Submit");
            if (data.success == true || data.success == false) {
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
    });
});

// not in use
$("body").on("click", ".clChargebackChecked", function (e) {
    // var id = $(this).data('id');
    // if(confirm("Are you sure you want to revome the chargeback?")){
    //     $.ajax({
    //         type: 'POST',
    //         context: $(this),
    //         url: changeTransactionUnChargeback,
    //         data: {
    //             '_token': CSRF_TOKEN,
    //             'id': id
    //         },
    //         beforeSend: function() {
    //             $(this).attr('disabled', 'disabled');
    //         },
    //         success: function(data) {
    //             if(data.success == true) {
    //                 toastr.success('UnChargeback Update Successfully!');
    //                 window.setTimeout(
    //                     function(){
    //                         location.reload(true)
    //                     },
    //                     2000
    //                 );
    //             }
    //             else {
    //                 toastr.error('Something Went Wrong!');
    //             }
    //             $(this).attr('disabled', false);
    //         },
    //     });
    // }
});

// not in use
$("body").on("click", ".clRetrieval", function () {
    var id = $(this).data("id");
    $("#retrievalForm").append(
        '<input type="hidden" name="id" value="' + id + '">'
    );
    $("#retrievalForm").append('<input type="hidden" name="status" value="1">');
});

$("body").on("click", "#submitRetrieval", function (e) {
    e.preventDefault();
    $("#chargebacks_error").html("");
    var formdata = $("#retrievalForm").serialize();
    const apiUrl = $(this).data("link");
    $.ajax({
        type: "POST",
        context: $(this),
        url: apiUrl,
        data: formdata,
        beforeSend: function () {
            $(this).attr("disabled", "disabled");
            $(this).html(
                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
            );
        },
        success: function (data) {
            if (data.errors) {
                if (data.errors.changebanks_date) {
                    $("#chargebacks_error").html(
                        data.errors.changebanks_date[0]
                    );
                }
            }

            if (data.success == true) {
                toastr.success("Retrieval Updated Successfully!");
            } else {
                toastr.error("Something Went Wrong!");
            }

            $(this).attr("disabled", false);
            $(this).html("Submit");
            if (data.success == true || data.success == false) {
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
    });
});

// not in use
$("body").on("click", ".clRetrievalChecked", function (e) {
    var id = $(this).data("id");
    if (confirm("Are you sure you want to revome the retrieval?")) {
        $.ajax({
            type: "POST",
            context: $(this),
            url: changeTransactionUnRetrieval,
            data: {
                _token: CSRF_TOKEN,
                id: id,
            },
            beforeSend: function () {
                $(this).attr("disabled", "disabled");
            },
            success: function (data) {
                if (data.success == true) {
                    toastr.success("UnRetrieval Updated Successfully!");
                    window.setTimeout(function () {
                        location.reload(true);
                    }, 2000);
                } else {
                    toastr.error("Something Went Wrong!");
                }
                $(this).attr("disabled", false);
            },
        });
    }
});

// not in use
$(document).on("click", ".clFlagged", function () {
    var id = $(this).data("id");
    var flaggedBy = $(this).data("type");
    if (flaggedBy == "testpay") {
        $(".testpayOpt").remove();
    } else if (flaggedBy == "bank") {
        $(".bankOpt").remove();
    }
    $("#flaggedForm").append(
        '<input type="hidden" name="id" value="' + id + '">'
    );
    $("#flaggedForm").append('<input type="hidden" name="status" value="1">');
});

$("body").on("click", "#submitFlagged", function (e) {
    e.preventDefault();
    $("#flagged_type_error").html("");
    var formdata = $("#flaggedForm").serialize();
    const apiUrl = $(this).data("link");
    $.ajax({
        type: "POST",
        context: $(this),
        url: apiUrl,
        data: formdata,
        beforeSend: function () {
            $(this).attr("disabled", "disabled");
            $(this).html(
                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
            );
        },
        success: function (data) {
            if (data.errors) {
                if (data.errors.changebanks_date) {
                    $("#flagged_type_error").html(
                        data.errors.changebanks_date[0]
                    );
                }
            }

            if (data.success == true) {
                toastr.success("Suspicious Transaction updated Successfully!");
            } else {
                toastr.error("Something Went Wrong!");
            }

            $(this).attr("disabled", false);
            $(this).html("Submit");
            if (data.success == true || data.success == false) {
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
    });
});

$("body").on("click", ".clRemoveFlagged", function (e) {
    var id = $(this).data("id");
    if (confirm("Are you sure you want to revome the suspicious?")) {
        $.ajax({
            type: "POST",
            context: $(this),
            url: changeTransactionUnFlagged,
            data: {
                _token: CSRF_TOKEN,
                id: id,
            },
            beforeSend: function () {
                $(this).attr("disabled", "disabled");
            },
            success: function (data) {
                if (data.success == true) {
                    toastr.success("Transaction Updated Successfully!");
                    window.setTimeout(function () {
                        location.reload(true);
                    }, 2000);
                } else {
                    toastr.error("Something Went Wrong!");
                }
                $(this).attr("disabled", false);
            },
        });
    }
});

// not in use
$("body").on("change", 'input[name="TransactionCancel"]', function () {
    if ($(this).prop("checked") == true) {
        var status = "0";
    } else if ($(this).prop("checked") == false) {
        var status = "0";
    }
    var id = $(this).data("id");
    if (confirm("Are you sure you want to change this record?")) {
        $.ajax({
            type: "POST",
            context: $(this),
            url: changeTransactionStatus,
            data: {
                _token: CSRF_TOKEN,
                status: status,
                id: id,
            },
            beforeSend: function () {
                $(this).attr("disabled", "disabled");
            },
            success: function (data) {
                if (data.success == true) {
                    toastr.success("Transaction declined successfully!");
                    window.setTimeout(function () {
                        location.reload(true);
                    }, 2000);
                } else {
                    toastr.error("Something Went Wrong!");
                }
                $(this).attr("disabled", false);
            },
        });
    }
});

$(document).on("click", "#transactionMove", function () {
    var transaction_status = $("#transaction_status").find(":selected").val();
    var id = [];
    var status = "1";
    if (transaction_status != "-- Select Status --") {
        $(".multidelete:checked").each(function () {
            id.push($(this).val());
        });
        if (id.length > 0) {
            if (transaction_status == "flagged") {
                $("#transactionFlagged").modal("show");
                var transactionID = id;
                $("#flaggedForm").append(
                    '<input type="hidden" name="type" value="forall">'
                );
                $("#flaggedForm").append(
                    '<input type="hidden" name="id" value="' +
                        transactionID +
                        '">'
                );
                $("#flaggedForm").append(
                    '<input type="hidden" name="status" value="1">'
                );
            }
            if (transaction_status == "declined") {
                const apiUrl = $(this).data("change-transaction-status");
                if (confirm("Are you sure you want to change this record?")) {
                    $.ajax({
                        type: "POST",
                        context: $(this),
                        url: apiUrl,
                        data: {
                            _token: CSRF_TOKEN,
                            status: status,
                            id: id,
                            type: "forall",
                        },
                        beforeSend: function () {
                            $(this).attr("disabled", "disabled");
                        },
                        success: function (data) {
                            // console.log(data.success);
                            if (data.success == true) {
                                toastr.success(
                                    "Transaction declined Successfully!"
                                );
                                location.reload();
                            } else {
                                toastr.warning("Something went wrong!");
                            }
                            $(this).attr("disabled", false);
                        },
                    });
                }
            }
            if (transaction_status == "chargebacks") {
                $("#transactionChargebacks").modal("show");
                var transactionID = id;
                $("#chargebacksForm").append(
                    '<input type="hidden" name="type" value="forall">'
                );
                $("#chargebacksForm").append(
                    '<input type="hidden" name="id" value="' +
                        transactionID +
                        '">'
                );
                $("#chargebacksForm").append(
                    '<input type="hidden" name="status" value="1">'
                );
            }
            if (transaction_status == "refund") {
                $("#transactionRefund").modal("show");
                var refundTransactionID = id;
                $("#refundForm").append(
                    '<input type="hidden" name="type" value="forall">'
                );
                $("#refundForm").append(
                    '<input type="hidden" name="id" value="' +
                        refundTransactionID +
                        '">'
                );
                $("#refundForm").append(
                    '<input type="hidden" name="status" value="1">'
                );
            }
            if (transaction_status == "retrieval") {
                $("#transactionRetrieval").modal("show");
                var transactionID = id;
                $("#retrievalForm").append(
                    '<input type="hidden" name="type" value="forall">'
                );
                $("#retrievalForm").append(
                    '<input type="hidden" name="id" value="' +
                        transactionID +
                        '">'
                );
                $("#retrievalForm").append(
                    '<input type="hidden" name="status" value="1">'
                );
            }
        } else {
            toastr.warning("Please select atleast one transaction!");
        }
    } else {
        toastr.warning("Please selected any status!");
    }
});

// $(document).on('click','#deleteSelected',function(){
//     var id = [];
//     $('.multidelete:checked').each(function(){
//         id.push($(this).val());
//     });
//     const apiUrl = $(this).data('link');
//     if(id.length > 0)
//     {
//         if (confirm('Are you sure you want to delete this record?')) {
//             $.ajax({
//                 type: 'POST',
//                 context: $(this),
//                 url: apiUrl,
//                 data: {
//                     '_token': CSRF_TOKEN,
//                     'id': id, 'type':'forall'
//                 },
//                 beforeSend: function() {
//                     $(this).attr('disabled', 'disabled');
//                 },
//                 success: function(data) {
//                     if(data.success == true) {
//                         toastr.success('Transaction deleted Successfully!')
//                         location.reload();
//                     }
//                     else {
//                         toastr.warning('Something went wrong!');
//                     }
//                     $(this).attr('disabled', false);
//                 },
//             });
//         }
//     }
//     else
//     {
//         toastr.warning('Please select atleast one transaction!');
//     }
// })

$("body").on("click", ".deleteTransaction", function () {
    var id = $(this).data("id");
    const apiUrl = $(this).data("link");
    if (confirm("Are you sure you want to delete this record?")) {
        $.ajax({
            type: "POST",
            context: $(this),
            url: apiUrl,
            data: {
                _token: CSRF_TOKEN,
                id: id,
            },
            beforeSend: function () {
                $(this).attr("disabled", "disabled");
            },
            success: function (data) {
                if (data.success == true) {
                    toastr.success("Transaction deleted Successfully!");
                    location.reload();
                } else {
                    toastr.warning("Something went wrong!");
                }
                $(this).attr("disabled", false);
            },
        });
    }
});

$("body").on("click", ".flagged-show-document", function () {
    var id = $(this).data("id");
    $("#detailsContent").html("");
    const apiUrl = $(this).data("link");
    $.ajax({
        url: apiUrl,
        type: "POST",
        data: { _token: CSRF_TOKEN, id: id },
        beforeSend: function () {
            $("#detailsContent").html(
                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
            );
        },
        success: function (data) {
            $("#detailsContent").html(data.html);
        },
    });
});

$("body").on("click", ".chargebacks-show-document", function () {
    var id = $(this).data("id");
    $("#detailsContent").html("");
    const apiUrl = $(this).data("link");
    $.ajax({
        url: apiUrl,
        type: "POST",
        data: { _token: CSRF_TOKEN, id: id },
        beforeSend: function () {
            $("#detailsContent").html(
                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
            );
        },
        success: function (data) {
            $("#detailsContent").html(data.html);
        },
    });
});

$("body").on("click", ".retrieval-show-document", function () {
    var id = $(this).data("id");
    $("#detailsContent").html("");
    const apiUrl = $(this).data("link");
    $.ajax({
        url: apiUrl,
        type: "POST",
        data: { _token: "{{ csrf_token() }}", id: id },
        beforeSend: function () {
            $("#detailsContent").html(
                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
            );
        },
        success: function (data) {
            $("#detailsContent").html(data.html);
        },
    });
});
