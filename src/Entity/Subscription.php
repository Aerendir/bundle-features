<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Entity;

use SerendipityHQ\Bundle\FeaturesBundle\Traits\SubscriptionTrait;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;

/**
 * {@inheritdoc}
 */
class Subscription implements SubscriptionInterface
{
    use SubscriptionTrait;
}
