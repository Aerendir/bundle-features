<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Form\Type;

use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\BooleanFeatureTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\CountableFeatureTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\RechargeableFeatureTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
final class FeaturesType extends AbstractType
{
    private const KEY_ATTR                  = 'attr';
    private const KEY_REQUIRED              = 'required';
    private const OPTION_SUBSCRIPTION       = 'subscription';
    private const DATA_FEATURE              = 'data-feature';
    private const DATA_INSTANT_AMOUNT_GROSS = 'data-gross-instant-amount';
    private const DATA_INSTANT_AMOUNT_NET   = 'data-net-instant-amount';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var SubscribedFeaturesCollection $subscribedFeatures */
        $subscribedFeatures = $options[self::OPTION_SUBSCRIPTION]->getFeatures();

        /** @var ConfiguredFeatureInterface $configuredFeature */
        foreach ($options['configured_features']->getValues() as $configuredFeature) {
            /** @var SubscribedFeatureInterface $subscribedFeature */
            $subscribedFeature = $subscribedFeatures->get($configuredFeature->getName());

            // Process the right kind of feature
            switch (\get_class($configuredFeature)) {
                case ConfiguredBooleanFeature::class:
                    $builder->add($configuredFeature->getName(), CheckboxType::class, $this->getBooleanFeatureOptions($options[self::OPTION_SUBSCRIPTION], $subscribedFeature));
                    $builder->get($configuredFeature->getName())->addModelTransformer(new BooleanFeatureTransformer($configuredFeature->getName(), $subscribedFeatures));

                    break;
                case ConfiguredCountableFeature::class:
                    /** @var ConfiguredCountableFeature $configuredFeature */
                    $builder->add($configuredFeature->getName(), ChoiceType::class, $this->getCountableFeaturePacksOptions($options[self::OPTION_SUBSCRIPTION], $subscribedFeature));
                    $builder->get($configuredFeature->getName())->addModelTransformer(new CountableFeatureTransformer($configuredFeature->getName(), $subscribedFeatures, $configuredFeature->getPacks()));

                    break;
                case ConfiguredRechargeableFeature::class:
                    /** @var ConfiguredRechargeableFeature $configuredFeature */
                    $builder->add($configuredFeature->getName(), ChoiceType::class, $this->getRechargeableFeaturePacksOptions($options[self::OPTION_SUBSCRIPTION], $subscribedFeature));
                    $builder->get($configuredFeature->getName())->addModelTransformer(new RechargeableFeatureTransformer($configuredFeature->getName(), $subscribedFeatures, $configuredFeature->getPacks()));

                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired([
            'configured_features',
            self::OPTION_SUBSCRIPTION,
        ]);
    }

    private function getBooleanFeatureOptions(SubscriptionInterface $subscription, SubscribedBooleanFeature $subscribedFeature = null): array
    {
        return [
            self::KEY_REQUIRED => false,
            self::KEY_ATTR     => [
                self::DATA_FEATURE              => FeatureInterface::TYPE_BOOLEAN,
                'data-toggle'                   => 'toggle',
                'data-already-active'           => $subscribedFeature->isStillActive(),
                'data-gross-amount'             => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), FeatureInterface::PRICE_GROSS)),
                self::DATA_INSTANT_AMOUNT_GROSS => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), FeatureInterface::PRICE_GROSS)),
                'data-net-amount'               => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), FeatureInterface::PRICE_NET)),
                self::DATA_INSTANT_AMOUNT_NET   => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), FeatureInterface::PRICE_NET)),
            ],
        ];
    }

    private function getCountableFeaturePacksOptions(SubscriptionInterface $subscription, SubscribedCountableFeature $subscribedFeature): array
    {
        return [
            self::KEY_REQUIRED => true,
            self::KEY_ATTR     => [
                self::DATA_FEATURE => FeatureInterface::TYPE_COUNTABLE,
                'data-name'        => $subscribedFeature->getName(),
            ],
            'choices'          => $this->getCountableFeaturePacks($subscribedFeature->getConfiguredFeature()),
            'choice_attr'      => $this->setCountableFeaturePacksPrices($subscription, $subscribedFeature->getConfiguredFeature()),
        ];
    }

    private function getRechargeableFeaturePacksOptions(SubscriptionInterface $subscription, SubscribedRechargeableFeature $subscribedFeature): array
    {
        return [
            self::KEY_REQUIRED => true,
            self::KEY_ATTR     => [
                self::DATA_FEATURE => FeatureInterface::TYPE_RECHARGEABLE,
                'data-name'        => $subscribedFeature->getName(),
            ],
            'choices'          => $this->getRechargeableFeaturePacks($subscribedFeature->getConfiguredFeature()),
            'choice_attr'      => $this->setRechargeableFeaturePacksPrices($subscription, $subscribedFeature->getConfiguredFeature()),
        ];
    }

    private function getCountableFeaturePacks(ConfiguredCountableFeature $feature): array
    {
        $choices = [];
        /** @var ConfiguredCountableFeaturePack $pack */
        foreach ($feature->getPacks() as $pack) {
            $choices[$pack->getNumOfUnits()] = $pack->getNumOfUnits();
        }

        return $choices;
    }

    private function getRechargeableFeaturePacks(ConfiguredRechargeableFeature $feature): array
    {
        $choices = [];
        /** @var ConfiguredRechargeableFeaturePack $pack */
        foreach ($feature->getPacks() as $pack) {
            $choices[$pack->getNumOfUnits()] = $pack->getNumOfUnits();
        }

        return $choices;
    }

    private function setCountableFeaturePacksPrices(SubscriptionInterface $subscription, ConfiguredCountableFeature $configuredFeature): callable
    {
        return function ($val) use ($subscription, $configuredFeature): array {
            /** @var ConfiguredCountableFeaturePack $pack */
            $pack = $configuredFeature->getPack($val);

            /** @var SubscribedCountableFeature $subscribedFeature */
            $subscribedFeature = $subscription->getFeatures()->get($configuredFeature->getName());

            $subscribedPack = $subscribedFeature->getSubscribedPack();

            if ($subscribedPack instanceof ConfiguredCountableFeaturePack) {
                $subscribedPack = $subscribedPack->getNumOfUnits();
            }

            $isPackAlreadyActive = $subscribedPack === $val;

            return [
                'data-gross-amount'             => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), FeatureInterface::PRICE_GROSS)),
                self::DATA_INSTANT_AMOUNT_GROSS => $this->formatPriceForDataAttribute($pack->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), FeatureInterface::PRICE_GROSS)),
                'data-net-amount'               => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), FeatureInterface::PRICE_NET)),
                self::DATA_INSTANT_AMOUNT_NET   => $this->formatPriceForDataAttribute($pack->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), FeatureInterface::PRICE_NET)),
                'data-already-subscribed'       => $isPackAlreadyActive,
            ];
        };
    }

    private function setRechargeableFeaturePacksPrices(SubscriptionInterface $subscription, ConfiguredRechargeableFeature $configuredFeature): callable
    {
        return function ($val) use ($subscription, $configuredFeature): array {
            /** @var ConfiguredRechargeableFeaturePack $pack */
            $pack = $configuredFeature->getPack($val);

            return [
                self::DATA_INSTANT_AMOUNT_GROSS => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), FeatureInterface::PRICE_GROSS)),
                self::DATA_INSTANT_AMOUNT_NET   => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), FeatureInterface::PRICE_NET)),
            ];
        };
    }

    /**
     * A really dirty way of getting a float or an integer removing the trailing 0s if float.
     *
     * @return float|int
     */
    private function formatPriceForDataAttribute(MoneyInterface $amount)
    {
        $splitted = \explode('.', $amount->getHumanAmount());

        $result = (int) $splitted[0];

        if (0 !== (int) $splitted[1]) {
            $sum    = '0.' . $splitted[1];
            $result += (float) $sum;
        }

        return $result;
    }
}
