<?php

namespace SerendipityHQ\Bundle\FeaturesBundle;

use SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass\FeaturesManagersCompilerPass;
use SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass\DrawersCompilerPass;
use SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass\InvoiceManagersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * {@inheritdoc}
 */
class FeaturesBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FeaturesManagersCompilerPass());
        $container->addCompilerPass(new InvoiceManagersCompilerPass());
        $container->addCompilerPass(new DrawersCompilerPass());
    }
}
