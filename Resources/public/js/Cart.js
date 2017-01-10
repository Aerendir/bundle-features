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
        },
        markOptionAsCurrentlySelected = function(option) {
            option.addClass('currently-selected');
        },
        unmarkOptionAsCurrentlySelected = function(option) {
            option.removeClass('currently-selected');
        };

    $('.feature.feature-boolean')
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

    $('.feature.feature-countable')
    // Find countable features
        .each(function() {
            // Find the subscribed pack (is an option in the select)
            selected = $(this).find("option[selected='selected']");
            markOptionAsCurrentlySelected(selected);

            if (typeof selected.data('amount') !== 'undefined') {
                // And add their price to the amount
                amount += selected.data('amount');

                // Write the price and the instant price in the front-end
                $('.feature.feature-countable.feature-details.feature-amount').text(currencyMask(selected.data('amount')));
                $('.feature.feature-countable.feature-details.feature-instant-amount').text(currencyMask(selected.data('instant-amount')));
            }
        })
        .change(function() {
            // Get the new selected option and the unselected one
            selected = $(this).find(':selected');
            deselected = $(this).find('.currently-selected');

            // Update the prices shown for the current selected option/pack
            if (typeof selected.data('amount') !== 'undefined') {
                // Write the price and the instant price in the front-end
                $('.feature.feature-countable.feature-details.feature-amount').text(currencyMask(selected.data('amount')));
                $('.feature.feature-countable.feature-details.feature-instant-amount').text(currencyMask(selected.data('instant-amount')));
            }

            // Subtract the amount of the deselected option and add the amount of the newly selected option
            amount -= deselected.data('amount');
            amount += selected.data('amount');

            // Subtract the instant amount only if the deselcted pack is not the one already subscribed
            if (typeof deselected.data('already-subscribed') === 'undefined') {
                instantAmount -= deselected.data('instant-amount');
            }

            // Add the new instant amount only if the selected pack is not the one already subscribed
            if (typeof selected.data('already-subscribed') === 'undefined') {
                instantAmount += selected.data('instant-amount');
            }

            // Update the totals
            $('.instantAmount').text(currencyMask(instantAmount));
            $('.amount').text(currencyMask(amount));

            // Switch de/selected options
            unmarkOptionAsCurrentlySelected(deselected);
            markOptionAsCurrentlySelected(selected);
        });

    $('.amount').text(currencyMask(amount));
    $('.instantAmount').text(currencyMask(instantAmount));

    // If a feature is activated via querystring, activate it
    $('.activate').find('input').prop('checked', true).trigger('change');
});
