jQuery(function ($) {

    $(document).on('click', '#btn-new-from-address', function (e) {
        e.preventDefault();
        if ($('.customers-list select').val() == "") {
            alert('Please select a customer before adding a new address');
            $('#addAddressModal').hide()
        } else {
            $('#addAddressModal form').trigger("reset");
            $('#addAddressModal').show();
        }
    })

    $(document).on('click', '#btn-new-to-address', function (e) {
        e.preventDefault();
        if ($('.customers-list select').val() == "") {
            alert('Please select a customer before adding a new address');
            $('#addAddressModal').hide()
        } else {
            $('#addAddressModal form').trigger("reset");
            $('#addAddressModal').show();
        }
    })

    // $(document).on('click', '#btn-new-package', function (e) {
    //     e.preventDefault();
    //
    //     $('#addNewPackage form').trigger("reset");
    //     $('#addNewPackage').show();
    // })

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

    $(document).on('click', '#btn-new-package', function (e) {
        e.preventDefault();
        var count = $('.package').length;
        var arrIndex = count -1;
        $('.right-sidebar .packages').append('<div class="package">' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<h4>Package #'+count+'</h4>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<p class="delete">Delete</p>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Weight</label>' +
            '<input type="text" name="packages['+arrIndex+'][weight]">' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Weight Unit</label>' +
            '<select name="packages['+arrIndex+'][unit]">' +
            '<option value=""></option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Length</label>' +
            '<input type="text" name="packages['+arrIndex+'][length]">' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Width</label>' +
            '<input type="text" name="packages['+arrIndex+'][width]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Height</label>' +
            '<input type="text" name="packages['+arrIndex+'][height]">' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>SKU</label>' +
            '<input type="text" name="packages['+arrIndex+'][sku]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Declared Currency</label>' +
            '<select name="packages['+arrIndex+'][declared_currency]">' +
            '<option value=""></option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Declared Customs Value</label>' +
            '<input type="text" name="packages['+arrIndex+'][declared_customs_value]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');
    })

    $(document).on('click', '.package .delete', function (e) {
        e.preventDefault;
        $(this).parents('.package').remove();
    })
})