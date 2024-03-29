<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasRecurringPricesProperty;

/**
 * CountableFeatures can be bought in packs on each subscription period.
 *
 * A Pack represents an amount of units of the ConfiguredCountableFeature with a correspondent price.
 */
final class ConfiguredCountableFeaturePack extends AbstractFeaturePack implements ConfiguredFeaturePackInterface, HasRecurringPricesInterface
{
    use HasRecurringPricesProperty;
    use CanBeFreeProperty;

    public function __construct(int $numOfUnits, array $prices, string $pricesType)
    {
        $this->setPrices($prices, $pricesType);

        parent::__construct(['num_of_units' => $numOfUnits]);
    }
}
