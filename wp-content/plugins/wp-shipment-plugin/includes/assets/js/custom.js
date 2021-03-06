jQuery(function ($) {

    $('#listShipments').DataTable( {
        order: [[0, "desc"]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csv',
                text: 'Export CSV',
                exportOptions: {
                    columns: ':visible :not(.js-not-exportable)'
                }
            }

        ]
        // order: [[0, "desc"]]
    } );

    $('.wpsp-datatable').DataTable();

    setTimeout(function () {
        $('div#shipment-form').hide();
    }, 200);

    $('body').on('label.form.loaded', function () {
        formInit();
    });

    $(document).on('change', '#shipment_form select[name="carrier"]', function (e, level, package_type, auto_submit) {

        var carrier = $(this).val();
        var $select_levels = $('select[name="shipping_method"]');
        var $package_types = $('select[name="package_type"]');
        var levels_loaded = false;
        var types_loaded = false;

        function after_load() {
            if (auto_submit) {
                $('#shipment_form').submit();
            }
        }

        $.ajax({
            url: wsp_ajax_url,
            data: {
                action: 'wpsp_shipment_carrier_levels',
                carrier: carrier
            },
            dataType: "JSON",
            success: function (res) {
                $select_levels.empty();

                $.each(res, function (index, value) {
                    $select_levels.append('<option value="' + index + '">' + value + '</option>');
                });

                $select_levels.append('<option value="">Default</option>');

                if (level !== undefined)
                    $select_levels.val(level);

                levels_loaded = true;
                after_load();
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

                $.each(res, function (index, value) {
                    $package_types.append('<option value="' + index + '">' + value + '</option>');
                });

                $package_types.append('<option value="">Default</option>');

                if (package_type !== undefined)
                    $package_types.val(package_type);

                types_loaded = true;
                after_load();
            }
        });
    });

    $(document).on('click', '#btn-new-from-address', function (e) {
        e.preventDefault();
        if ($('#shipment_form .customers-list select').val() == "") {
            alert('Please select a customer and shipping carrier before adding a new address');
            $('#addAddressModal').hide();
        } else {
            $('#addAddressModal form').trigger("reset");
            $('#addAddressModal').show();
        }
    });

    $(document).on('click', '.address_actions .btn-edit-address', function (e) {
        e.preventDefault();

        $('#editAddressModal').show();

        var tbl = $('#listShipments').DataTable();
        var currentTarget = e.currentTarget;
        var key = $(currentTarget).data('key');
        var id = $(currentTarget).data('id');
        var data = tbl.rows(key).data();
        var row_data = {};

        $('#listShipments').find('thead th').each(function (i) {
            row_data[$(this).text()] = data[0][i];
        });

        $('#editAddressModal input[name="code"]').val(row_data['Code']);
        $('#editAddressModal input[name="full_name"]').val(row_data['Full Name']);
        $('#editAddressModal input[name="company"]').val(row_data['Company']);
        $('#editAddressModal select[name="country"]').val(row_data['Country']);
        $('#editAddressModal input[name="city"]').val(row_data['City']);
        $('#editAddressModal input[name="street_1"]').val(row_data['Street 1']);
        $('#editAddressModal input[name="street_2"]').val(row_data['Street 2']);
        $('#editAddressModal select[name="state"]').attr('data-value', row_data['State']);
        $('#editAddressModal input[name="zip_code"]').val(row_data['Zip Code']);
        $('#editAddressModal input[name="phone"]').val(row_data['Phone']);
        $('#editAddressModal input[name="email"]').val(row_data['Email']);
        $('#editAddressModal input[name="id"]').val(id);

        $('#editAddressModal select[name="country"]').chosen().trigger("chosen:updated");
        $('#editAddressModal select[name="state"]').chosen().trigger("chosen:updated");

        $('#editAddressModal select[name="country"]').trigger('change');
        $('#editAddressModal select[name="state"]').trigger('change');
    });

    $(document).on('click', '.address_actions .btn-delete-address', function (e) {
        e.preventDefault();

        var id = $(this).data('id');

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'wpsp_delete_address',
                id: id
            },
            url: wsp_ajax_url,
            success: function (response) {
                if (response.status) {
                    location.href = location.href + '&success=' + response.message;
                } else {
                    alert(response.message);
                }
            }
        })
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
    });

    $(document).on('click', '.modal .close', function (e) {
        e.preventDefault();
        $(this).parents('.modal').hide();
    });

    $(document).on('click', '.modal .action .cancel', function (e) {
        e.preventDefault();
        $(this).parents('.modal').hide();
    });

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
            '<div class="wpsp-one-half"> \
            <div class="wpsp-form-group"> \
            <label>Weight unit</label>\
            <select name="packages[' + arrIndex + '][unit]" required>\
            <option value="">Select Weight Unit</option>\
            <option value="oz">Ounces</option>\
            <option value="lbs">Pounds</option>\
            </select>\
            </div>\
            </div>' +
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
            '</div>' +
            '</div>' +
            '<div class="wpsp-clearfix"></div>'
        );
    });

    $(document).on('click', '.package .delete', function (e) {
        e.preventDefault();
        $(this).parents('.package').remove();
    });

    $('#wp-admin-bar-create-label, a[data-wpsp-create-label], a[href="#wpsp_create_label"]').click(function (e) {
        e.preventDefault();
        $('div#shipment-form').toggle();
    });

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
    });

    $('#editAddressModal form').submit(function (e) {
        e.preventDefault();

        var form_data = $(this).serializeArray();
        var $btn = $(this).find('button[type="submit"]');

        $btn.attr('disabled', 'disabled');
        $btn.attr('data-text', $btn.text());
        $btn.text('Please wait...');

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: form_data,
            url: wsp_ajax_url,
            success: function (response) {
                $('#addAddressModal').hide();

                $btn.removeAttr('disabled');
                $btn.text($btn.attr('data-text'));

                if (response.status) {
                    location.href = location.href + '&success=' + response.message;
                } else {
                    alert(response.message);
                }
            }, error: function () {
                $btn.removeAttr('disabled');
                $btn.text($btn.attr('data-text'));
            }
        })
    });

    $(document).on('change', '.select-country select', function () {
        var country = $(this).val();
        var $states = $(this).parents('form').find('.select-state').find('select');
        var default_state = $states.attr('data-value');

        $.ajax({
            url: wsp_ajax_url,
            data: {
                action: 'wpsp_get_states',
                country: country
            },
            dataType: "JSON",
            success: function (response) {
                $states.empty();

                if (response.length > 0) {
                    for (var i in response) {
                        var state = response[i];
                        var selected = default_state === state.code;

                        $states.append('<option value="' + state.code + '"' + (selected ? " selected" : "") + '>' + state.name + '</option>');
                    }
                }

                $states.chosen().trigger("chosen:updated");
            }
        });
    });

    $(document).on('reset', 'form', function () {
        var $form = $(this);

        $form.find('.wpsp-chosen').each(function () {
            $(this).val('').trigger('change').chosen().trigger("chosen:updated");
        });
    });

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

                $('.from-address select').chosen().trigger("chosen:updated");
                $('.to-address select').chosen().trigger("chosen:updated");
            }
        })
    }

    function refreshRates(all_rates) {
        var $div = $('#rateShop #rates-list');

        $div.empty();

        for (var key in all_rates) {
            var rate = all_rates[key];
            var name = rate['name'];

            if (rate.error != undefined) {
                var $h2 = $('<h2>');
                $h2.html(name);
                $h2.append('<small>' + rate.error + '</small>');

                $div.append($h2);
            } else {

                var rates = rate['rates'];
                var pickup_rates = rate['pickup_rates'];

                var $h2 = $('<h2>');
                $h2.html(name);

                if (pickup_rates > 0) {
                    //$h2.append('<small>Pickup rates will be applied - $' + pickup_rates.toFixed(2) + '</small>');
                }

                if (rates.length > 0) {
                    var $rates_tbl = $('<table/>');
                    var $rates_th = $('<thead><tr><th>Level</th><th>Rate</th><th>Action</th></tr></thead>');

                    $rates_tbl.append($rates_th);

                    for (var i in rates) {
                        var level_rate = rates[i];

                        var $tr = $('<tr/>');
                        var $level_td = $('<td/>');
                        var $total_rate_td = $('<td/>');
                        var $action_td = $('<td/>');
                        var $select_btn = $('<button/>');

                        $select_btn.attr('data-carrier', key);
                        $select_btn.attr('data-level', level_rate.level);
                        $select_btn.attr('data-package-type', level_rate.package_type);
                        $select_btn.text('Select');

                        $level_td.html(level_rate.name);
                        $total_rate_td.text('$' + level_rate.total);
                        $action_td.append($select_btn);

                        $tr.append($level_td)
                            .append($total_rate_td)
                            .append($action_td);

                        $rates_tbl.append($tr);
                    }

                    $div.append($h2);
                    $div.append($rates_tbl);
                }
            }
        }
    }

    function formInit() {
        $('#rateShop').hide();

        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear() + "-" + (month) + "-" + (day);

        $('.wpsp-chosen').chosen();

        $('.shipping-date input').val(today);
        $('.shipping-carrier select').trigger('change');

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

                    if (response.nonce) {
                        $('#shipment_form input#_wpnonce').val();
                    }
                }
            });

            return false;
        });

        $('#addAddressModal form').submit(function (e) {
            e.preventDefault();

            var form_data = $(this).serializeArray();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');

            form_data.push({
                'name': 'customer',
                'value': $('select[name="customer"]').val()
            });

            form_data.push({
                'name': 'carrier',
                'value': $('select[name="carrier"]').val()
            });

            $btn.attr('disabled', 'disabled');
            $btn.attr('data-text', $btn.text());
            $btn.text('Please wait...');

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                url: wsp_ajax_url,
                success: function (response) {

                    $btn.val($btn.attr('data-text'));
                    $btn.removeAttr('disabled');

                    if (response.status) {
                        $('#addAddressModal').hide();
                        refreshAddresses();
                    } else {
                        alert(response.message);

                        if (response.nonce) {
                            $form.find('input[name="_wpnonce"]').val(response.nonce);
                        }
                    }
                },
                error: function (e) {
                    $btn.text($btn.attr('data-text'));
                    $btn.removeAttr('disabled');
                }
            })
        });

        $(document).on('click', '#rates-list button', function (e) {
            e.preventDefault();

            var carrier = $(this).attr('data-carrier');
            var level = $(this).attr('data-level');
            var package_type = $(this).attr('data-package-type');

            $('#rateShop').hide();
            $('.shipping-carrier select').val(carrier).trigger('change', [level, package_type]);
        });

        $(document).on('click', '#rate-shop, #rate-shop-send', function (e) {
            e.preventDefault();

            var form_data = $('#shipment_form').serializeArray();
            var $btn = $(this);
            var text = $btn.text();
            var auto_send = $btn.attr('data-auto-send') === '1';
            var err = false;

            for (var i = 0; i <= form_data.length; i++) {
                if (form_data[i].name === 'action') {
                    form_data[i].value = 'wpsp_get_rates';
                    break;
                }
            }

            if ($('#shipment_form .customers-list select').val() == "") {
                err = 'Please select customer first';
            } else if ($('#shipment_form .from-address select').val() == "") {
                err = 'Please select from address';
            } else if ($('#shipment_form .to-address select').val() == "") {
                err = 'Please select to address';
            } else if ($('#shipment_form .package').first().find('input[name*="weight"]').val() == "") {
                err = 'Package #1 weight is required';
            } else if ($('#shipment_form .package').first().find('select[name*="unit"]').val() == "") {
                err = 'Package #1 unit is required';
            } else if ($('#shipment_form .package').first().find('input[name*="width"]').val() == "") {
                err = 'Package #1 width is required';
            } else if ($('#shipment_form .package').first().find('input[name*="height"]').val() == "") {
                err = 'Package #1 height is required';
            } else if ($('#shipment_form .package').first().find('input[name*="length"]').val() == "") {
                err = 'Package #1 length is required';
            }

            if (err !== false) {
                alert(err);
                return;
            }

            $btn.text('Please wait...');
            $btn.attr('disabled', 'disabled');

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                url: wsp_ajax_url,
                success: function (response) {
                    if (response.status) {
                        refreshRates(response.data.rates);

                        if (auto_send) {
                            $('.shipping-carrier select').val(response.data.lowest.carrier).trigger('change', [response.data.lowest.level, response.data.lowest.package_type, true]);
                        } else {
                            $('#rateShop').show();
                        }
                    } else {
                        $('#shipment_form .wpsp-success').hide();
                        $('#shipment_form .wpsp-error').show().find('p').text(response.message);

                        $('#wpsp-shipment-form-container').animate({
                            scrollTop: 0
                        });
                    }

                    $btn.text(text);
                    $btn.removeAttr('disabled');
                }
            })
        });

        $(document).on('change', '#shipment_form .customers-list select', function () {
            refreshAddresses();
        });
    }
});