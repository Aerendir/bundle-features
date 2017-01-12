<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesProperty;

/**
 * {@inheritdoc}
 */
class ConfiguredCountableFeature extends AbstractFeature implements ConfiguredCountableFeatureInterface
{
    use HasConfiguredPacksProperty {
        HasConfiguredPacksProperty::setPacks as setPacksProperty;
    }
    use HasRecurringPricesProperty {
        HasRecurringPricesProperty::__construct as RecurringConstruct;
        HasRecurringPricesProperty::setSubscription as setRecurringSubscription;
    }
    use CanBeFreeProperty;

    /**
     * @var
     */
    private $packs;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
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
    public function setPacks(array $packs, string $class = null) : HasConfiguredPacksInterface
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
