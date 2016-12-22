<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Common interface for a PremiumPlansManager.
 */
interface FeaturesManagerInterface
{
    /**
     * @param array $plans
     */
    public function __construct(array $plans);

    /**
     * @param string $actionUrl
     * @param SubscriptionInterface $subscription
     * @param array $options
     * @return FormBuilderInterface
     */
    public function getFeaturesFormBuilder(string $actionUrl, SubscriptionInterface $subscription, array $options = []);
}
