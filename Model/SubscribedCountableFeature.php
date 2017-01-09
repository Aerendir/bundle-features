<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasQuantitiesProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
class SubscribedCountableFeature extends AbstractSubscribedFeature implements SubscribedCountableFeatureInterface
{
    use HasRecurringFeatureProperty {
        HasRecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }

    /**
     * @todo but here the initial quantity is not required as there is ever a subscribed pack to which reference to get
     * the available quantity in the subscription period.
     */
    use HasQuantitiesProperty;

    /** @var  int $previousRemainedQuantity Internal variable used when cumulate() is called */
    private $previousRemainedQuantity;

    /** @var  int $subscribedPack */
    private $subscribedPack;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::COUNTABLE;

        $this->RecurringFeatureConstruct($details);
        $this->setQuanity($details);

        if (isset($details['subscribed_pack']))
            $this->subscribedPack = $details['subscribed_pack'];

        parent::__construct($name, $details);
    }

    /**
     * @return int
     */
    public function getSubscribedPack()
    {
        return $this->subscribedPack;
    }

    /**
     * Transforms the $subscribedPack integer into the correspondent ConfiguredFeaturePackInterface object.
     *
     * {@inheritdoc}
     */
    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature)
    {
        /** @var ConfiguredCountableFeatureInterface $configuredFeature */
        $configuredPack = $configuredFeature->getPack($this->subscribedPack);
        $this->subscribedPack = $configuredPack;

        parent::setConfiguredFeature($configuredFeature);
    }

    /**
     * Adds the new recharge amount to the already existent quantity.
     *
     * So, if the current quantity is 4 and a recharge(5) is made, the new $remainedQuantity is 5.
     * But if cumulate() is called, the new $remainedQuantity is 9:
     *
     *     ($previousRemainedQuantity = 4) + ($rechargeQuantity = 5).
     *
     * @return SubscribedCountableFeatureInterface
     */
    public function cumulate() : SubscribedCountableFeatureInterface
    {
        $this->remainedQuantity += $this->previousRemainedQuantity;

        return $this;
    }

    /**
     * @todo method to implement
     */
    public function updatePreviousRemainedQuantity()
    {
        $this->previousRemainedQuantity = $this->remainedQuantity;
        //$this->remainedQuantity = $rechargeQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $subscribedPack = $this->getSubscribedPack();

        // If it is an object, transofmr it
        if ($subscribedPack instanceof ConfiguredCountableFeaturePack) {
            $subscribedPack = $subscribedPack->getNumOfUnits();
        }

        return array_merge([
            'active_until' => json_decode(json_encode($this->getActiveUntil()), true),
            'subscribed_pack' => $subscribedPack
        ], parent::toArray());
    }
}
