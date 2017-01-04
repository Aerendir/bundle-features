<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * A feature loaded from the Configuration.
 */
interface ConfiguredFeatureInterface extends FeatureInterface
{
}
