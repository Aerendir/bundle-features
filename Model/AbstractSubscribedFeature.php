<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * {@inheritdoc}
 */
abstract class AbstractSubscribedFeature extends AbstractFeature implements SubscribedFeatureInterface
{
    /** @var  ConfiguredFeatureInterface $configuredFeature */
    private $configuredFeature;

    /**
     * @return ConfiguredFeatureInterface
     */
    public function getConfiguredFeature() : ConfiguredFeatureInterface
    {
        if (null === $this->configuredFeature) {
            throw new \LogicException('The configured feature of this subscribed feature is not set. Use FeaturesManager::setSubscription to set the correspondent configured feature in each subscribed feature of the subscription.');
        }

        return $this->configuredFeature;
    }

    /**
     * @param ConfiguredFeatureInterface $configuredFeature
     */
    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature)
    {
        $this->configuredFeature = $configuredFeature;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => $this->getType()
        ];
    }
}
