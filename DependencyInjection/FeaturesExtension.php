<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

use SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer\PlainTextDrawer;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesManager;
use SerendipityHQ\Bundle\FeaturesBundle\Service\InvoicesManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\VarDumper\VarDumper;

/**
 * {@inheritdoc}
 */
class FeaturesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $set = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Create services for drawers
        foreach ($set['invoices_drawers'] as $drawer) {
            $this->createFormatterService($drawer, $container);
        }

        // Create services for features
        foreach ($set['sets'] as $creatingServiceKey => $set) {
            $this->createFeaturesService($creatingServiceKey, $set['features'], $container);
            $this->createInvoicesService($creatingServiceKey, $set, $container);
        }
    }

    /**
     * @param string $drawer
     * @param ContainerBuilder $containerBuilder
     */
    private function createFormatterService(string $drawer, ContainerBuilder $containerBuilder)
    {
        $drawerServiceName = null;
        $drawerDefinition = null;
        // Create the drawer definition
        switch ($drawer) {
            case 'plain_text':
                $drawerDefinition = new Definition(PlainTextDrawer::class);
                $drawerServiceName = 'shq_features.drawer.plain_text';
                break;
        }

        $drawerDefinition->addTag('shq_features.invoice_drawer');
        $containerBuilder->setDefinition($drawerServiceName, $drawerDefinition);
    }

    /**
     * @param string           $name
     * @param array            $features
     * @param ContainerBuilder $containerBuilder
     */
    private function createFeaturesService(string $name, array $features, ContainerBuilder $containerBuilder)
    {
        // Create the feature manager definition
        $featureManagerDefinition = new Definition(FeaturesManager::class, [$features]);
        $serviceName = 'shq_features.manager.'.$name.'.features';
        $featureManagerDefinition->addTag('shq_features.feature_manager');
        $containerBuilder->setDefinition($serviceName, $featureManagerDefinition);
    }

    /**
     * @param string           $name
     * @param array            $config
     * @param ContainerBuilder $containerBuilder
     */
    private function createInvoicesService(string $name, array $config, ContainerBuilder $containerBuilder)
    {
        $arrayWriterDefinition = $containerBuilder->findDefinition('shq_features.array_writer');
        $defaultDrawer = $config['default_drawer'] ?? null;
        $invoicesManagerDefinition = new Definition(InvoicesManager::class, [$config['features'], $arrayWriterDefinition, $defaultDrawer]);
        $serviceName = 'shq_features.manager.'.$name.'.invoices';
        $invoicesManagerDefinition->addTag('shq_features.invoice_manager');
        $containerBuilder->setDefinition($serviceName, $invoicesManagerDefinition);
    }
}
