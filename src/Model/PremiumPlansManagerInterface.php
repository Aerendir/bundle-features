<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * Common interface for a PremiumPlansManager.
 */
interface PremiumPlansManagerInterface
{
    /**
     * @param array $plans
     */
    public function __construct(array $plans);
}
