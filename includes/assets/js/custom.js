jQuery(document).ready(function ($) {
    $(document).on('click','.right-sidebar .from-address', function (e) {
        e.preventDefault();
        $('#fromAddress').show();
    })
    $(document).on('click','.right-sidebar .close', function (e) {
        e.preventDefault();
        $('#fromAddress').hide();
    })
    $(document).on('click','.right-sidebar .to-address', function (e) {
        e.preventDefault();
        $('#toAddress').show();
    })
    $(document).on('click','.right-sidebar .close', function (e) {
        e.preventDefault();
        $('#toAddress').hide();
    })
    $(document).on('click','.right-sidebar .add_new_package', function (e) {
        e.preventDefault();
        $('.new-package').show();
    })
    $(document).on('click','.right-sidebar .close', function (e) {
        e.preventDefault();
        $('.new-package').hide();
    })

    $(document).on('click','.actions .cancel', function (e) {
        e.preventDefault();
        $('.new-package').hide();
    })


    $('#listShipments').DataTable();

    // Shipment Actions

    $('#shipment_form').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            data: {
                action: 'save_label',
                data: $(this).serialize()
            },
            url: wsp_ajax_url,
            dataType: 'JSON',
            success: function (response) {
                console.log(response)
            }
        })
    })

    $('#fromAddress form').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'save_from_address',
                data: $(this).serialize()
            },
            url: wsp_ajax_url,
            success: function (response) {
                console.log(response)
            }
        })
    })

    $('#toAddress form').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'save_to_address',
                data: $(this).serialize()
            },
            url: wsp_ajax_url,
            success: function (response) {
                console.log(response)
            }
        })
    })
})