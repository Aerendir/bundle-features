<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

use SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer\PlainTextDrawer;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesManager;
use SerendipityHQ\Bundle\FeaturesBundle\Service\InvoicesManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * {@inheritdoc}
 */
final class SHQFeaturesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Create services for drawers
        foreach ($config['invoices']['drawers'] as $drawer) {
            $this->createFormatterService($drawer, $container);
        }

        // Create services for features
        foreach ($config['sets'] as $creatingSetName => $setConfig) {
            $this->createFeaturesService($creatingSetName, $setConfig, $container);
            $this->createInvoicesService($creatingSetName, $setConfig, $container);
        }
    }

    private function createFormatterService(string $drawer, ContainerBuilder $containerBuilder): void
    {
        $drawerServiceName = null;
        $drawerDefinition  = null;
        // Create the drawer definition
        switch ($drawer) {
            case 'plain_text':
                $drawerDefinition  = new Definition(PlainTextDrawer::class);
                $drawerServiceName = 'shq_features.drawer.plain_text';
                break;
        }

        $drawerDefinition->addTag('shq_features.invoice_drawer');
        $containerBuilder->setDefinition($drawerServiceName, $drawerDefinition);
    }

    private function createFeaturesService(string $name, array $setConfig, ContainerBuilder $containerBuilder): void
    {
        // Create the feature manager definition
        $featureManagerDefinition = new Definition(FeaturesManager::class, [$setConfig['features']]);
        $serviceName              = 'shq_features.manager.' . $name . '.features';
        $featureManagerDefinition->addTag('shq_features.feature_manager');
        $containerBuilder->setDefinition($serviceName, $featureManagerDefinition);
    }

    private function createInvoicesService(string $name, array $setConfig, ContainerBuilder $containerBuilder): void
    {
        $arrayWriterDefinition     = $containerBuilder->findDefinition('shq_features.array_writer');
        $defaultDrawer             = $setConfig['default_drawer'] ?? null;
        $invoicesManagerDefinition = new Definition(InvoicesManager::class, [$setConfig['features'], $arrayWriterDefinition, $defaultDrawer]);
        $serviceName               = 'shq_features.manager.' . $name . '.invoices';
        $invoicesManagerDefinition->addTag('shq_features.invoice_manager');
        $containerBuilder->setDefinition($serviceName, $invoicesManagerDefinition);
    }
}
