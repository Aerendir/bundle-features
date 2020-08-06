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
 * Set the appropriate FeaturesHandler for each created FeaturesManager.
 */
final class FeaturesManagersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach (\array_keys($container->findTaggedServiceIds('shq_features.feature_manager')) as $service) {
            $managerDefinition = $container->getDefinition($service);

            $formFactoryDefinition = $container->findDefinition('form.factory');
            $managerDefinition->addMethodCall('setFormFactory', [$formFactoryDefinition]);

            $aliasIdParts = \explode('.', $service);

            $invoicesManagerAlias      = $aliasIdParts[0] . '.' . $aliasIdParts[1] . '.' . $aliasIdParts[2] . '.invoices';
            $invoicesManagerDefinition = $container->findDefinition($invoicesManagerAlias);
            $managerDefinition->addMethodCall('setInvoicesManager', [$invoicesManagerDefinition]);
        }
    }
}
