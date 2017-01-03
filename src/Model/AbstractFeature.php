<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * {@inheritdoc}
 */
abstract class AbstractFeature implements FeatureInterface
{
    /** @var bool $fromConfiguration This is set to true only if the feature is loaded from a subscription object */
    private $fromConfiguration = false;

    /** @var string $name */
    private $name;

    /** @var string $type */
    private $type;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        $this->name = $name;

        /*
         * This property defines if the feature is loading from the configuration file or from a subscription object.
         *
         * If it is loaded from a subscription object, in fact, some features, like the instant prices, are disabled.
         */
        if (isset($details['from_configuration'])) {
            $this->fromConfiguration = true;
        }

        $this->type = $details['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return array
     */

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => $this->getType()
        ];
    }

    /**
     * True if the feature is loaded from configuration, false if it is loaded from a subscription object.
     *
     * @return bool
     */
    protected function isFromConfiguration() : bool
    {
        return $this->fromConfiguration;
    }
}
