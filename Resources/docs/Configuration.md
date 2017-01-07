How to configure Serendipity HQ Features Bundle
===============================================

The Serendipity HQ Features Bundle uses configuration files of your application to define the features you can manage and group the in sets.

It is not possible (and not desirable, too) to manage them through a web interface other than the one that the end user uses to use and configure the features for his profile and resources.

The bundle creates a `FeatureManager` and an `InvoiceManager` for each set of features: these are the two services that run all the logic of the bundle. Use them to manage features in your app and create invoices for the premium features your users can buy.

STEP 3: CONFIGURE SERENDIPITYHQ FEATURES BUNDLE
-----------------------------------------------

As we use this bundle in our own applications, we are going to explaing the configuration taking as example [TrustBack.Me](//trustback.me), the first of our apps that used this bundle.

TrustBack.Me permits to merchants to get feedbacks from their own customers and shows them on a public profile page.

Each Merchant has a Company and each Company has one or more Stores (the self-hosted e-commerce site, the eBay store, the Amazon store, ecc.).

Lets go to analyze the features (not all of them, but only the ones required to illustrate how SerendipityHQ Features Bundle works).
 
- The merchant can buy the invites at Company level, so the Stores of each Company use the invites bought by the merchant for the whole Company.
- Each Store Profile can be boosted:
    - Removing Ads
    - Activating SEO functionalities
    - Activating Social functionalities

How can we represent these features so they can be managed by SerendipityHQ Features Bundle?

Here is our configuration:

```yaml
features:
    # Premium Features for Company
    company: # <-- The name of this features set is "company"
        features:
            reminders:
                type: rechargeable
                # The amount to recharge each time
                free_recharge: 10
                unitary_prices:
                    EUR: 100
                packs:
                    10:
                        EUR: 1000
#                    50:
#                        EUR: 5000
#                    100:
#                        EUR: 10000
#                    500:
#                        EUR: 50000
#                    1000:
#                        EUR: 100000

    # Premium Features for Store
    # If enabled, the feature is free as "enabled by default"
    store: # <-- The name of this features set is "store"
        features:
            ads:
                type: boolean
                # This feature is enabled by default but the Merchant can disable it anyway from the subscription page
                enabled: true
                # No price is specified: the feature is free
            seo:
                type: boolean
                enabled: false
                prices:
                    EUR:
                        monthly: 500
                        yearly: 5000
            social:
                type: boolean
                enabled: false
                prices:
                    EUR:
                        monthly: 500
                        yearly: 5000
```

Don't be afraid: we don't sell our features for thousands of euros! :) We sell them for dozens!

Then, why are we using thousands? Because internally Serendipity HQ Features Bundle uses a Money Value Object: those numbers represents the the base units of euros that are cents.

So, `500` represents FIVE Euros while `5000` represents FIFTY Euros.

We use a money value object internally as this is the correct way of managing monetary values with PHP. [If you are using `float`s you are doing it wrong!](https://github.com/moneyphp/money)

You can read more about Value Objects [here](https://github.com/Aerendir/PHPValueObjects).

So, once we have explained this strange numbers, the rest could be simpler: each feature has its own name (`ads` and `seo` for Stores and `reminders` for Companies).

Then we specify the `type` that can be `boolean`, `rechargeable` or `countable` (NOTE: only `boolean` features are implemented at this stage of development but the remaining type will be implemented soon!).

As currently only `boolean` features are supported, we continue speaking only about them.

So, `enabled` defines if the feature has to be enabled by default or not when the default configuration or subscription is created (more about this soon).

Finally we specify `prices`: so we specify one for each currency we are going to support (TrustBack.Me supports only Euros for the moment but the bundle already supports all existing currencies thanks to the [SerendipityHQ PHPValueObjects library](https://github.com/Aerendir/PHPValueObjects)) using the [ISO 4217 standard](https://en.wikipedia.org/wiki/ISO_4217).

For each currency we specify an interval that can be `monthly` or `yearly` (the only two intervals currently supporte: more are planned to be implemented).

Done: the configuration is finished.

Now you can access four services to manage your features and the invoices for the premium features:

- `shq_features.manager.store.features`;
- `shq_features.manager.store.invoices`;
- `shq_features.manager.company.features`;
- `shq_features.manager.company.features`;

Continue reading the documentation to know how to use them or read the specifications for each type of feature:

- [Boolean Feature config details](Specification-Boolean.md)
- [Countable Feature config details](Specification-Countable.md)
- [Rechargeable Feature config details](Specification-Rechargeable.md)

([Go back to index](Index.md)) | Next step: To write
