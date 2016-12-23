<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesHandler;

class FeaturesCollection extends ArrayCollection
{
    /** @var FeaturesCollection $boolean */
    private $booleans;

    /** @var FeaturesCollection $rechargeables */
    private $rechargeables;

    public function __construct(array $elements = array())
    {
        if (0 < count($elements)) {
            foreach ($elements as $feature => $details) {
                // Required as the Collection can be instantiated by the ArrayCollection::filter() method (see FeaturesHandler)
                if (is_array($details)) {
                    switch ($details['type']) {
                        case FeatureInterface::BOOLEAN:
                            $elements[$feature] = new BooleanFeature($feature, $details);
                            break;

                        case FeatureInterface::RECHARGEABLE:
                            $elements[$feature] = new RechargeableFeature($feature, $details);
                            break;

                        default:
                            throw new \InvalidArgumentException(sprintf('Unknown feature of type "%s".', $details['type']));
                    }
                }
            }
        }

        parent::__construct($elements);
    }
}
