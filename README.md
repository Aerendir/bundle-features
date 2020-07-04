<p align="center">
    <a href="http://www.serendipityhq.com" target="_blank">
        <img src="http://www.serendipityhq.com/assets/open-source-projects/Logo-SerendipityHQ-Icon-Text-Purple.png">
    </a>
</p>

SHQ FEATURES BUNDLE
===================

[![PHP from Packagist](https://img.shields.io/packagist/php-v/serendipity_hq/features-bundle?color=%238892BF)](https://packagist.org/packages/serendipity_hq/features-bundle)
[![Latest Stable Version](https://poser.pugx.org/serendipity_hq/features-bundle/v/stable.png)](https://packagist.org/packages/serendipity_hq/features-bundle)
[![Total Downloads](https://poser.pugx.org/serendipity_hq/features-bundle/downloads.svg)](https://packagist.org/packages/serendipity_hq/features-bundle)
[![License](https://poser.pugx.org/serendipity_hq/features-bundle/license.svg)](https://packagist.org/packages/serendipity_hq/features-bundle)

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-features&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-features)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8805ebe7-6fa3-42a8-b514-f1e7469bc2ca/mini.png)](https://insight.sensiolabs.com/projects/8805ebe7-6fa3-42a8-b514-f1e7469bc2ca)

![Phan](https://github.com/Aerendir/bundle-features/workflows/Phan/badge.svg)
![PHPStan](https://github.com/Aerendir/bundle-features/workflows/PHPStan/badge.svg)
![PSalm](https://github.com/Aerendir/bundle-features/workflows/PSalm/badge.svg)
![PHPUnit](https://github.com/Aerendir/bundle-features/workflows/PHPunit/badge.svg)
![Composer](https://github.com/Aerendir/bundle-features/workflows/Composer/badge.svg)
![PHP CS Fixer](https://github.com/Aerendir/bundle-features/workflows/PHP%20CS%20Fixer/badge.svg)
![Rector](https://github.com/Aerendir/bundle-features/workflows/Rector/badge.svg)

SerendipityHQ Features Bundle helps you to manage features and plans in your Symfony 2 app.

Serendipity HQ Features Bundle gives you the ability to configure the features you need to manage, give them a price if they are premium ones, create invoices for them and save subscriptions and configurations associated to your entities to give each of your users only the features they have to get.

How to use the Serendipity HQ Features Bundle
---------------------------------------------

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

Requirements
------------

1. PHP ^7.1

Status: ACTIVE DEVELOPMENT
--------------------------

This bundle is currently in development mode. We use it in our live projects and so we try to maintain it in good health.

Currently not all Features kinds are implemented, only the ones we currently need and can test on the wild.

It is as stable as possible, also if incomplete. If you, using it, find bugs or scenarios not covered, please, open an issue describing the problem.

All issues are reviewd and fixed.

If you have a feature request, euqally, please, open an issue and we will review it and evaluate if it may be implemented.

If you are able to write your feature by your self, you can open a pull request to integrate it with the main repository. Before doing this, please, open an issue, so we can have a discussion about the creating feature and make it even better! Open source is collaboration, and collaboration is founded on discussion: be aware of this and enjoy the process! :)

Thank you for your collaboration.

DOCUMENTATION
=============

You can read how to install, configure, test and use the SerendipityHQ Features Bundle in the [documentation](src/Resources/docs/Index.md).

Inspiration for future features

- https://github.com/yannickl88/features-bundle
- https://github.com/DZunke/FeatureFlagsBundle

Some other information

- http://stackoverflow.com/questions/7707383/what-is-a-feature-flag
