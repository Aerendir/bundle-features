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

    use HasQuantitiesProperty;

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
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge([
            'active_until' => json_decode(json_encode($this->getActiveUntil()), true),
            'initial_quantity' => $this->getInitialQuantity(),
            'remained_quantity' => $this->getRemainedQuantity(),
            'subscribed_pack' => $this->getSubscribedPack()
        ], parent::toArray());
    }
}
