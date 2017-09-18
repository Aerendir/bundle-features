<?php

/*
 * This file is part of the SHQFeaturesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2016-2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2016 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle;

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
    }
}
