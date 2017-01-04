<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SerendipityHQ\Bundle\FeaturesBundle\FeaturesFactory;

/**
 * {@inheritdoc}
 */
abstract class AbstractFeaturesCollection extends ArrayCollection implements \JsonSerializable
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
            $predictate = function ($element) {
                if ($element instanceof ConfiguredBooleanFeatureInterface) {
                    return $element;
                }
            };

            // Cache the result
            $this->booleans = $this->filter($predictate);
        }

        return $this->booleans;
    }

    /**
     * @return AbstractFeaturesCollection
     */
    public function getCountableFeatures()
    {
        if (null === $this->countables) {
            $predictate = function ($element) {
                if ($element instanceof ConfiguredCountableFeatureInterfaceConfigured) {
                    return $element;
                }
            };

            // Cache the result
            $this->countables = $this->filter($predictate);
        }

        return $this->countables;
    }

    /**
     * @return AbstractFeaturesCollection
     */
    public function getRechargeableFeatures()
    {
        if (null === $this->rechargeables) {
            $predictate = function ($element) {
                if ($element instanceof ConfiguredRechargeableFeatureInterfaceSimple) {
                    return $element;
                }
            };

            // Cache the result
            $this->rechargeables = $this->filter($predictate);
        }

        return $this->rechargeables;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $return = [];
        /**
         * @var string
         * @var FeatureInterface $featureDetils
         */
        foreach (parent::toArray() as $featureName => $featureDetils) {
            $return[$featureName] = $featureDetils->toArray();
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
