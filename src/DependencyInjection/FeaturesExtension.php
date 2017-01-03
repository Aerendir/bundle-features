<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

use SerendipityHQ\Bundle\FeaturesBundle\Service\FeaturesManager;
use SerendipityHQ\Bundle\FeaturesBundle\Service\InvoicesManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        // Create services for features
        foreach ($config as $creatingServiceKey => $features) {
            $features = $this->setAsFromConfiguration($features['features']);
            $this->createFeaturesServices($creatingServiceKey, $features, $container);
            $this->createInvoicesServices($creatingServiceKey, $features, $container);
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
        $invoicesManagerDefinition = new Definition(InvoicesManager::class, [$features]);
        $serviceName = 'shq_features.manager.'.$name.'.invoices';
        $containerBuilder->setDefinition($serviceName, $invoicesManagerDefinition);
    }

    /**
     * Adds a property to distinguish the features loaded from the configuration from the features loaded from a
     * subscription object.
     *
     * @param array $features
     *
     * @return array
     */
    private function setAsFromConfiguration(array $features)
    {
        $return = [];
        foreach ($features as $featureName => $featureDetails) {
            $featureDetails['from_configuration'] = true;
            $return[$featureName] = $featureDetails;
        }

        return $return;
    }
}
