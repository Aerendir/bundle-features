HOW TO MANAGE RENEWS
--------------------

Once a Subscription is created is your responsibility to renew it at regular times.
 
Renewing a subscription means 2 things:

1. Getting paid for the active features;
2. Recharge the rechargeable features (both free and paid ones).

As the subscriptions are linked to entities that are tailored to your app, you have to create on your own a command to
 renew them as this procedure may involve some custom activities.

We are going to explain you exactly how you MUST manage the renewal process explaining you how to use the built-in
 features of Serendipity HQ Features Bundle.

GETTING THE LIST OF SUBSCRIPTIONS TO RENEW
==========================================

The first thing you need is a list of `Subscription`s that need to be renew.

As the `Subscription`s are tailored to your entities, you have to [first create a custom repository](https://symfony.com/doc/current/doctrine/repository.html) for your entity and
then implement a method to get all the `Subscription`s that need to be renew. 

In your just created custom repository, create the following method:

findNeedsSubscri

http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/batch-processing.html#iterating-results
https://stackoverflow.com/a/26698814/1399706
