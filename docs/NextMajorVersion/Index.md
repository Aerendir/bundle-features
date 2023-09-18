Features Component and Bundle
=============================

This is the "planing" of the new version of Features.

Features will be a standalone component, accompanied by a bundle to easily integrate it in Symfony apps.

What features will have Features ("oh, this is so incestous!")
--------------------------------------------------------------

- Will permit to create Countable Features, Toggeable Features (the ones that now are Boolean Features) and Rechargeable Features
- Features gating
- Subscription management

Starting point
--------------

The starting point is the difference between free features and premium features.

Free features are the ones that are managed by Features mainly with the Gate.

The use of the Gate permits to decide if a feature should be accessible or not.

Premium features, other than the gating, decide on the accessibility also in dependence of the subscription.

Basic usage
-----------

Scenario: an image uploading app

The app creates an account and a public profile for each one of its users and these are the features

- You can upload images
- You vote uploaded images
- You can leave a comment under the images

So, we can describe our feature set this way
- upload_images
- vote_images
- comment_images

But we have a constraint: in fact, only some users can comment: we need to check their sentiment and to fine grain our algorithm we need some research.

So, we are not ready to make anyone able to comment, but only some, trusted users.

### Creating a `Guard`

To manage this scenario we need a `Guard`.

A `Guard` guards a gate: the `Guard` shows red (`false`) or green (`true`) `Flag`s to grant or deny the passage of the gate.

So, each `Feature` requires some green `Flag` to be used.

```php
$imagesSet = new FeaturesSet('images');
$imagesSet
    ->addFeature(new Feature('vote_images', [new QueryStringFlag('secret', '4110w-M3')]))
    ->addFeature(new Feature('comment_images', [new IpFlag('123.456.789.123')]));

$imagesGuard = new Guard($imagesSet);

if ($imagesGuard->grants('vote_images')) {
    // Code to permit the vote of images
}
```

As you can see, we didn't set any feature for `upload_images`: this is not a feature that can be enabled or disabled and we don't need to check anything: anyone can upload images (in this current implementation ;)).

Each `FeatureSet` has its own `Guard`.

When you have many `FeatureSet`s, you have many corresponding `Sentiry`ies: in this case, you can create a `Gate`:

```php
$imagesGuard = ...
$themesGuard = ...

$gate = new Gate();
$gate->addGuard($imagesGuard);
$gate->addGuard($themesGuard);
```

Then you can use the `Gate` this way:

````php
$gate->opened('vote_images');

$imagesGuard = $gate->getGuardOf('images');
$imagesGuard->grants('vote_images');
````

Out of the box, `Features` provides many

- `IpFlag`: checks the IP
- `QueryStringFlag`: checks the query string
- `EnvironmentFlag`: checks an environment variable
- `DateTimeFlag`: checks the current date/time
- `CookieFlag`: checks a cookie
- `SubscriptionFlag`: checks the subscription of the given object
- Custom flags that implement `FlagInterface`

Flags available in FeaturesBundle
- `UserRoleFlag`: checks the user state
- `DoctrineFlag` (?)

Advanced usage
--------------

The development of our app is going very well and now we want to implement premium features.

The app creates an account and a public profile for each one of its users and these are the features:

- You can activate or deactivate ads on the public profile
- You have an upload limit of 3GB/month in the free version, but paying you can increase this limit
- You have an upload limit of 30 images/month in the free version, but paying you can increase this limit
- You have a total storage of 10GB in the free version, but paying you can increase this limit
- You can upload 100 images in total in the free version, but paying you can increase this limit

So, we can describe our feature set this way:

- public_profile_ads
- upload_bandwidth
- upload_images
- total_storage
- total_images

We have also other specifics:

- If you don't consume all the 3GB one month, the remaining band with will be cumulated the next month (the second month you will have 3GB + the remaining bandwidth of the first month)
- You can also ask other users for a feedback on the images you upload, sending them one or more reminders: you have 100 reminders/month in the free version, but paying you can increase this number
- You can also buy reminders in bulk so, if you reached your monthly limit, you can anyway send reminders consuming the ones you bought in bulk: you have 10 addtional bulk reminders for free

And what about prices?

We have two currencies:
- EUR
- USD

We have two renew periods:

- Monthly
- Yearly

We have four plans:

- Free
- Silver
- Gold
- Platinum

We have some packs for some of the features:

- `upload_bandwidth`:
    - You can buy in many denominations (3Gb are free each month):
        - 5 Gb per month
        - 7 Gb per month
        - 10 Gb per month
    - You can pay in two different billing cycles and get some discounts for longer cycles:
        - Monthly
        - Yearly
