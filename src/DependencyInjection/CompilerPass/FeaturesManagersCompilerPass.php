<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\VarDumper\VarDumper;

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

            $invoicesManagerAlias = $aliasIdParts[0].'.'.$aliasIdParts[1].'.'.$aliasIdParts[2].'.invoices';
            $invoicesManagerDefinition = $container->findDefinition($invoicesManagerAlias);
            $managerDefinition->addMethodCall('setInvoicesManager', [$invoicesManagerDefinition]);
        }
    }
}
