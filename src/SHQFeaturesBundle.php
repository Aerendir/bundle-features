<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass\DrawersCompilerPass;
use SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass\FeaturesManagersCompilerPass;
use SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection\CompilerPass\InvoiceManagersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * {@inheritdoc}
 */
class SHQFeaturesBundle extends Bundle
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
        $container->addCompilerPass(DoctrineOrmMappingsPass::createAnnotationMappingDriver(['SerendipityHQ\Bundle\FeaturesBundle\Model'], [realpath(__DIR__ . '/Model')]));
    }
}
