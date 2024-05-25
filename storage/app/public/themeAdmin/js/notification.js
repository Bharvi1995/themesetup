window.Echo.private('user.notification.' + window.user_id)
    .listen('.notification.created', (data) => {
        var count = parseInt($(".notification-count").html() || 0) + 1;
        $(".notification-count").html(count);
        $(".notification-count").css("display", "block");
        var html = '<li><a href="/admin/read-admin-notifications/' + data.id + '" >';
        html += '<p class="mb-0">' + data.title + '</p>';
        html += '<p class="text-danger text-right mb-0">';
        html += '<small>' + data.created_at_date + '</small>';
        html += '</p></a></li>';
        $(".notification-list").prepend(html);
});
