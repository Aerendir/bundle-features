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
     * @var array $plans
     * @deprecated Use getFeaturesNavigator() instead
     */
    private $plans;

    /**
     * @param array $features
     */
    public function __construct(array $features)
    {
        $this->featuresNavigator = FeaturesNavigator::create($features);
        $this->plans = $features;
    }

    /**
     * @return FeaturesNavigator
     */
    public function getFeaturesNavigator() : FeaturesNavigator
    {
        return $this->featuresNavigator;
    }
}
