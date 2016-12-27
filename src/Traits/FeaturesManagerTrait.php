<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesHandler;
use Symfony\Component\Form\FormFactory;

/**
 * A trait to manage common tasks in a PremiumPlansManager.
 */
trait FeaturesManagerTrait
{
    /** @var FeaturesHandler $featuresHandler */
    private $featuresHandler;

    /** @var FormFactory $formFactory */
    private $formFactory;

    /** @var  SubscriptionInterface $subscription */
    private $subscription;

    /**
     * @return FeaturesHandler
     */
    public function getFeaturesHandler() : FeaturesHandler
    {
        return $this->featuresHandler;
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
     * @param FeaturesHandler $featuresHandler
     */
    public function setFeaturesHandler(FeaturesHandler $featuresHandler)
    {
        $this->featuresHandler = $featuresHandler;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }
}
