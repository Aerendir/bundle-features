<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SerendipityHQ\Bundle\FeaturesBundle\Util\FeaturesNavigator;
use Symfony\Component\Form\FormFactory;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * A trait to manage common tasks in a PremiumPlansManager.
 */
trait FeaturesManagerTrait
{
    /** @var FeaturesNavigator $featuresNavigator */
    private $featuresNavigator;

    /** @var FormFactory */
    private $formFactory;

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

    /**
     * @return FormFactory
     */
    public function getFormFactory() : FormFactory
    {
        return $this->formFactory;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }
}
