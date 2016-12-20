<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SerendipityHQ\Bundle\FeaturesBundle\Util\FeaturesNavigator;

/**
 * A trait to manage common tasks in a PremiumPlansManager.
 */
trait PremiumPlansManagerTrait
{
    /** @var FeaturesNavigator $featuresNavigator */
    private $featuresNavigator;

    /**
     * @param array $features
     */
    public function __construct(array $features)
    {
        $this->featuresNavigator = FeaturesNavigator::create($features);
    }

    /**
     * @return FeaturesNavigator
     */
    public function getFeaturesNavigator() : FeaturesNavigator
    {
        return $this->featuresNavigator;
    }
}
