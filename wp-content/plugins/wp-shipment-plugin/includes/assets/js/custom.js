jQuery(function ($) {

    $(document).on('click', '#btn-new-from-address', function (e) {
        e.preventDefault();

        $('#addAddressModal form').trigger("reset");
        $('#addAddressModal').show();
    })

    $(document).on('click', '#btn-new-to-address', function (e) {
        e.preventDefault();

        $('#addAddressModal form').trigger("reset");
        $('#addAddressModal').show();
    })

    $(document).on('click', '#btn-new-package', function (e) {
        e.preventDefault();

        $('#addNewPackage form').trigger("reset");
        $('#addNewPackage').show();
    })

    $(document).on('click', '.modal .close', function (e) {
        e.preventDefault();
        $(this).parents('.modal').hide();
    })

    $(document).on('click', '.modal .action .cancel', function (e) {
        e.preventDefault();
        $(this).parents('.modal').hide();
    })


    $('#listShipments').DataTable();

    $('body').on('label.form.loaded', function () {
        formInit();
    });

    function formInit() {
        $('#shipment_form').submit(function (e) {
            e.preventDefault();

            var form_data = $(this).serializeArray()

            $.ajax({
                type: 'POST',
                data: form_data,
                url: wsp_ajax_url,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response)
                }
            });

            return false;
        });

        $('#addAddressModal form').submit(function (e) {
            e.preventDefault();

            var form_data = $(this).serializeArray()
            form_data.push({
                'name': 'customer',
                'value': $('select[name="customer"]').val()
            });

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                url: wsp_ajax_url,
                success: function (response) {
                    console.log(response)
                }
            })
        });
    }
})