- `upload_images`:
    - You can buy in many denominations (30 images are free each month):
        - 50 images per month
        - 70 images per month
        - 100 images per month
    - You can pay in two different billing cycles and get some discounts for longer cycles:
        - Monthly
        - Yearly
- `total_storage`:
    - You can buy many sizes (3Gb are free):
        - 5Gb
        - 7Gb
        - 10Gb
    - You can pay in two different billing cycles and get some discounts for longer cycles:
        - Monthly
        - Yearly
- `total_images`:
    - You can buy many packs (30 images are free):
        - 50 images
        - 70 images
        - 100 images
    - You can pay in two different billing cycles and get some discounts for longer cycles:
        - Monthly
        - Yearly

Many things to describe!

### Creating a `Guard`

To describe this scenario we will create a `FeaturesSet`.

*Note: For the name `toggleable`, read this: https://english.stackexchange.com/questions/232774/is-it-togglable-or-toggleable*

```php
// Convert Gb to Kb
$uploadBandwidth = 3 * 1024 * 1024;
$totalStorage = 10 * 1024 * 1024;

$userAccountSet = new FeaturesSet('user_account');
$userAccountSet
    // Activate or deactivate ads on the profile page
    ->addFeature(new ToggleableFeature('public_profile_ads', /* Active by default */ true, /* Price */ new Money(new Currency('EUR'), '500')))
    // How many gigabytes you can upload monthly
    ->addFeature(new RefreshableFeature('upload_bandwidth', $uploadBandwidth, RefreshableFeature::REFRESH_MONTHLY, /* cumulable */ true /* , $price, array $packs */))
    // How many images you can upload monthly
    ->addFeature(new RefreshableFeature('upload_images', 30, RefreshableFeature::REFRESH_MONTHLY/* , $price, array $packs */))
    // How much gigabytes of storage you have
    ->addFeature(new CountableFeature('total_storage', $totalStorage/* , $price, array $packs */))
    // How many images you can upload in total
    ->addFeature(new CountableFeature('total_images', $totalStorage/* , $price, array $packs */));

$userAccountGuard = new Guard($userAccountSet);
```

As you can see, creating premium features is not as easy and it is prone to errors.

For this reason, it is better to use the `FeatureSetBuilder`:

```php
$builder = new FeatureSetBuilder();
$builder
    ->addCurrencies(['EUR', 'USD'])
    ->addBillingCycles('monthly', 'yearly')
    ->addPlans('free', 'silver', 'gold', 'platinum');
$builder
    ->addToggeableFeature('public_profile_ads')
    ->toggleByDefault(true)
    // 5 Euros
    ->setPrice('EUR', '500');

$builder
    ->addRefreshableFeature('upload_bandwidth')
    ->toggleByDefault(true)
    // 5 Euros
    ->setPrice('EUR', '500');
```

When you have many `FeatureSet`s, you have many corresponding `Sentiry`ies: in this case, you can create a `Gate`:

```php
$imagesGuard = ...
$themesGuard = ...
$userAccountGuard = ...

$gate = new Gate();
$gate->addGuard($imagesGuard);
$gate->addGuard($themesGuard);
$gate->addGuard($userAccountGuard);
```

Then you can use the `Gate` this way:

````php
$gate->opened('vote_images', $subject);

$imagesGuard = $gate->getGuardOf('images');
$imagesGuard->grants('vote_images', $subject);
````

As you can see, in this advanced scenario we pass a second argument `$subject`.

This is

# Useful information

- Prorated: use to name the amount to pay to be in pair with the chosen plan. For example, if the current plan has a billing cycle of 30 days, and I upgrade the plan the 21st day of the billing cycle, then the prorated amount is calculated on the remaining 9 days of the billing cycle;
- https://en.wikipedia.org/wiki/Token_bucket

# Other features

- Make possible to set the max amount of times a plan can be downgraded (or upgraded)
- Make possible to subscribe to multiple plans at the same time

# Inspiration for this component

- https://github.com/bestit/flagception-sdk/
- https://github.com/bestit/flagception-bundle
- https://github.com/yannickl88/features-bundle
- https://github.com/DZunke/FeatureFlagsBundle
- https://github.com/intenseprogramming/feature-flag-bundle/blob/master/doc/USAGE.md
- https://www.google.com/search?q=features+bundle+github
- https://github.com/symfony/symfony/pull/51649
