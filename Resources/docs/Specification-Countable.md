COUNTABLE FEATURE SPECIFICATION
===============================

This is the full configuration of a Countable feature:

```
...
    a_countable_feature:
        type: countable
        # If true, on each subscription cycle the num of units is summed up to the already existent num of units
        cumulable: false
        # The amount of free units. In this case, the user start paying additional units from the fourth one.
        free_amount: 3
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
