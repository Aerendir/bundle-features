<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SerendipityHQ\Bundle\FeaturesBundle\FeaturesFactory;

/**
 * {@inheritdoc}
 */
abstract class AbstractFeaturesCollection extends ArrayCollection
{
    const KIND = null;

    /** @var AbstractFeaturesCollection $booleans */
    private $booleans;

    /** @var AbstractFeaturesCollection $countables */
    private $countables;

    /** @var AbstractFeaturesCollection $rechargeables */
    private $rechargeables;

    /**
     * @param array $elements
     */
    public function __construct($elements = [])
    {
        if (null === $elements) {
            $elements = [];
        }

        if (0 < count($elements)) {
            foreach ($elements as $feature => $details) {
                // Required as the Collection can be instantiated by the ArrayCollection::filter() method (see FeaturesHandler)
                if (is_array($details)) {
                    switch ($details['type']) {
                        case FeatureInterface::BOOLEAN:
                            $elements[$feature] = FeaturesFactory::createBoolean($feature, $details);
                            break;

                        case FeatureInterface::COUNTABLE:
                            $elements[$feature] = FeaturesFactory::createCountable($feature, $details);
                            break;

                        case FeatureInterface::RECHARGEABLE:
                            $elements[$feature] = FeaturesFactory::createRechargeable($feature, $details);
                            break;

                        default:
                            throw new \InvalidArgumentException(sprintf('Unknown feature of type "%s".', $details['type']));
                    }
                }
            }
        }

        parent::__construct($elements);
    }

    /**
     * @return AbstractFeaturesCollection
     */
    public function getBooleanFeatures()
    {
        if (null === $this->booleans) {
            // Cache the result
            $this->booleans = $this->filter($this->getFilterPredictate('boolean'));
        }

        return $this->booleans;
    }

    /**
     * @return AbstractFeaturesCollection
     */
    public function getCountableFeatures()
    {
        if (null === $this->countables) {
            // Cache the result
            $this->countables = $this->filter($this->getFilterPredictate('countable'));
        }

        return $this->countables;
    }

    /**
     * @return AbstractFeaturesCollection
     */
    public function getRechargeableFeatures()
    {
        if (null === $this->rechargeables) {
            // Cache the result
            $this->rechargeables = $this->filter($this->getFilterPredictate('rechargeable'));
        }

        return $this->rechargeables;
    }

    /**
     * @param string $type
     * @return \Closure
     */
    private function getFilterPredictate(string $type)
    {
        $featureClass = $this->getFeatureClass($type);

        return function ($element) use ($featureClass) {
            if ($element instanceof $featureClass) {
                return $element;
            }
        };
    }

    /**
     * @param string $type
     * @return string
     */
    private function getFeatureClass(string $type)
    {
        switch ($type) {
            case 'boolean':
        }

        return '\SerendipityHQ\Bundle\FeaturesBundle\Model\\' . ucfirst(FeaturesFactory::getKind()) . ucfirst($type) . 'Feature';
    }

    public function __clone()
    {
        foreach ($this as $key => $element) {
            $this->set($key, clone $element);
        }
    }
}
