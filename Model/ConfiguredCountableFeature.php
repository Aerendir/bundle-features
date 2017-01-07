<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\PacksProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\RecurringPricesProperty;

/**
 * {@inheritdoc}
 */
class ConfiguredCountableFeature extends AbstractFeature implements ConfiguredCountableFeatureInterface
{
    use PacksProperty {
        PacksProperty::setPacks as setPacksProperty;
    }
    use RecurringPricesProperty {
        RecurringPricesProperty::__construct as RecurringConstruct;
        RecurringPricesProperty::setSubscription as setRecurringSubscription;
    }
    use CanBeFreeProperty;

    private $freeAmount;

    /**
     * @var
     */
    private $packs;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        $this->freeAmount = $details['free_amount'] ?? 0;

        // Set the type
        $details['type'] = self::COUNTABLE;

        if (isset($details['packs']))
            $this->setPacks($details['packs']);

        $this->RecurringConstruct($details);

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function getFreeAmount() : int
    {
        return $this->freeAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs, string $class = null) : HasPacksInterface
    {
        return $this->setPacksProperty($packs, ConfiguredCountableFeaturePack::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscription(SubscriptionInterface $subscription): HasRecurringPricesInterface
    {
        $this->setRecurringSubscription($subscription);

        // If there are packs, set subscription in them, too
        if (false === empty($this->packs)) {
            /** @var ConfiguredCountableFeaturePack $pack */
            foreach ($this->packs as $pack) {
                $pack->setSubscription($subscription);
            }
        }

        return $this;
    }
}
