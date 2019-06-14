jQuery(function ($) {

    $(document).on('change', 'select[name="carrier"]', function () {
        var carrier = $(this).val();
        var $select_levels = $('select[name="shipping_method"]');
        var $package_types = $('select[name="package_type"]');

        $.ajax({
            url: wsp_ajax_url,
            data: {
                action: 'wpsp_shipment_carrier_levels',
                carrier: carrier
            },
            dataType: "JSON",
            success: function (res) {
                $select_levels.empty();

                for (var i in res) {
                    $select_levels.append('<option>' + res[i] + '</option>');
                }
            }
        });

        $.ajax({
            url: wsp_ajax_url,
            data: {
                action: 'wpsp_shipment_package_types',
                carrier: carrier
            },
            dataType: "JSON",
            success: function (res) {
                $package_types.empty();

                for (var i in res) {
                    $package_types.append('<option>' + res[i] + '</option>');
                }
            }
        });
    });

    $(document).on('click', '#btn-new-from-address', function (e) {
        e.preventDefault();
        if ($('.customers-list select').val() == "" || $('.shipping-carrier select').val() == "") {
            alert('Please select a customer and shipping carrier before adding a new address');
            $('#addAddressModal').hide()
        } else {
            $('#addAddressModal form').trigger("reset");
            $('#addAddressModal').show();
        }
    })

    $(document).on('click', '#btn-edit-from-address', function (e) {
        e.preventDefault();
        $('#editAddressModal').show();
        var currentTarget = e.currentTarget;
        var id = $(currentTarget).data('id');
        var data = $(currentTarget).parent().siblings();
        $('#editAddressModal input[name="full_name"]').val($($(data)[1]).html());
        $('#editAddressModal input[name="company"]').val($($(data)[2]).html());
        $('#editAddressModal input[name="country"]').val($($(data)[3]).html());
        $('#editAddressModal input[name="city"]').val($($(data)[4]).html());
        $('#editAddressModal input[name="street_1"]').val($($(data)[5]).html());
        $('#editAddressModal input[name="street_2"]').val($($(data)[6]).html());
        $('#editAddressModal input[name="state"]').val($($(data)[7]).html());
        $('#editAddressModal input[name="zip_code"]').val($($(data)[8]).html());
        $('#editAddressModal input[name="phone"]').val($($(data)[9]).html());
        $('#editAddressModal input[name="email"]').val($($(data)[10]).html());
        $('#editAddressModal input[name="id"]').val(id);


    })

    $(document).on('click', '#btn-new-to-address', function (e) {
        e.preventDefault();
        if ($('.customers-list select').val() == "" || $('.shipping-carrier select').val() == "") {
            alert('Please select a customer and shipping carrier before adding a new address');
            $('#addAddressModal').hide()
        } else {
            $('#addAddressModal form').trigger("reset");
            $('#addAddressModal').show();
        }
    })

    $(document).on('click', '.modal .close', function (e) {
        e.preventDefault();
        $(this).parents('.modal').hide();
    })

    $(document).on('click', '.modal .action .cancel', function (e) {
        e.preventDefault();
        $(this).parents('.modal').hide();
    })

    $(document).on('change', '.schedule-pickup input[type="checkbox"]', function () {
        if ($(this).prop("checked") == true) {
            $('.pickup-schedule').show()
        } else if ($(this).prop("checked") == false) {
            $('.pickup-schedule').hide()
        }
    });

    $('#listShipments').DataTable();

    $('body').on('label.form.loaded', function () {
        formInit();
    });

    function refreshAddresses() {
        var customer_id = $('.customers-list select').val();
        var action = 'wpsp_get_addresses';
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: {
                customer_id: customer_id,
                action: action
            },
            url: wsp_ajax_url,
            success: function (response) {
                $('.from-address select').empty();
                $('.to-address select').empty();
                for (var i = 0; i < response.length; i++) {
                    $('.from-address select').append('<option value="' + response[i].id + '">' + response[i].address_name + '</option>')
                    $('.to-address select').append('<option value="' + response[i].id + '">' + response[i].address_name + '</option>')
                }
            }
        })
    }

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

            var form_data = $(this).serializeArray();

            form_data.push({
                'name': 'customer',
                'value': $('select[name="customer"]').val()
            });

            form_data.push({
                'name': 'carrier',
                'value': $('select[name="carrier"]').val()
            });

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                url: wsp_ajax_url,
                success: function (response) {
                    $('#addAddressModal').hide();

                    if (response.status) {
                        refreshAddresses();
                    } else {
                        alert(response.message);
                    }
                }
            })
        });

        $('#editAddressModal form').submit(function (e) {
            e.preventDefault();

            var form_data = $(this).serializeArray();

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                url: wsp_ajax_url,
                success: function (response) {
                    $('#addAddressModal').hide();

                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            })
        });

        $(document).on('click', '#rateShop button', function (e) {
            e.preventDefault();
            var carrier = $(this).val();
            $('.shipping-carrier select').val(carrier).trigger('change');
            $('#rateShop').hide();
        })

        $(document).on('click', '#rate-shop', function (e) {
            e.preventDefault();
            var form_data = $('#shipment_form').serializeArray()
            for (var i = 0; i <= form_data.length; i++) {
                if (form_data[i].name == 'action') {
                    form_data[i].value = 'wpsp_get_rates';
                    break;
                }
            }
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                url: wsp_ajax_url,
                success: function (response) {
                    $('#rateShop').show();
                }
            })
        })

        $(document).on('change', '.customers-list select', function () {
            refreshAddresses()
        })

        $('.shipment_detail_actions .void-label').click(function (e) {
            e.preventDefault();
            var shipment_id = $(this).attr('data-shipment-id');
            $.ajax({
                dataType: 'JSON',
                url: wsp_ajax_url,
                data: {
                    action: 'wpsp_void_label',
                    shipment_id: shipment_id
                },
                success: function (response) {
                    console.log(response);
                }
            })
        })
    }

    $('#rateShop').hide();

    $(document).on('click', '#btn-new-package', function (e) {
        e.preventDefault();
        var count = $('.package').length;
        var arrIndex = count - 1;
        $('.right-sidebar .packages').append('<div class="package">' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<h4>Package #' + count + '</h4>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<p class="delete">Delete</p>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Weight</label>' +
            '<input type="text" name="packages[' + arrIndex + '][weight]">' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Weight Unit</label>' +
            '<select name="packages[' + arrIndex + '][unit]">' +
            '<option value=""></option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Length</label>' +
            '<input type="text" name="packages[' + arrIndex + '][length]">' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Width</label>' +
            '<input type="text" name="packages[' + arrIndex + '][width]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Height</label>' +
            '<input type="text" name="packages[' + arrIndex + '][height]">' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>SKU</label>' +
            '<input type="text" name="packages[' + arrIndex + '][sku]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Declared Currency</label>' +
            '<select name="packages[' + arrIndex + '][declared_currency]">' +
            '<option value=""></option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Declared Customs Value</label>' +
            '<input type="text" name="packages[' + arrIndex + '][declared_customs_value]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');
    })

    $(document).on('click', '.package .delete', function (e) {
        e.preventDefault();
        $(this).parents('.package').remove();
    })

    setTimeout(function () {
        $('div#shipment-form').hide();
    }, 200)

    $('#wp-admin-bar-create-label').click(function (e) {
        e.preventDefault();
        $('div#shipment-form').toggle();
    })
})