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
 * Set the appropriate FeaturesHandler for each created FeaturesManager.
 */
class FeaturesManagersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('shq_features.feature_manager') as $service => $tags) {
            $managerDefinition = $container->getDefinition($service);

            $formFactoryDefinition = $container->findDefinition('form.factory');
            $managerDefinition->addMethodCall('setFormFactory', [$formFactoryDefinition]);

            $aliasIdParts = explode('.', $service);

            $invoicesManagerAlias      = $aliasIdParts[0] . '.' . $aliasIdParts[1] . '.' . $aliasIdParts[2] . '.invoices';
            $invoicesManagerDefinition = $container->findDefinition($invoicesManagerAlias);
            $managerDefinition->addMethodCall('setInvoicesManager', [$invoicesManagerDefinition]);
        }
    }
}
