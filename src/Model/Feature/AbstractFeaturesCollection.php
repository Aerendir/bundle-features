<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature;

use Doctrine\Common\Collections\ArrayCollection;
use SerendipityHQ\Bundle\FeaturesBundle\FeaturesFactory;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedRechargeableFeature;

use function Safe\sprintf;

abstract class AbstractFeaturesCollection extends ArrayCollection
{
    /** @var null */
    public const KIND = null;

    /** @var ConfiguredBooleanFeature[]|ConfiguredFeaturesCollection|SubscribedBooleanFeature[]|SubscribedFeaturesCollection|null $booleans */
    protected $booleans;

    /** @var ConfiguredCountableFeature[]|ConfiguredFeaturesCollection|SubscribedCountableFeature[]|SubscribedFeaturesCollection|null $countables */
    protected $countables;

    /** @var ConfiguredFeaturesCollection|ConfiguredRechargeableFeature[]|SubscribedFeaturesCollection|SubscribedRechargeableFeature[]|null $rechargeables */
    protected $rechargeables;

    public function __construct(string $kind, ?array $elements = [])
    {
        if (false === \in_array($kind, [ConfiguredFeaturesCollection::KIND, SubscribedFeaturesCollection::KIND])) {
            throw new \InvalidArgumentException(sprintf('Features kind can be only "configured" or "subscribed". You passed "%s".', $kind));
        }

        if (null === $elements) {
            $elements = [];
        }

        if ([] !== $elements) {
            foreach ($elements as $feature => $details) {
                // Required as the Collection can be instantiated by the ArrayCollection::filter() method (see FeaturesHandler)
                if (\is_array($details)) {
                    switch ($details[FeatureInterface::FIELD_TYPE]) {
                        case FeatureInterface::TYPE_BOOLEAN:
                            $elements[$feature] = FeaturesFactory::createBoolean($kind, $feature, $details);

                            break;

                        case FeatureInterface::TYPE_COUNTABLE:
                            $elements[$feature] = FeaturesFactory::createCountable($kind, $feature, $details);

                            break;

                        case FeatureInterface::TYPE_RECHARGEABLE:
                            $elements[$feature] = FeaturesFactory::createRechargeable($kind, $feature, $details);

                            break;

                        default:
                            throw new \InvalidArgumentException(sprintf('Unknown feature of type "%s".', $details[FeatureInterface::FIELD_TYPE]));
                    }
                }
            }
        }

        parent::__construct($elements);
    }

    public function __clone()
    {
        foreach ($this as $key => $element) {
            $this->set($key, clone $element);
        }
    }

    protected function getFilterPredictate(string $kind, string $type): callable
    {
        $featureClass = $this->getFeatureClass($kind, $type);

        return static fn ($element): bool => $element instanceof $featureClass;
    }

    private function getFeatureClass(string $kind, string $type): string
    {
        if (false === \in_array($kind, [ConfiguredFeaturesCollection::KIND, SubscribedFeaturesCollection::KIND])) {
            throw new \InvalidArgumentException(sprintf('Features kind can be only "configured" or "subscribed". You passed "%s".', $kind));
        }

        switch ($type) {
            case FeatureInterface::TYPE_BOOLEAN:
                return ConfiguredFeaturesCollection::KIND === $kind ? ConfiguredBooleanFeature::class : SubscribedBooleanFeature::class;
            case FeatureInterface::TYPE_COUNTABLE:
                return ConfiguredFeaturesCollection::KIND === $kind ? ConfiguredCountableFeature::class : SubscribedCountableFeature::class;
            case FeatureInterface::TYPE_RECHARGEABLE:
                return ConfiguredFeaturesCollection::KIND === $kind ? ConfiguredRechargeableFeature::class : SubscribedRechargeableFeature::class;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown feature of type "%s".', $type));
        }
    }
}
