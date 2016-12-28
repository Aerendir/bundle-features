<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Set the appropriate FeaturesHandler for each created FeaturesManager.
 */
class SetManagersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach (array_keys($container->getDefinitions()) as $alias) {
            if (strpos($alias, 'shq_features') !== 0) {
                continue;
            }

            $aliasIdParts = explode('.', $alias);
            if ('manager' !== $aliasIdParts[1]) {
                continue;
            }

            $managerDefinition = $container->findDefinition($alias);
            $formFactoryDefinition = $container->findDefinition('form.factory');
            $managerDefinition->addMethodCall('setFormFactory', [$formFactoryDefinition]);
        }
    }
}
