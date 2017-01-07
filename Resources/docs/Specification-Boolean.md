BOOLEAN FEATURE SPECIFICATION
=============================

This is the full configuration of a Boolean feature:

```
...
    a_boolean_feature:
        type: boolean
        # If true, the feature is enabled when the default subscription is created
        enabled: false
        # Optional: if not set the feature is free
        price:
            # Currency code in ISO 4217 format
            EUR:
                # Subscription periods: The price in the base currency units
                monthly: 500
                yearly: 5000

```
