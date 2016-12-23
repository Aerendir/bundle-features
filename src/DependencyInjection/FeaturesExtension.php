<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

use SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass\SetHandlersCompilerPass;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesHandler;
use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesManager;
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
        $config        = $this->processConfiguration($configuration, $configs);

        // Create services for features
        foreach ($config as $creatingServiceKey => $creatingService) {
            $this->createFeaturesService($creatingServiceKey, $creatingService, $container);
        }
    }

    /**
     * @param string $name
     * @param array $features
     * @param ContainerBuilder $containerBuilder
     */
    private function createFeaturesService(string $name, array $features, ContainerBuilder $containerBuilder)
    {
        // Create the feature handler definition
        $featureHandlerDefinition = new Definition(FeaturesHandler::class, [$features['features']]);
        $serviceName = 'shq_features.handler.' . $name;
        $containerBuilder->setDefinition($serviceName, $featureHandlerDefinition);

        // Create the feature manager definition
        $featureManagerDefinition = new Definition(FeaturesManager::class);
        $serviceName = 'shq_features.manager.' . $name;
        $containerBuilder->setDefinition($serviceName, $featureManagerDefinition);
    }
}
