<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRecurringFeatureInterface;

/**
 * Manages properties of a Recurring feature.
 */
trait HasRecurringFeatureProperty
{
    /** @var \DateTime $activeUntil */
    private $activeUntil;

    /**
     * @param array $details
     */
    public function __construct(array $details = [])
    {
        if (isset($details['active_until'])) {
            $this->activeUntil = $details['active_until'] instanceof \DateTime ? $details['active_until'] : new \DateTime($details['active_until']['date'], new \DateTimeZone($details['active_until']['timezone']));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveUntil()
    {
        return $this->activeUntil;
    }

    /**
     * {@inheritdoc}
     */
    public function isStillActive() : bool
    {
        if (null === $this->getActiveUntil()) {
            return false;
        }

        return $this->getActiveUntil() >= new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveUntil(\DateTime $activeUntil) : SubscribedRecurringFeatureInterface
    {
        $this->activeUntil = $activeUntil;

        /** @var SubscribedRecurringFeatureInterface $this */
        return $this;
    }
}
