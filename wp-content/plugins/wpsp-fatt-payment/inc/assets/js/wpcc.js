jQuery(function ($) {

    $('.select-payment-type select').on('change', function () {
        var type = $(this).val();

        if (type === 'eCheck') {
            $('.card-fields').hide();
            $('.echeck-fields').slideDown();
        } else {
            $('.echeck-fields').hide();
            $('.card-fields').slideDown();
        }
    });

    if ($('.select-payment-type select').length > 0 && $('.select-payment-type select').val() != '') {
        $('.select-payment-type select').trigger('change');
    }

    $('.btn-delete-fatt-customer').on('click', function (e) {
        e.preventDefault();

        if (confirm('Are you sure?')) {
            window.location.href = $(this).attr('href');
        }
    });

    $('.btn-view-fatt-logs').on('click', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var $logs_div = $('#wpcc-logs');

        $logs_div.empty();
        $('#logsModal').show();

        $.ajax({
            url: wpcc_ajax_url,
            data: {
                action: 'wpcc_get_customer_logs',
                id: id
            },
            dataType: "JSON",
            success: function (logs) {
                $logs_div.empty();

                if (logs.length > 0) {
                    for (var i in logs) {
                        $logs_div.prepend('<p><u>' + logs[i][0] + '</u>: ' + logs[i][1] + '</p>');
                    }
                } else {
                    $logs_div.append('<p>No logs</p>');
                }
            }
        });
    });
});