<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SerendipityHQ\Bundle\FeaturesBundle\Model\BooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesManagerInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\RechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesHandler;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesManager;
use Symfony\Component\Form\FormFactory;

/**
 * A trait to manage common tasks in a PremiumPlansManager.
 */
trait FeaturesManagerTrait
{
    /** @var  FeaturesCollection $configuredFeatures */
    private $configuredFeatures;

    /** @var FeaturesCollection $boolean */
    private $configuredBooleans;

    /** @var FeaturesCollection $configuredRechargeables */
    private $configuredRechargeables;

    /** @var FormFactory $formFactory */
    private $formFactory;

    /** @var  SubscriptionInterface $subscription */
    private $subscription;

    /**
     * Returns all the configured features.
     *
     * @return FeaturesCollection
     */
    public function getConfiguredFeatures() : FeaturesCollection
    {
        return $this->configuredFeatures;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory() : FormFactory
    {
        return $this->formFactory;
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription() : SubscriptionInterface
    {
        return $this->subscription;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return FeaturesManagerInterface
     */
    public function setSubscription(SubscriptionInterface $subscription) : FeaturesManager
    {
        //$this->getFeaturesHandler()->setSubscription($subscription);
        $this->subscription = $subscription;

        return $this;
    }
}
