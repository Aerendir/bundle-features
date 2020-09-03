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
final class InvoiceManagersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $drawers = $this->getFormatters($container);

        foreach (\array_keys($container->findTaggedServiceIds('shq_features.invoice_manager')) as $service) {
            $managerDefinition = $container->getDefinition($service);

            foreach ($drawers as $key => $drawer) {
                $key        = \explode('.', $key);
                $drawerName = \end($key);
                $managerDefinition->addMethodCall('addDrawer', [$drawerName, $drawer]);
            }
        }
    }

    private function getFormatters(ContainerBuilder $containerBuilder): array
    {
        $drawers = $containerBuilder->findTaggedServiceIds('shq_features.invoice_drawer');

        foreach (\array_keys($drawers) as $drawer) {
            $drawers[$drawer] = $containerBuilder->findDefinition($drawer);
        }

        return $drawers;
    }
}
