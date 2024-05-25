$(document).ready(function () {
    // * By Default fetch Daily summary report
    fetchSummaryReport(0);

    $(document).on("click", ".transactionSummaryFilter", function () {
        $(".transactionSummaryFilter").removeClass("active");
        $(this).addClass("active");
        var value = $(this).attr("data-value");
        fetchSummaryReport(value);
    });

    // * Listen the merchant select box changes
    $(document).on("change", ".merchantSelectBox", function () {
        var user_id = $(this).val();
        $.ajax({
            type: "POST",
            url: merchantTxnPercentUrl,
            data: {
                _token: CSRF_TOKEN,
                user_id: user_id,
            },
            beforeSend: function () {
                $("#merchantTxnPercentages").html(appendLoader());
            },
            success: function (res) {
                if (res.status == 200) {
                    $("#merchantTxnPercentages").html(res.html);
                } else if (res.status == 500) {
                    toastr.error(res.message);
                }
            },
            error: function () {
                toastr.error("Something went wrong!Please try again later!");
            },
        });
    });

    // * Listen the agent select box changes
    $(document).on("change", ".agentSelectBox", function () {
        var agentId = $(this).val();
        $.ajax({
            url: agentMerchantOverviewUrl,
            method: "POST",
            data: {
                _token: CSRF_TOKEN,
                agent_id: agentId,
            },
            beforeSend: function () {
                $("#agentMerchantsOverview").html(appendLoader());
            },
            success: function (res) {
                if (res.status == 200) {
                    $("#agentMerchantsOverview").html(res.html);
                } else if (res.status == 500) {
                    toastr.error(res.message);
                }
            },
            error: function () {
                toastr.error("Something went wrong!Please try again later!");
            },
        });
    });
});

// * Fetch the first daily report
function fetchSummaryReport(value) {
    $.ajax({
        type: "POST",
        url: transactionSummaryURL,
        data: {
            _token: CSRF_TOKEN,
            value: value,
        },
        success: function (res) {
            var html = res.html;
            $("#dashboardTransactionSummary").html(html);
        },
    });
}
