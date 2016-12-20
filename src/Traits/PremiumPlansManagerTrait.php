<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SerendipityHq\Bundle\FeaturesBundle\Util\PremiumPlansNavigator;

/**
 * A trait to manage common tasks in a PremiumPlansManager.
 */
trait PremiumPlansManagerTrait
{
    /** @var array $plans */
    private $plans;

    /**
     * @param array       $plans
     */
    public function __construct(array $plans)
    {
        $this->plans       = $plans;

        PremiumPlansNavigator::setPlans($this->plans);
    }
}
