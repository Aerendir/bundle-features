<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Bundle\FeaturesBundle\Traits\SubscriptionTrait;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Subscription implements SubscriptionInterface
{
    use SubscriptionTrait;
}
