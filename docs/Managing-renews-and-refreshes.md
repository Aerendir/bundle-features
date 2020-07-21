*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this included!*

HOW TO MANAGE RENEWS AND REFRESHES
==================================

Once a Subscription is created is your responsibility to renew and refresh it at regular times.

"Renew" and "Refresh" mean this:

1. "Renew" refers to `Subscription`s and means getting paid for the active premium features on each renew period;
2. "Refresh" refres to [Countable Features](Features/Specification-Countable.md)(both free and paid ones) and means that at the end of the set `refresh_period` they come back to the original available quantity.

As the subscriptions are linked to entities that are tailored to your app, you have to create on your own two commands to renew and refresh `Subscription`s as this procedure may involve some custom activities.

We are going to explain you exactly how you MUST manage the renewal and refreshing processes, explaining how to use the built-in features of Serendipity HQ Features Bundle.

HOW TO REFRESH SUBSCRIPTIONS
----------------------------

Countable Features are the ones that are refreshed at defined time intervals.

The first thing you need is a list of `Subscription`s that need to be refreshed.

As the `Subscription`s are tailored to your entities, you have to [first create a custom repository](https://symfony.com/doc/current/doctrine/repository.html) for your entity and
then implement a method to get all the `Subscription`s that need to be refreshed.

In your just created custom repository, create the following method:

```php
// AppBundle/Repository/YourEntityRepository.php

class YouEntitySubscriptionRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function findNeedsRefresh()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $query        = $queryBuilder->select('s')
            ->from('AppBundle:YourEntitySubscription', 's')
            ->where(
                $queryBuilder->expr()->lte('s.nextRefreshOn', ':now')
            )->setParameter('now', new \DateTime())
            // Uncomment this if you are upgrading from a previous version of FeatureBundle
            // ->orWhere($queryBuilder->expr()->isNull('s.nextRefreshOn'))
            ->orderBy('s.nextRefreshOn', 'ASC')
            ->getQuery();

        return $query->iterate();
    }
}
```

Now lets go to create our command.

```php
// AppBundle/Command/RenewYourEntitySubscriptionCommand.php

class RenewYourEntitySubscriptionCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('yourapp:subscriptions:refresh_your_entity_subscription')
            ->setDescription('Refreshes the subscriptions of your entity.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create the Input/Output writer (composer install serendipity_hq/console-styles-bundle)
        $ioWriter = new SerendipityHQStyle($input, $output);
        $ioWriter->setFormatter(new SerendipityHQOutputFormatter(true));

        $ioWriter->title('Starting to refresh subscriptions.');

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $subscriptionsToRefresh = $entityManager->getRepository('AppBundle:YourEntity')->findNeedRefresh();

        foreach ($subscriptionsToRefresh as $row) {
            /** @var StoreSubscription $subscription */
            $subscription = $row[0];
            $ioWriter->infoLineNoBg(
                sprintf(
                    'Starting to refresh Subscription <success-nobg>%s</success-nobg>.',
                    $subscription->getId()
                )
            );

            // Refresh the subscription
            $this->getContainer()->get('shq_features.manager.store.features')->setSubscription($subscription)->refreshSubscription();

            // This is to not consume too much memory
            // See http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/batch-processing.html#iterating-results
            $entityManager->flush();

            // Detaches all objects from Doctrine to free up memory!
            $entityManager->clear();
        }

        $ioWriter->success('All Subscription were refreshed.');
    }
}

```

Done!

Now you can run `bin/console yourapp:subscriptions:refresh_your_entity_subscription` too refresh all your subscriptions.

The way you make this console command a cronjob is up to you and depends on your server and on your app's architecture.

One way is to use a bundle like [SHQCommandsQueuesBundle](https://github.com/Aerendir/bundle-commands-queues).

Anyway, this is a starting point that you can, obviously, adapt to your concrete situations.

HOW TO RENEWING SUBSCRIPTIONS AND GET PAID
------------------------------------------

To get paid at the end of the renew period, you have to follow the same exact procedure just seen.

You have to create a command to get all the `Subscription`s that needs to be renew.

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
