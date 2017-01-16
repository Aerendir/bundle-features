<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Set the appropriate FeaturesHandler for each created FeaturesManager.
 */
class InvoiceManagersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $drawers = $this->getFormatters($container);

        foreach ($container->findTaggedServiceIds('shq_features.invoice_manager') as $service => $tags) {
            $managerDefinition = $container->getDefinition($service);

            foreach ($drawers as $key => $drawer) {
                $key = explode('.', $key);
                $drawerName = end($key);
                $managerDefinition->addMethodCall('addDrawer', [$drawerName, $drawer]);
            }
        }
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @return array
     */
    private function getFormatters(ContainerBuilder $containerBuilder) : array
    {
        $drawers = $containerBuilder->findTaggedServiceIds('shq_features.invoice_drawer');

        foreach ($drawers as $drawer => $tags) {
            $drawers[$drawer] = $containerBuilder->findDefinition($drawer);
        }

        return $drawers;
    }
}
