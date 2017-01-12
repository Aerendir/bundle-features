DIFFERENCES BETWEEN COUNTABLE AND RECHARGEABLE FEATURES
=======================================================

At a first look, `CountableFeature` and `RechargeableFeature` may appear very similar if not equals.

Here are highlighted the main differences between the two types of feature.

Prices
------

The first difference is about pricing.

In fact, while `CountableFeature` has recurring prices (See [`HasRecurringPricesInterface`](../../../Property/HasRecurringPricesInterface.php)), the `RechargeableFeature` has unatantum prices (See [`HasUntantumPricesInterface`](../../../Property/HasUntantumPricesInterface.php)).

This difference is reflected in the configuration needed to set prices, as the `CountableFeature` requeres that the subscription period is set, while the `RechargeableFeature` requires a simple price:

```
...
    a_countable_feature:
        type: countable
        unitary_price:
            # Currency code in ISO 4217 format
            EUR:
                # Subscription periods
                monthly: 1000
                yearly: 10000
        packs:
            # The num of units: empty for a free feature
            50:
                # Optional. Currency code in ISO 4217 format. If not set the pack is free
                EUR:
                    # Subscription periods: The price in the base currency units
                    monthly: 500
                    yearly: 5000
...
    a_rechargeable_feature:
        type: rechargeable
        # Required: The price of a single unit of this feature (ex.: the cost of one more user)
        unitary_price:
            # Currency code in ISO 4217 format: The price in the base currency units
            EUR: 100
        # Optional: if not set the feature is free
        packs:
            50:
                # Optional. Currency code in ISO 4217 format. If not set the pack is free
                EUR: 500

```

This is because the `RechargeableFeature` is meant to be bought each time the User requires it, while the `CountableFeature` is subscribed by the user and on each subscription period interval it is renewed.

`free_recharge` and FreePack
----------------------------

While it is possible to set in the default subscription the initial quantity of a `RechargeableFeature` units, this is not possible in for a `CountableFeature`.

But for a `CountableFeature` is possible to set a FreePack while this is not possible for a `RechargeableFeature`.
 
This is because the logic of the two features is different.

A `RechargeableFeature` can be bought in pack but those packs are not subscribable. So, if you want to give some initial free amount of units to your users, you have necessarily set the `free_recharge` property.

If you set the `free_recharge` configuration of a `RechargeableFeature` when the default plan is built, this quantity is set as the remained quantity of units.

Conversely, a `CountableFeature` has packs that can be subscribed as they are renewed each time the subscription period ends.

So, if you want to give some free units to your users, you have to set a package with no prices that will become the default subscribed one when the default subscription is created.

Cumulability
------------

Cumulability is not settable for `RechargeableFeatures` as, by design, when the user buys new quantities of it, the new quantity is added to the existent quantity.

Cumulability, is, instead, settable for `CountableFeatures` as in this case, you, as developer, may want to sum to the still existent quantity the new quantity set by the new subscription cycle.

Unitary price
-------------

Currently `CountableFeature`s don't support `unitary_price` (but there is [an issue opened](https://github.com/Aerendir/bundle-features/issues/1) to support it).
