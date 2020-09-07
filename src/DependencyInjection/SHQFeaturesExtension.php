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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
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

        $arrayWriterDefinition = new Definition(\SerendipityHQ\Component\ArrayWriter\ArrayWriter::class);
        $formFactoryReference  = new Reference('form.factory');
        $translatorReference   = new Reference('translator.default');
        $locale                = $container->getParameter('locale');

        // Create services for drawers
        $drawers = [];
        foreach ($config['invoices']['drawers'] as $drawer) {
            $drawers[$drawer] = $this->createFormatterService($drawer, $container, $locale, $translatorReference);
        }

        // Create services for features
        foreach ($config['sets'] as $creatingSetName => $setConfig) {
            $invoicesManagerDefinition = $this->createInvoicesService($creatingSetName, $setConfig, $container, $drawers, $arrayWriterDefinition);
            $this->createFeaturesService($creatingSetName, $setConfig, $container, $invoicesManagerDefinition, $formFactoryReference);
        }
    }

    private function createFormatterService(string $drawer, ContainerBuilder $containerBuilder, string $locale, Reference $translatorReference): Definition
    {
        // Create the drawer definition
        switch ($drawer) {
            case 'plain_text':
                $drawerDefinition  = new Definition(PlainTextDrawer::class, [$locale, $translatorReference]);
                $drawerServiceName = 'shq_features.drawer.plain_text';
                break;
            default:
                throw new \RuntimeException('The type of drawer "%s" is not recognized.');
        }

        $containerBuilder->setDefinition($drawerServiceName, $drawerDefinition);

        return $drawerDefinition;
    }

    /**
     * @param array<string, Definition> $drawers
     */
    private function createInvoicesService(string $name, array $setConfig, ContainerBuilder $containerBuilder, array $drawers, Definition $arrayWriterDefinition): Definition
    {
        $defaultDrawer             = $setConfig['default_drawer'] ?? null;
        $invoicesManagerDefinition = new Definition(InvoicesManager::class, [$setConfig['features'], $arrayWriterDefinition, $defaultDrawer, $drawers]);
        $serviceName               = 'shq_features.manager.' . $name . '.invoices';
        $containerBuilder->setDefinition($serviceName, $invoicesManagerDefinition);

        return $invoicesManagerDefinition;
    }

    private function createFeaturesService(string $name, array $setConfig, ContainerBuilder $containerBuilder, Definition $invoicesManagerDefinition, Reference $formFactoryReference): void
    {
        // Create the feature manager definition
        $featureManagerDefinition = new Definition(FeaturesManager::class, [$setConfig['features'], $invoicesManagerDefinition, $formFactoryReference]);
        $serviceName              = 'shq_features.manager.' . $name . '.features';
        $containerBuilder->setDefinition($serviceName, $featureManagerDefinition);
    }
}
