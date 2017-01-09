COUNTABLE FEATURE SPECIFICATION
===============================

This is the full configuration of a Countable feature:

```
...
    a_countable_feature:
        type: countable
        # If true, on each subscription cycle the num of units is summed up to the already existent num of units
        cumulable: false
        # Required: The price of a single unit of this feature (ex.: the cost of one more user)
        unitary_price:
            # Currency code in ISO 4217 format
            EUR:
                # Subscription periods
                monthly: 1000
                yearly: 10000
        # Optional: if not set the feature is free
        packs:
            # The num of units: empty for a free feature
            10: ~
            50:
                # Optional. Currency code in ISO 4217 format. If not set the pack is free
                EUR:
                    # Subscription periods: The price in the base currency units
                    monthly: 500
                    yearly: 5000
            100:
                EUR:
                    monthly: 1000
                    yearly: 10000
            500:
                EUR:
                    monthly: 5000
                    yearly: 50000
            1000:
                EUR:
                    monthly: 50000
                    yearly: 500000

```

Cumulability is useful when you want the new units be added to the ones remained from the previous subscription cycle.

We use this strategy for Stores' automatic reminders on TrustBack.Me: the merchant can subscribe to a monthly package of automatic reminders and may happen that when the subscription cycle ends, the Store still has some automatic reminders.

As "We trust in the trustable ecommerce era", we like to be fair with our customers, so we don't reset the amount of remained automatic reminders on each subscription cycle. Instead, if some other automatic reminders remain at the end of a subscription cycle, we make them available in the next subscription cycle, summing them up to the new amount given by the new subscription cycle.

So, if at the end, there are still 4 reminders, we do make available in the new subscription period 4 remained reminders plus the new ones available as from the pack subscription.

This is the purpose of the `cumulable` setting of the `CountableFeature`.

It is optional as there are also scenarios where you don't want this behavior: think at the number of allowed extra users: you for sure don't want the number of allowed users increse with each subscription cycle, so, in this scenario, you set the `cumulable` setting to `false` (or don't set it at all as, by default, it is already set to false).
