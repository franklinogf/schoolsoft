$(document).ready(function () {

    $(document).on("click", ".dispatch", (function (e) {
        const thisButton = $(this)
        let orderId = thisButton.data('orderId')
        thisButton.parents('li').hide(function () {
            thisButton.remove()
        });
        $.post(includeThisFile(), { orderId });
    }))

    setInterval(function () {
        $.post(includeThisFile(), { updateOrders: true },
            function (data, textStatus, jqXHR) {
                $("#ordersList").empty();
                $("#ordersList").append(data);
            }
        )
    }, 10000)
});