<?php

declare(strict_types=1);

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
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SHQFeaturesBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $modelDir = realpath(__DIR__ . '/Model');
        if (false === $modelDir) {
            throw new \RuntimeException(sprintf('Impossible to resolve the path of the directory "%s".', __DIR__ . '/Model'));
        }

        $container->addCompilerPass(DoctrineOrmMappingsPass::createAttributeMappingDriver(['SerendipityHQ\Bundle\FeaturesBundle\Model'], [$modelDir], reportFieldsWhereDeclared: true), PassConfig::TYPE_BEFORE_OPTIMIZATION, 0);
    }
}
