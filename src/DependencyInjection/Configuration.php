<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('features');

        $rootNode
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->arrayNode('features')
                    ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->enumNode('type')->values(['boolean', 'rechargeable'])->end()
                                ->scalarNode('enabled')->defaultFalse()->end()
                                // @todo Only if type === Rechargeable
                                ->scalarNode('free_recharge')->defaultNull()->end()
                                // @todo Only if type === Rechargeable
                                ->arrayNode('unitary_prices')
                                    // @todo Validate currency code
                                    ->useAttributeAsKey('name')
                                    ->prototype('integer')->end()
                                ->end()
                                // @todo Only if type === Rechargeable
                                ->arrayNode('packs')
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        // @todo Validate currency code
                                        ->useAttributeAsKey('name')
                                        ->prototype('integer')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('prices')
                                    // @todo Validate currency code
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        ->children()
                                            // @todo Set this as section
                                            ->scalarNode('monthly')->defaultNull()->end()
                                            ->scalarNode('yearly')->defaultNull()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end() // End features
                ->end()
            ->end();

        return $treeBuilder;
    }
}
