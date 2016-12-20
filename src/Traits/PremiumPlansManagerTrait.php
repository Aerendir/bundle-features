<?php

namespace SerendipityHq\Bundle\FeaturesBundle;

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

        PremiumPlansHelper::setPlans($this->plans);
    }
}
