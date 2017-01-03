/*!
 * Cart
 * Copyright 2016 Aerendir
 * Licensed under a MIT license
 */

/* ========================================================================
 * Store Cart v1
 *
 * Manages a cart on the front end.
 * ========================================================================
 * Copyright 2016 Aerendir
 * Licensed under a NON DISCLOSE LICENSE
 * ======================================================================== */

$(document).ready(function() {
    var amount = 0,
        instantAmount = 0,
        currencyMask = function (amount) {
            return parseFloat(amount).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString().replace(/\./g, ',')
        };

    $('.feature')
    // Calculate current values
        .each(function() {
            // If the feature is checked, it is active: sum it to the amount
            if ($(this).is(':checked')) {
                amount += $(this).data('amount');
            }
        })
        .change(function() {
            // Calculate the unatantum instant payment only if the feature is not currently active
            if (typeof $(this).data('already-active') === 'undefined') {
                if ($(this).is(':checked')) {
                    instantAmount += $(this).data('instant-amount');
                } else {
                    instantAmount -= $(this).data('instant-amount');

                    if (0 > instantAmount) {
                        instantAmount = 0;
                    }
                }

                $('.instantAmount').text(currencyMask(instantAmount));
            }

            if ($(this).is(':checked')) {
                amount += $(this).data('amount');
            } else {
                amount -= $(this).data('amount');

                if (0 > amount) {
                    amount = 0;
                }
            }

            $('.amount').text(currencyMask(amount));
        });

    $('.amount').text(currencyMask(amount));
    $('.instantAmount').text(currencyMask(instantAmount));

    // If a feature is activated via querystring, activate it
    $('.activate').find('input').prop('checked', true).trigger('change');
});
