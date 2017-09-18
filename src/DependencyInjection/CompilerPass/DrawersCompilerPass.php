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

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Configure the Formatters with all required other services and parameters.
 */
class DrawersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $translatorDefinition = $container->findDefinition('translator.default');
        foreach ($container->findTaggedServiceIds('shq_features.invoice_drawer') as $service => $tags) {
            $drawerDefinition = $container->getDefinition($service);
            $locale           = $container->getParameter('locale');
            $drawerDefinition->addMethodCall('setTranslator', [$translatorDefinition, $locale]);
        }
    }
}
