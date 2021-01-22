<p align="center">
    <a href="http://www.serendipityhq.com" target="_blank">
        <img style="max-width: 350px" src="http://www.serendipityhq.com/assets/open-source-projects/Logo-SerendipityHQ-Icon-Text-Purple.png">
    </a>
</p>

<h1 align="center">Serendipity HQ Features Bundle</h1>
<p align="center">Features Bundle helps you manage paid features and plans in your Symfony app.</p>
<p align="center">
    <a href="https://github.com/Aerendir/bundle-features/releases"><img src="https://img.shields.io/packagist/v/serendipity_hq/bundle-features.svg?style=flat-square"></a>
    <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
    <a href="https://github.com/Aerendir/bundle-features/releases"><img src="https://img.shields.io/packagist/php-v/serendipity_hq/bundle-features?color=%238892BF&style=flat-square&logo=php" /></a>
    <a title="Tested with Symfony ^4.4" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Tested with Symfony ^4.4" src="https://img.shields.io/badge/Symfony-%5E4.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Tested with Symfony ^5.2" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Tested with Symfony ^5.2" src="https://img.shields.io/badge/Symfony-%5E5.2-333?style=flat-square&logo=symfony" /></a>
</p>
<p align="center">
    <a href="https://www.php.net/manual/en/book.iconv.php"><img src="https://img.shields.io/badge/Suggests-ext--iconv-%238892BF?style=flat-square&logo=php"></a>
    <a href="https://www.php.net/manual/en/book.intl.php"><img src="https://img.shields.io/badge/Suggests-ext--intl-%238892BF?style=flat-square&logo=php"></a>
    <a href="https://www.php.net/manual/en/book.json.php"><img src="https://img.shields.io/badge/Suggests-ext--json-%238892BF?style=flat-square&logo=php"></a>
    <img src="https://img.shields.io/badge/Suggests-serendipity__hq/component--text--matrix-%238892BF?style=flat-square">
</p>

## Current Status

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)

[![Phan](https://github.com/Aerendir/bundle-features/workflows/Phan/badge.svg)](https://github.com/Aerendir/bundle-features/actions?query=branch%3Adev)
[![PHPStan](https://github.com/Aerendir/bundle-features/workflows/PHPStan/badge.svg)](https://github.com/Aerendir/bundle-features/actions?query=branch%3Adev)
[![PSalm](https://github.com/Aerendir/bundle-features/workflows/PSalm/badge.svg)](https://github.com/Aerendir/bundle-features/actions?query=branch%3Adev)
[![PHPUnit](https://github.com/Aerendir/bundle-features/workflows/PHPunit/badge.svg)](https://github.com/Aerendir/bundle-features/actions?query=branch%3Adev)
[![Composer](https://github.com/Aerendir/bundle-features/workflows/Composer/badge.svg)](https://github.com/Aerendir/bundle-features/actions?query=branch%3Adev)
[![PHP CS Fixer](https://github.com/Aerendir/bundle-features/workflows/PHP%20CS%20Fixer/badge.svg)](https://github.com/Aerendir/bundle-features/actions?query=branch%3Adev)
[![Rector](https://github.com/Aerendir/bundle-features/workflows/Rector/badge.svg)](https://github.com/Aerendir/bundle-features/actions?query=branch%3Adev)

## Features

Serendipity HQ Features Bundle gives you the ability to configure the features you need to manage, give them a price if they are premium ones, create invoices for them and save subscriptions and configurations associated to your entities to give each of your users only the features they have to get.

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

## Basic usage

SerendipityHQ Features Bundle divides the features in three macro-categories:

- Boolean features: are features or configurations that can be switched on or off. For example, "send an e-mail on [event_name]".
- Countable Features: are features that can be increased or decreased in number. For example, the number of users that can be added.
- Rechargeable Features: Are features of which it is possible to run out. For example, "You have X invites left".

These are the three main categories in which all kind of features or configurations can fall in (if you find other macro categories, feel free to suggest them in the issues!).

These features can be configured in your configuration file and then can be managed through your app to make it able to do some things or not to do things.

Features are grouped in sets, this way you can better manage your features without going crazy and without be confused.

For example, lets say you are building a project management app, you may want to have a set of features for the profile of each user and a set of features specific for projects.

Grouping features in sets you can manage them in an easier way.

Those are the very base concepts to understand and are the core of the internal working of the Serendipity HQ Features Bundle.

Read more in the documentation to better understand how all this work toward your feature management in your app.

## Install Serendipity HQ Features Bundle via Composer

    $ composer require serendipity_hq/bundle-features

This library follows the http://semver.org/ versioning conventions.

## Documentation

You can read how to install, configure, test and use the SerendipityHQ Features Bundle in the [documentation](docs/Index.md).

Inspiration for future features

- https://github.com/yannickl88/features-bundle
- https://github.com/DZunke/FeatureFlagsBundle

Some other information

- http://stackoverflow.com/questions/7707383/what-is-a-feature-flag

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
