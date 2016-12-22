<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesHandler;
use Symfony\Component\Form\FormFactory;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * A trait to manage common tasks in a PremiumPlansManager.
 */
trait FeaturesManagerTrait
{
    /** @var FeaturesHandler $featuresHandler */
    private $featuresHandler;

    /** @var FormFactory */
    private $formFactory;

    /**
     * @return FeaturesHandler
     */
    public function getFeaturesHandler() : FeaturesHandler
    {
        return $this->featuresNavigator;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory() : FormFactory
    {
        return $this->formFactory;
    }

    /**
     * @param FeaturesHandler $featuresHandler
     */
    public function setFeaturesHandler(FeaturesHandler $featuresHandler)
    {
        $this->featuresNavigator = $featuresHandler;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }
}
