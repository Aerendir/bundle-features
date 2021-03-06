*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this included!*

COUNTABLE FEATURE SPECIFICATION
===============================

This is the full configuration of a Countable feature:

```
...
    a_countable_feature:
        type: countable
        # If true, on each subscription cycle the num of units is summed up to the already existent num of units
        cumulable: false
        refresh_period: 'monthly'
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

UNDERSTANDING THE COUNTABLE FEATURES
====================================

## THE `refresh_period`
Countable Features are the one that are refreshed at the end of the `refresh_period`.

For example, you may want to give your users 10 reminders a month for free and 50 reminders a month for 5 Euros.

This is exactly what we do at TrustBack.Me.

Another example may be taken from CodeShip.io: it gives you 100 monthly builds for free and unlimited on paid plans.

If you reflect on those scenarios, you'll notice that there are some triky aspects to manage:

1. A Countable Feature MUST be refreshed at defined time intervals;
2. A Countable Feature MUST be refreshed also if the subscribed pack is the free one.

So, the `refresh_period` is not linked in anyway to the period that passes between the payments the user makes to you.

You have to be free to setup a configuration that allows you to give your customers 30 units of a feature each month, but that will be paid annually.

In such case, you need to get a payment each year, but you also need to refresh the number of available units eaach month.

This is the reason behind the separation between the `refresh_period` set for Countable Features and the "payment period" (the time between payments).

To practically understand how to manage the refresh of countable features, read the [Managing Renews and Refreshes](../Managing-renews-and-refreshes.md) help page.

## THE CUMULABILITY

Cumulability is useful when you want the new units be added to the ones remained from the previous refresh period.

On TrustBack.Me we use this strategy for Stores' automatic reminders: the merchant can subscribe to a monthly package of automatic reminders and may happen that when the refresh period ends, the Store still has some automatic reminders.

As "We trust in the trustable ecommerce era", we like to be fair with our customers, so we don't reset the amount of remained automatic reminders on each subscription cycle. Instead, if some other automatic reminders remain at the end of a refresh period, we make them available in the next one, summing them up to the new amount given by the new refresh period.

So, if at the end of the month there are still 4 reminders, we sum them to the ones available for the next month based on the subscribed pack.

This is the purpose of the `cumulable` setting of the `CountableFeature`.

It is optional as there are also scenarios where you don't want this behavior: think at the number of allowed extra users: you for sure don't want the number of allowed users increse with each refresh period, so, in this scenario, you set the `cumulable` setting to `false` (or don't set it at all as, by default, it is already set to false).

WHAT DOES IT HAPPEN WHEN THE USER UPGRADES THE PACKS
----------------------------------------------------

When the user decides to upgrade the subscribed packages of a `CountableFeature` many things may happen.

Lets start with an example: for the feature "a_countable_feature" the user currently has subscribed a pack with 10 units with a monthly subscription period.

He has consumed 3 units of the feature, so there still are 7 units remained. Once the 7 units run out, the feature should be stopped.

But now the User has increased the quantity, subscribing (and paying the instant price) a package with 50 monthly units.

What does it happen now? Should we have to expect the next subscription cycle to make the feature use the new quantity (50)? And if in the mean time the user consumes all the remaining 7 units? In this case there will be a period in which the user has paid an instant amount for a bigger number of units but this increased quantity isn't used.

This is not so fair.

So, in a case like this where the User has increased the number of units, we do this calculation:

- consumedQuantity: <remains the same>
- remainedQuantity: NewNumOfUnits - consumedUnits

So, in our example, now the user has:

- NewNumOfUnits: 50
- ConsumedQuantity: 3
- RemainedQuantity: 50 - 3 = 47

So simple! ... Mmm... not, not so simple!

Because we have the `cumulate` configuration! So RemainedQuantity maybe well be something like 47 or 83 or any number bigger than 50!

And if this is the case, assuming the user had a remainedQuantity of 83, now the remained quantity is only of 47: we have done away about 36 units!

So, if `cumulate: false`, the previous calculation may be good, but if `cumulate: true` we MUST do a different calculation!

It's this:

1) remainedQuantity += consumedQuantity (7 + 3 = 10)
2) remainedQuantity -= oldNumOfUnits (10 - 10 = 0)
3) remainedQuantity += newNumOfUnits (0 + 50 = 50)
4) remainedQuantity -= consumedQuantity (50 - 3 = 47)

The same result, but now taking into account the `cumulate` configuration!

Lets do another try.

Our user subscribed three months ago to the 10 units monthly package, `cumulate: true` and he consumed:

- First month: 3 units (remained: 10 - 3 = 7. Previously remained: 0);
- Second month: 6 units (remained: 10 - 6 = 4 + 7 PreviousRemained = 11);
- Third month: 9 units (remained: 10 - 9 = 1 + 11 PreviousRemained = 12).

So, in this fourth month he has consumed until now 7 units:

- ConsumedQuantity: 7
- RemainedQuantity: previousRemainedQuantity + newNumOfUnits - consumedQuantity (12 + 10 = 22 - 7 = 15)

Now he subscribes to the 50 monthly units pack:

1) remainedQuantity += consumedQuantity (15 + 7 = 22) <-- The remainedQuantity he had at the beginning of the Subscription period!
2) remainedQuantity -= oldNumOfUnits (22 - 10 = 12) <-- The previous remained quantity
3) remainedQuantity += newNumOfUnits (12 + 50 = 62) <-- The new RemainedQuantity with the new subscribed pack
4) remainedQuantity -= consumedQuantity (62 - 7 = 55) <-- Actual RemainedQuantity

Done, again we have taken into account the `cumulate: true` configuration!

WHAT DOES IT HAPPEN WHEN THE USER DOWNGRADES THE PACKS
------------------------------------------------------

Here comes the complexity.

Lets continue with our example.

After 10 days, the User decides to downgrade again to the 10 monthly units pack. In the mean time he consumed 29 more units, so its current quantity are the following:

- newNumberOfUnits: 10
- oldNumberOfUnits: 50
- ConsumedQuantity: 7 + 29 = 36
- RemainedQuantity: 55 - 36 = 19

But now the remained quantity is bigger than the available quantity of the new package (the 10 units package).

So, what happens now? In this case, the User will continue to have the remained 19 units, but at the end of the cycle the amounts will be recalculated.

Lets illustrate the edge case: at the end of the subscription period, the user remains with still 12 units (he consumed other 7 units until the end of the subscription period).

In this case the "cumulate" configuration comes into action again: if `cumulate: true`, then in the new subscription period the user will have:

    remainedQuantity + newNumberOfUnits = 12 + 10 = 22

If `cumulate: false`, instead, the new remainedQuantity will simply be

    remainedQuantity = newNumberOfUnits = 10

<hr />
<h3 align="center">
    <b>Do you like this bundle?</b><br />
    <b><a href="#js-repo-pjax-container">LEAVE A &#9733;</a></b>
</h3>
<p align="center">
    or run<br />
    <code>composer global require symfony/thanks && composer thanks</code><br />
    to say thank you to all libraries you use in your current project, this included!
</p>
<hr />
