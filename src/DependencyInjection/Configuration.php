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
                    ->arrayNode('boolean') // Boolean type features
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('enabled')->defaultFalse()->end()
                                ->arrayNode('price')
                                    // @todo Validate currency code
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        ->children()
                                            // @todo Set this as section
                                            ->scalarNode('month')->defaultNull()->end()
                                            ->scalarNode('year')->defaultNull()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end() // End Boolean type features
                    ->arrayNode('rechargeable') // Rechargeable type features
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('free_recharge')->defaultValue(0)->end()
                                ->arrayNode('unitary_price')
                                    // @todo Validate currency code
                                    ->useAttributeAsKey('name')
                                    ->prototype('integer')->end()
                                ->end()
                                ->arrayNode('packs')
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        // @todo Validate currency code
                                        ->useAttributeAsKey('name')
                                        ->prototype('integer')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end() // End Rechargeable type features
                ->end()
            ->end();

        return $treeBuilder;
    }
}
