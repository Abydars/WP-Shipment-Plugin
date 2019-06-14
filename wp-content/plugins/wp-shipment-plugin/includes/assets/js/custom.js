jQuery(function ($) {

    $('#listShipments').DataTable({
        "order": [[1, "desc"]]
    });

    setTimeout(function () {
        $('div#shipment-form').hide();
    }, 200)

    $('body').on('label.form.loaded', function () {
        formInit();
    });

    $(document).on('change', '#shipment_form select[name="carrier"]', function () {
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
        if ($('#shipment_form .customers-list select').val() == "" || $('.shipping-carrier select').val() == "") {
            alert('Please select a customer and shipping carrier before adding a new address');
            $('#addAddressModal').hide()
        } else {
            $('#addAddressModal form').trigger("reset");
            $('#addAddressModal').show();
        }
    });

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
    });

    $(document).on('click', '#btn-new-to-address', function (e) {
        e.preventDefault();
        if ($('#shipment_form .customers-list select').val() == "" || $('.shipping-carrier select').val() == "") {
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
            '<label>Weight (ounces)</label>' +
            '<input type="text" name="packages[' + arrIndex + '][weight]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Length (inches)</label>' +
            '<input type="text" name="packages[' + arrIndex + '][length]">' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Width (inches)</label>' +
            '<input type="text" name="packages[' + arrIndex + '][width]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row">' +
            '<div class="wpsp-one-half">' +
            '<div class="wpsp-form-group">' +
            '<label>Height (inches)</label>' +
            '<input type="text" name="packages[' + arrIndex + '][height]">' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-one-half" style="display: none;">' +
            '<div class="wpsp-form-group">' +
            '<label>SKU</label>' +
            '<input type="text" name="packages[' + arrIndex + '][sku]">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="wpsp-row" style="display: none;">' +
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

    $('#wp-admin-bar-create-label').click(function (e) {
        e.preventDefault();
        $('div#shipment-form').toggle();
    })

    $('.shipment_detail_actions .void-label').click(function (e) {
        e.preventDefault();

        var shipment_id = $(this).attr('data-shipment-id');
        var refund = $(this).attr('data-refund');
        var $btn = $(this);
        var text = $btn.text();

        $btn.text('Please wait...');
        $btn.attr('disabled', 'disabled');

        $('#shipmentDetails .wpsp-error').hide();

        $.ajax({
            dataType: 'JSON',
            url: wsp_ajax_url,
            data: {
                action: 'wpsp_void_label',
                id: shipment_id,
                refund: refund
            },
            success: function (response) {
                if (response.status) {
                    location.reload();
                } else {
                    $('#shipmentDetails .wpsp-error').show().find('p').text(response.message);
                }

                $btn.text(text);
                $btn.removeAttr('disabled', 'disabled');
            }
        })
    })

    function refreshAddresses() {
        var customer_id = $('#shipment_form .customers-list select').val();

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: {
                customer_id: customer_id,
                action: 'wpsp_get_addresses'
            },
            url: wsp_ajax_url,
            success: function (response) {
                $('#shipment_form .from-address select').empty();
                $('#shipment_form .to-address select').empty();

                for (var i = 0; i < response.length; i++) {
                    $('.from-address select').append('<option value="' + response[i].id + '">' + response[i].address_name + '</option>')
                    $('.to-address select').append('<option value="' + response[i].id + '">' + response[i].address_name + '</option>')
                }
            }
        })
    }

    function formInit() {
        $('#rateShop').hide();

        $('#shipment_form').submit(function (e) {
            e.preventDefault();

            var form_data = $(this).serializeArray();
            var $btn = $(this).find('button[type="submit"]');

            $btn.attr('disabled', 'disabled');
            $btn.text('Please wait...');

            $.ajax({
                type: 'POST',
                data: form_data,
                url: wsp_ajax_url,
                dataType: 'JSON',
                success: function (response) {
                    if (response.status) {
                        $('#shipment_form .wpsp-error').hide();
                        $('#shipment_form .wpsp-success').show().find('p').text(response.message);

                        $('#shipment_form').trigger('reset');
                    } else {
                        $('#shipment_form .wpsp-success').hide();
                        $('#shipment_form .wpsp-error').show().find('p').text(response.message);
                    }

                    $('#wpsp-shipment-form-container').animate({
                        scrollTop: 0
                    });

                    $btn.removeAttr('disabled', 'disabled');
                    $btn.text('Create Shipment');
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
                if (form_data[i].name === 'action') {
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

        $(document).on('change', '#shipment_form .customers-list select', function () {
            refreshAddresses()
        })
    }
})