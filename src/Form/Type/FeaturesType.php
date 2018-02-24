<?php

/*
 * This file is part of the SHQFeaturesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2016-2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2016 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
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
class FeaturesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var SubscribedFeaturesCollection $subscribedFeatures */
        $subscribedFeatures = $options['subscription']->getFeatures();

        /** @var ConfiguredFeatureInterface $configuredFeature */
        foreach ($options['configured_features']->getValues() as $configuredFeature) {
            /** @var SubscribedFeatureInterface $subscribedFeature */
            $subscribedFeature = $subscribedFeatures->get($configuredFeature->getName());

            // Process the right kind of feature
            switch (get_class($configuredFeature)) {
                case ConfiguredBooleanFeature::class:
                    $builder->add($configuredFeature->getName(), CheckboxType::class, $this->getBooleanFeatureOptions($options['subscription'], $subscribedFeature));
                    $builder->get($configuredFeature->getName())->addModelTransformer(new BooleanFeatureTransformer($configuredFeature->getName(), $subscribedFeatures));
                    break;
                case ConfiguredCountableFeature::class:
                    /** @var ConfiguredCountableFeature $configuredFeature */
                    $builder->add($configuredFeature->getName(), ChoiceType::class, $this->getCountableFeaturePacksOptions($options['subscription'], $subscribedFeature));
                    $builder->get($configuredFeature->getName())->addModelTransformer(new CountableFeatureTransformer($configuredFeature->getName(), $subscribedFeatures, $configuredFeature->getPacks()));
                    break;
                case ConfiguredRechargeableFeature::class:
                    /** @var ConfiguredRechargeableFeature $configuredFeature */
                    $builder->add($configuredFeature->getName(), ChoiceType::class, $this->getRechargeableFeaturePacksOptions($options['subscription'], $subscribedFeature));
                    $builder->get($configuredFeature->getName())->addModelTransformer(new RechargeableFeatureTransformer($configuredFeature->getName(), $subscribedFeatures, $configuredFeature->getPacks()));
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired([
            'configured_features',
            'subscription',
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
            'required' => false,
            'attr'     => [
                'data-feature'              => 'boolean',
                'data-toggle'               => 'toggle',
                'data-already-active'       => $subscribedFeature->isStillActive(),
                'data-gross-amount'         => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), 'gross')),
                'data-gross-instant-amount' => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), 'gross')),
                'data-net-amount'           => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), 'net')),
                'data-net-instant-amount'   => $this->formatPriceForDataAttribute($subscribedFeature->getConfiguredFeature()->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), 'net')),
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
            'required' => true,
            'attr'     => [
                'data-feature' => 'countable',
                'data-name'    => $subscribedFeature->getName(),
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
            'required' => true,
            'attr'     => [
                'data-feature' => 'rechargeable',
                'data-name'    => $subscribedFeature->getName(),
            ],
            'choices'     => $this->getRechargeableFeaturePacks($subscribedFeature->getConfiguredFeature()),
            'choice_attr' => $this->setRechargeableFeaturePacksPrices($subscription, $subscribedFeature->getConfiguredFeature()),
        ];
    }

    /**
     * @param ConfiguredCountableFeatureInterface $feature
     *
     * @return array
     */
    private function getCountableFeaturePacks(ConfiguredCountableFeatureInterface $feature)
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
     *
     * @return array
     */
    private function getRechargeableFeaturePacks(ConfiguredRechargeableFeatureInterface $feature)
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
     *
     * @return \Closure
     */
    private function setCountableFeaturePacksPrices(SubscriptionInterface $subscription, ConfiguredCountableFeatureInterface $configuredFeature)
    {
        return function ($val) use ($subscription, $configuredFeature) {
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
                'data-gross-amount'         => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), 'gross')),
                'data-gross-instant-amount' => $this->formatPriceForDataAttribute($pack->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), 'gross')),
                'data-net-amount'           => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), $subscription->getRenewInterval(), 'net')),
                'data-net-instant-amount'   => $this->formatPriceForDataAttribute($pack->getInstantPrice($subscription->getCurrency(), $subscription->getRenewInterval(), 'net')),
                'data-already-subscribed'   => $isPackAlreadyActive,
            ];
        };
    }

    /**
     * @param SubscriptionInterface                  $subscription
     * @param ConfiguredRechargeableFeatureInterface $configuredFeature
     *
     * @return \Closure
     */
    private function setRechargeableFeaturePacksPrices(SubscriptionInterface $subscription, ConfiguredRechargeableFeatureInterface $configuredFeature)
    {
        return function ($val) use ($subscription, $configuredFeature) {
            /** @var ConfiguredRechargeableFeaturePack $pack */
            $pack = $configuredFeature->getPack($val);

            return [
                'data-gross-instant-amount' => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), 'gross')),
                'data-net-instant-amount'   => $this->formatPriceForDataAttribute($pack->getPrice($subscription->getCurrency(), 'net')),
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
        $splitted = explode('.', $amount->getHumanAmount());

        $result = (int) $splitted[0];

        if (0 !== (int) $splitted[1]) {
            $sum    = '0.' . $splitted[1];
            $result = $result + (float) $sum;
        }

        return $result;
    }
}
