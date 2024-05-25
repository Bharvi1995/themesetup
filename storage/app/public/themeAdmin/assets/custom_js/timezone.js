$(document).ready(function (){
    $.ajax({
        url: '/save-local-timezone',
        method: "get",
        data: { timezone: Intl.DateTimeFormat().resolvedOptions().timeZone },
        success: function (response) {
        }
    });
});
