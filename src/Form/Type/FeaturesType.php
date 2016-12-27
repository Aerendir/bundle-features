<?php

/*
 * This file is part of the Trust Back Me Www.
 *
 * Copyright Adamo Aerendir Crespi 2012-2016.
 *
 * This code is to consider private and non disclosable to anyone for whatever reason.
 * Every right on this code is reserved.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2012 - 2016 Aerendir. All rights reserved.
 * @license   SECRETED. No distribution, no copy, no derivative, no divulgation or any other activity or action that
 *            could disclose this text.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Form\Type;

use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\FeatureModelTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesHandler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        /** @var FeaturesHandler $featuresHandler */
        $featuresHandler = $options['features_handler'];
        foreach ($featuresHandler->getFeatures(FeatureInterface::BOOLEAN) as $feature => $details) {
            $builder->add($feature, CheckboxType::class, ['required' => false]);
            $builder->get($feature)->addModelTransformer(new FeatureModelTransformer($feature));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired([
            'features_handler'
        ]);
    }
}
