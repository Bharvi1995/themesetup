$(document).ready(function () {
    $("#account-details-trigger").click(function () {
        var url = $(this).attr("data-url");
        window.location.replace(url);
    });
    $("#director-info-trigger").click(function () {
        var url = $(this).attr("data-url");
        window.location.replace(url);
    });
    $("#personal-info-trigger").click(function () {
        var url = $(this).attr("data-url");
        window.location.replace(url);
    });
    $("#address-step-trigger").click(function () {
        var url = $(this).attr("data-url");
        window.location.replace(url);
    });
    $("#extra-step-trigger").click(function () {
        var url = $(this).attr("data-url");
        window.location.replace(url);
    });
});