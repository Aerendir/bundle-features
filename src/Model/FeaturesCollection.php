<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * {@inheritdoc}
 */
class FeaturesCollection extends ArrayCollection implements \JsonSerializable
{
    /** @var FeaturesCollection $boolean */
    private $booleans;

    /**
     * @param array $elements
     */
    public function __construct($elements = array())
    {
        if (null === $elements)
            $elements = [];

        if (0 < count($elements)) {
            foreach ($elements as $feature => $details) {
                // Required as the Collection can be instantiated by the ArrayCollection::filter() method (see FeaturesHandler)
                if (is_array($details)) {
                    switch ($details['type']) {
                        case FeatureInterface::BOOLEAN:
                            $elements[$feature] = new BooleanFeature($feature, $details);
                            break;

                        case FeatureInterface::RECHARGEABLE:
                            $elements[$feature] = new RechargeableFeature($feature, $details);
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
     * @return FeaturesCollection
     */
    public function getBooleanFeatures()
    {
        if (null === $this->booleans) {
            $predictate = function ($element) {
                if ($element instanceof BooleanFeatureInterface)
                    return $element;
            };

            // Cache the result
            $this->booleans = $this->filter($predictate);
        }

        return $this->booleans;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $return = [];
        /**
         * @var string $featureName
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
