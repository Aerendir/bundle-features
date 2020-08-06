<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SerendipityHQ\Bundle\FeaturesBundle\FeaturesFactory;

/**
 * {@inheritdoc}
 */
abstract class AbstractFeaturesCollection extends ArrayCollection
{
    /**
     * @var null
     */
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

        if (0 < \count($elements)) {
            foreach ($elements as $feature => $details) {
                // Required as the Collection can be instantiated by the ArrayCollection::filter() method (see FeaturesHandler)
                if (\is_array($details)) {
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
                            throw new \InvalidArgumentException(\Safe\sprintf('Unknown feature of type "%s".', $details['type']));
                    }
                }
            }
        }

        parent::__construct($elements);
    }

    public function getBooleanFeatures(): \SerendipityHQ\Bundle\FeaturesBundle\Model\AbstractFeaturesCollection
    {
        if (null === $this->booleans) {
            // Cache the result
            $this->booleans = $this->filter($this->getFilterPredictate('boolean'));
        }

        return $this->booleans;
    }

    public function getCountableFeatures(): \SerendipityHQ\Bundle\FeaturesBundle\Model\AbstractFeaturesCollection
    {
        if (null === $this->countables) {
            // Cache the result
            $this->countables = $this->filter($this->getFilterPredictate('countable'));
        }

        return $this->countables;
    }

    public function getRechargeableFeatures(): \SerendipityHQ\Bundle\FeaturesBundle\Model\AbstractFeaturesCollection
    {
        if (null === $this->rechargeables) {
            // Cache the result
            $this->rechargeables = $this->filter($this->getFilterPredictate('rechargeable'));
        }

        return $this->rechargeables;
    }

    /**
     * @param string $type
     */
    private function getFilterPredictate(string $type): callable
    {
        $featureClass = $this->getFeatureClass($type);

        return function ($element) use ($featureClass): BaseObject {
            if ($element instanceof $featureClass) {
                return $element;
            }
        };
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getFeatureClass(string $type)
    {
        switch ($type) {
            case 'boolean':
        }

        return '\SerendipityHQ\Bundle\FeaturesBundle\Model\\' . \ucfirst(FeaturesFactory::getKind()) . \ucfirst($type) . 'Feature';
    }

    public function __clone()
    {
        foreach ($this as $key => $element) {
            $this->set($key, clone $element);
        }
    }
}
