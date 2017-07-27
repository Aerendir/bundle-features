RECHARGEABLE FEATURE SPECIFICATION
==================================

This is the full configuration of a Rechargeable feature:

```
...
    a_rechargeable_feature:
        type: rechargeable
        # If true, each time the feature is bought, the num of units is summed up to the already existent num of units
        cumulable: false
        # The number of units to give in the default subscription
        free_recharge: 10
        # Required: The price of a single unit of this feature (ex.: the cost of one more user)
        unitary_price:
            # Currency code in ISO 4217 format: The price in the base currency units
            EUR: 100
        # Optional: if not set the feature is free
        packs:
            50:
                # Optional. Currency code in ISO 4217 format. If not set the pack is free
                EUR: 500
            100:
                EUR: 1000
            500:
                EUR: 5000
            1000:
                EUR: 50000

```
