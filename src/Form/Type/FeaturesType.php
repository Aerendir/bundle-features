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
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedCountableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeatureInterface;
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
    /**
     * @var string
     */
    private const SUBSCRIPTION = 'subscription';
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    /**
     * @var string
     */
    private const ATTR = 'attr';
    /**
     * @var string
     */
    private const DATA_FEATURE = 'data-feature';
    /**
     * @var string
     */
    private const GROSS = 'gross';
    /**
     * @var string
     */
    private const DATA_GROSS_INSTANT_AMOUNT = 'data-gross-instant-amount';
    /**
     * @var string
     */
    private const NET = 'net';
    /**
     * @var string
     */
    private const DATA_NET_INSTANT_AMOUNT = 'data-net-instant-amount';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var SubscribedFeaturesCollection $subscribedFeatures */
        $subscribedFeatures = $options[self::SUBSCRIPTION]->getFeatures();

        /** @var ConfiguredFeatureInterface $configuredFeature */
        foreach ($options['configured_features']->getValues() as $configuredFeature) {
            /** @var SubscribedFeatureInterface $subscribedFeature */
            $subscribedFeature = $subscribedFeatures->get($configuredFeature->getName());

            // Process the right kind of feature
            switch (\get_class($configuredFeature)) {
                case ConfiguredBooleanFeature::class:
                    $builder->add($configuredFeature->getName(), CheckboxType::class, $this->getBooleanFeatureOptions($options[self::SUBSCRIPTION], $subscribedFeature));
                    $builder->get($configuredFeature->getName())->addModelTransformer(new BooleanFeatureTransformer($configuredFeature->getName(), $subscribedFeatures));
                    break;
                case ConfiguredCountableFeature::class:
                    /** @var ConfiguredCountableFeature $configuredFeature */
                    $builder->add($configuredFeature->getName(), ChoiceType::class, $this->getCountableFeaturePacksOptions($options[self::SUBSCRIPTION], $subscribedFeature));
                    $builder->get($configuredFeature->getName())->addModelTransformer(new CountableFeatureTransformer($configuredFeature->getName(), $subscribedFeatures, $configuredFeature->getPacks()));
                    break;
                case ConfiguredRechargeableFeature::class:
                    /** @var ConfiguredRechargeableFeature $configuredFeature */
                    $builder->add($configuredFeature->getName(), ChoiceType::class, $this->getRechargeableFeaturePacksOptions($options[self::SUBSCRIPTION], $subscribedFeature));
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
            self::SUBSCRIPTION,
        ]);
    }

    /**
     * @param SubscriptionInterface                  $subscription
     * @param SubscribedBooleanFeatureInterface|null $subscribedFeature
     *
     * @return array
     */
    private function getBooleanFeatureOptions(SubscriptionInterface $subscription, SubscribedBooleanFeatureInterface $subscribedFeature = null): array
    {
        return [
            self::REQUIRED => false,
            self::ATTR     => [
                self::DATA_FEATURE              => 'boolean',
                'data-toggle'                   => 'toggle',
                'data-already-active'           => $subscribedFeature->isStillActive(),
                'data-gross-amount'             => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), self::GROSS)),
                self::DATA_GROSS_INSTANT_AMOUNT => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), self::GROSS)),
                'data-net-amount'               => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), self::NET)),
                self::DATA_NET_INSTANT_AMOUNT   => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), self::NET)),
            ],
        ];
    }

    /**
     * @param SubscriptionInterface               $subscription
     * @param SubscribedCountableFeatureInterface $subscribedFeature
     *
     * @return array
     */
    private function getCountableFeaturePacksOptions(SubscriptionInterface $subscription, SubscribedCountableFeatureInterface $subscribedFeature): array
    {
        return [
            self::REQUIRED => true,
            self::ATTR     => [
                self::DATA_FEATURE => 'countable',
                'data-name'        => $subscribedFeature->getName(),
            ],
            'choices'     => $this->getCountableFeaturePacks($subscribedFeature->getConfiguredFeature()),
            'choice_attr' => $this->setCountableFeaturePacksPrices($subscription, $subscribedFeature->getConfiguredFeature()),
        ];
    }

    /**
     * @param SubscriptionInterface                  $subscription
     * @param SubscribedRechargeableFeatureInterface $subscribedFeature
     *
     * @return array
     */
    private function getRechargeableFeaturePacksOptions(SubscriptionInterface $subscription, SubscribedRechargeableFeatureInterface $subscribedFeature): array
    {
        return [
            self::REQUIRED => true,
            self::ATTR     => [
                self::DATA_FEATURE => 'rechargeable',
                'data-name'        => $subscribedFeature->getName(),
            ],
            'choices'     => $this->getRechargeableFeaturePacks($subscribedFeature->getConfiguredFeature()),
            'choice_attr' => $this->setRechargeableFeaturePacksPrices($subscription, $subscribedFeature->getConfiguredFeature()),
        ];
    }

    /**
     * @param ConfiguredCountableFeatureInterface $feature
     */
    private function getCountableFeaturePacks(ConfiguredCountableFeatureInterface $feature): array
    {
        $choices = [];
        /** @var ConfiguredCountableFeaturePack $pack */
        foreach ($feature->getPacks() as $pack) {
            $choices[$pack->getNumOfUnits()] = $pack->getNumOfUnits();
        }

        return $choices;
    }

    /**
     * @param ConfiguredRechargeableFeatureInterface $feature
     */
    private function getRechargeableFeaturePacks(ConfiguredRechargeableFeatureInterface $feature): array
    {
        $choices = [];
        /** @var ConfiguredRechargeableFeaturePack $pack */
        foreach ($feature->getPacks() as $pack) {
            $choices[$pack->getNumOfUnits()] = $pack->getNumOfUnits();
        }

        return $choices;
    }

    /**
     * @param SubscriptionInterface               $subscription
     * @param ConfiguredCountableFeatureInterface $configuredFeature
     */
    private function setCountableFeaturePacksPrices(SubscriptionInterface $subscription, ConfiguredCountableFeatureInterface $configuredFeature): callable
    {
        return function ($val) use ($subscription, $configuredFeature): array {
            /** @var ConfiguredCountableFeaturePack $pack */
            $pack = $configuredFeature->getPack($val);

            /** @var SubscribedCountableFeatureInterface $subscribedFeature */
            $subscribedFeature = $subscription->getFeatures()->get($configuredFeature->getName());

            $subscribedPack = $subscribedFeature->getSubscribedPack();

            if ($subscribedPack instanceof ConfiguredCountableFeaturePack) {
                $subscribedPack = $subscribedPack->getNumOfUnits();
            }

            $isPackAlreadyActive = $subscribedPack === $val;

            return [
                'data-gross-amount'             => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), self::GROSS)),
                self::DATA_GROSS_INSTANT_AMOUNT => $this->formatPriceForDataAttribute($pack->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), self::GROSS)),
                'data-net-amount'               => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), self::NET)),
                self::DATA_NET_INSTANT_AMOUNT   => $this->formatPriceForDataAttribute($pack->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), self::NET)),
                'data-already-subscribed'       => $isPackAlreadyActive,
            ];
        };
    }

    /**
     * @param SubscriptionInterface                  $subscription
     * @param ConfiguredRechargeableFeatureInterface $configuredFeature
     */
    private function setRechargeableFeaturePacksPrices(SubscriptionInterface $subscription, ConfiguredRechargeableFeatureInterface $configuredFeature): callable
    {
        return function ($val) use ($subscription, $configuredFeature): array {
            /** @var ConfiguredRechargeableFeaturePack $pack */
            $pack = $configuredFeature->getPack($val);

            return [
                self::DATA_GROSS_INSTANT_AMOUNT => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), self::GROSS)),
                self::DATA_NET_INSTANT_AMOUNT   => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), self::NET)),
            ];
        };
    }

    /**
     * A really dirty way of getting a float or an integer removing the trailing 0s if float.
     *
     * @param MoneyInterface $amount
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
