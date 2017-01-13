<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

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
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Create services for features
        foreach ($config as $creatingServiceKey => $features) {
            $this->createFeaturesServices($creatingServiceKey, $features['features'], $container);
            $this->createInvoicesServices($creatingServiceKey, $features['features'], $container);
        }
    }

    /**
     * @param string           $name
     * @param array            $features
     * @param ContainerBuilder $containerBuilder
     */
    private function createFeaturesServices(string $name, array $features, ContainerBuilder $containerBuilder)
    {
        // Create the feature manager definition
        $featureManagerDefinition = new Definition(FeaturesManager::class, [$features]);
        $serviceName = 'shq_features.manager.'.$name.'.features';
        $containerBuilder->setDefinition($serviceName, $featureManagerDefinition);
    }

    /**
     * @param string           $name
     * @param array            $features
     * @param ContainerBuilder $containerBuilder
     */
    private function createInvoicesServices(string $name, array $features, ContainerBuilder $containerBuilder)
    {
        $arrayWriterDefinition = $containerBuilder->findDefinition('shq_features.array_writer');
        $invoicesManagerDefinition = new Definition(InvoicesManager::class, [$features, $arrayWriterDefinition]);
        $serviceName = 'shq_features.manager.'.$name.'.invoices';
        $containerBuilder->setDefinition($serviceName, $invoicesManagerDefinition);
    }
}
