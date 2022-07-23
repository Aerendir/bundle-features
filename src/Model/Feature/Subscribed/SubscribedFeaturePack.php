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

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeaturePackInterface;

/**
 * The subscribed pack of the SubscribedCountableFeature.
 */
class SubscribedFeaturePack extends AbstractFeaturePack implements FeaturePackInterface
{
    public function toArray(): array
    {
        return [
            FeaturePackInterface::FIELD_NUM_OF_UNITS => $this->getNumOfUnits(),
        ];
    }
}
