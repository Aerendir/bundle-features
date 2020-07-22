<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
