<?php

namespace SerendipityHQ\Bundle\FeaturesBundle;

use SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass\SetHandlersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class HubBundle.
 *
 * {@inheritdoc}
 */
class FeaturesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SetHandlersCompilerPass());
    }
}
