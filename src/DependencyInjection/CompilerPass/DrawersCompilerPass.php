<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Configure the Formatters with all required other services and parameters.
 */
class DrawersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $translatorDefinition = $container->findDefinition('translator.default');
        foreach ($container->findTaggedServiceIds('shq_features.invoice_drawer') as $service => $tags) {
            $drawerDefinition = $container->getDefinition($service);
            $locale = $container->getParameter('locale');
            $drawerDefinition->addMethodCall('setTranslator', [$translatorDefinition, $locale]);
        }
    }
}
