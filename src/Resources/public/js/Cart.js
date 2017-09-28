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
/* eccolo */
$(document).ready(function() {
    var grossAmount = 0,
        netAmount = 0,
        grossInstantAmount = 0,
        netInstantAmount = 0,
        currencyMask = function (amount) {
            return parseFloat(amount).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString().replace(/\./g, ',')
        },
        markOptionAsCurrentlySelected = function(option) {
            option.addClass('currently-selected');
        },
        unmarkOptionAsCurrentlySelected = function(option) {
            option.removeClass('currently-selected');
        };

    $('[data-feature="boolean"]')
    // Calculate current values
        .each(function() {
            // If the feature is checked, it is active: sum it to the amount
            if ($(this).is(':checked')) {
                grossAmount += $(this).data('gross-amount');
                netAmount += $(this).data('net-amount');
            }
        })
        .change(function() {
            // Calculate the unatantum instant payment only if the feature is not currently active
            if (typeof $(this).data('already-active') === 'undefined') {
                if ($(this).is(':checked')) {
                    grossInstantAmount += $(this).data('gross-instant-amount');
                    netInstantAmount += $(this).data('net-instant-amount');
                } else {
                    grossInstantAmount -= $(this).data('gross-instant-amount');
                    netInstantAmount -= $(this).data('net-instant-amount');

                    if (0 > grossInstantAmount) {
                        grossInstantAmount = 0;
                        netInstantAmount = 0;
                    }
                }

                $('.total-gross-instant-amount').text(currencyMask(grossInstantAmount));
                $('.total-net-instant-amount').text(currencyMask(netInstantAmount));
            }

            if ($(this).is(':checked')) {
                grossAmount += $(this).data('gross-amount');
                netAmount += $(this).data('net-amount');
            } else {
                grossAmount -= $(this).data('gross-amount');
                netAmount -= $(this).data('net-amount');

                if (0 > grossAmount) {
                    grossAmount = 0;
                    netAmount = 0;
                }
            }

            $('.total-gross-amount').text(currencyMask(grossAmount));
            $('.total-net-amount').text(currencyMask(netAmount));
            $('.total-gross-instant-amount').text(currencyMask(grossInstantAmount));
            $('.total-net-instant-amount').text(currencyMask(netInstantAmount));
        });

    $('[data-feature="countable"]')
    // Find countable features
        .each(function() {
            featureName = 'feature-' + $(this).data('name');

            // Find the subscribed pack (is an option in the select)
            selected = $(this).find("option[selected='selected']");
            markOptionAsCurrentlySelected(selected);

            if (typeof selected.data('gross-amount') !== 'undefined') {
                // And add their price to the amount
                grossAmount += selected.data('gross-amount');
                netAmount += selected.data('net-amount');

                // Write the price and the instant price in the front-end
                $('.feature.feature-details.' + featureName + ' .feature-gross-amount').text(currencyMask(selected.data('gross-amount')));
                $('.feature.feature-details.' + featureName + ' .feature-gross-instant-amount').text(currencyMask(selected.data('gross-instant-amount')));
                $('.feature.feature-details.' + featureName + ' .feature-net-amount').text(currencyMask(selected.data('net-amount')));
                $('.feature.feature-details.' + featureName + ' .feature-net-instant-amount').text(currencyMask(selected.data('net-instant-amount')));
            }
        })
        .change(function() {
            featureName = 'feature-' + $(this).data('name');
            // Get the new selected option and the unselected one
            selected = $(this).find(':selected');
            deselected = $(this).find('.currently-selected');

            // Update the prices shown for the current selected option/pack
            if (typeof selected.data('gross-amount') !== 'undefined') {
                // Write the price and the instant price in the front-end
                $('.feature.feature-details.' + featureName + ' .feature-gross-amount').text(currencyMask(selected.data('gross-amount')));
                $('.feature.feature-details.' + featureName + ' .feature-gross-instant-amount').text(currencyMask(selected.data('gross-instant-amount')));
                $('.feature.feature-details.' + featureName + ' .feature-net-amount').text(currencyMask(selected.data('net-amount')));
                $('.feature.feature-details.' + featureName + ' .feature-net-instant-amount').text(currencyMask(selected.data('net-instant-amount')));
            }

            // Subtract the amount of the deselected option and add the amount of the newly selected option
            grossAmount -= deselected.data('gross-amount');
            grossAmount += selected.data('gross-amount');
            netAmount -= deselected.data('net-amount');
            netAmount += selected.data('net-amount');

            // Subtract the instant amount only if the deselcted pack is not the one already subscribed
            if (typeof deselected.data('already-subscribed') === 'undefined') {
                grossInstantAmount -= deselected.data('gross-instant-amount');
                netInstantAmount -= deselected.data('net-instant-amount');
            }

            // Add the new instant amount only if the selected pack is not the one already subscribed
            if (typeof selected.data('already-subscribed') === 'undefined') {
                grossInstantAmount += selected.data('gross-instant-amount');
                netInstantAmount += selected.data('net-instant-amount');
            }

            // Update the totals
            $('.total-gross-instant-amount').text(currencyMask(grossInstantAmount));
            $('.total-net-instant-amount').text(currencyMask(netInstantAmount));
            $('.total-gross-amount').text(currencyMask(grossAmount));
            $('.total-net-amount').text(currencyMask(netAmount));

            // Switch de/selected options
            unmarkOptionAsCurrentlySelected(deselected);
            markOptionAsCurrentlySelected(selected);
        });

    $('[data-feature="rechargeable"]')
    // Find rechargeable features
        .each(function() {
            featureName = 'feature-' + $(this).data('name');

            // Find the subscribed pack (is an option in the select)
            selected = $(this).find("option[selected='selected']");
            if (0 === selected.length) {
                selected = $(this).find("option").first();
            }
            markOptionAsCurrentlySelected(selected);

            // Write the price and the instant price in the front-end
            $('.feature.feature-details.' + featureName + ' .feature-gross-instant-amount').text(currencyMask(selected.data('gross-instant-amount')));
            $('.feature.feature-details.' + featureName + ' .feature-net-instant-amount').text(currencyMask(selected.data('net-instant-amount')));
        })
        .change(function() {
            featureName = 'feature-' + $(this).data('name');
            // Get the new selected option and the unselected one
            selected = $(this).find(':selected');
            deselected = $(this).find('.currently-selected');

            // Update the prices shown for the current selected option/pack
            if (typeof selected.data('gross-instant-amount') !== 'undefined') {
                // Write the price and the instant price in the front-end
                $('.feature.feature-details.' + featureName + ' .feature-gross-instant-amount').text(currencyMask(selected.data('gross-instant-amount')));
                $('.feature.feature-details.' + featureName + ' .feature-net-instant-amount').text(currencyMask(selected.data('net-instant-amount')));
            }

            // Subtract the instant amount only if the deselcted pack is not the one already subscribed
            grossInstantAmount -= deselected.data('gross-instant-amount');
            netInstantAmount -= deselected.data('net-instant-amount');
            grossInstantAmount += selected.data('gross-instant-amount');
            netInstantAmount += selected.data('net-instant-amount');

            // Update the totals
            $('.total-gross-instant-amount').text(currencyMask(grossInstantAmount));
            $('.total-net-instant-amount').text(currencyMask(netInstantAmount));

            // Switch de/selected options
            unmarkOptionAsCurrentlySelected(deselected);
            markOptionAsCurrentlySelected(selected);
        });

    $('.total-gross-amount').text(currencyMask(grossAmount));
    $('.total-gross-instant-amount').text(currencyMask(grossInstantAmount));
    $('.total-net-amount').text(currencyMask(netAmount));
    $('.total-net-instant-amount').text(currencyMask(netInstantAmount));

    // If a feature is activated via querystring, activate it being sure it not already checked.
    // Maybe the form were reloaded due to an error: in this case the total would count two times the feature.
    // This if prevents the doubled counting.
    if (false === $('.activate').find('input').is(':checked')) {
        $('.activate').find('input').prop('checked', true).trigger('change');
    }
});
