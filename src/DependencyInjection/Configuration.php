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

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use SerendipityHQ\Component\PHPTextMatrix\PHPTextMatrix;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{
    /** @var array $allowedDrawers The allowed drawers */
    private $allowedDrawers = ['plain_text'];

    /** @var array $foundDrawers The drawers found as default ones in features sets */
    private $foundDrawers = [];

    /** @var string $pricesKey The type of prices set: gross or net */
    private $pricesType;

    /** @var string $pricesKey The type of prices set: gross or net */
    private $pricesKey;

    /** @var string $unitaryPriceKey The type of prices set: gross or net */
    private $unitaryPriceKey;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('shq_features')
                ->children()
                    ->arrayNode('prices')
                        ->children()
                            ->enumNode('are')->values(['net', 'gross'])->defaultValue('gross')->end()
                        ->end()
                    ->end()
                    ->arrayNode('invoices')
                        ->children()
                            ->arrayNode('drawers')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('sets')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->arrayNode('features')
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        ->children()
                                            ->enumNode('type')
                                                ->values(['boolean', 'countable', 'rechargeable'])
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                            // type === Boolean
                                            ->scalarNode('enabled')->defaultFalse()->end()
                                            // type === Rechargeable || type === Countable
                                            ->scalarNode('cumulable')->defaultFalse()->end()
                                            // type === Countable
                                            ->enumNode('refresh_period')->defaultValue('monthly')->values(['monthly', 'yearly'])->end()
                                            // type === Rechargeable
                                            ->scalarNode('free_recharge')->defaultValue(0)->end()
                                            // type === Rechargeable (integer) || type === Countable (array)
                                            ->arrayNode('unitary_price')
                                                ->useAttributeAsKey('name')
                                                ->prototype('array')
                                                    // As we expect anyway an array, here we convert 'EUR'=>100 to 'EUR'=>['_'=>100]
                                                    ->beforeNormalization()
                                                        ->ifTrue(function ($price) {return is_numeric($price); })
                                                        ->then(function ($price) {
                                                            return ['_' => $price];
                                                        })
                                                    ->end()
                                                    ->children()
                                                        // Define acceptable subscription periods, including the artificial one '_' for scalars
                                                        ->scalarNode('monthly')->defaultNull()->end()
                                                        ->scalarNode('yearly')->defaultNull()->end()
                                                        ->scalarNode('_')->defaultNull()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            // type === Boolean || type === Countable
                                            ->arrayNode('price')
                                                ->useAttributeAsKey('name')
                                                ->prototype('array')
                                                    ->children()
                                                        // Define acceptable subscription periods,
                                                        ->integerNode('monthly')->defaultNull()->end()
                                                        ->integerNode('yearly')->defaultNull()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            // type === Countable || type === Rechargeable
                                            ->arrayNode('packs')
                                                ->useAttributeAsKey('name')
                                                ->prototype('array')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('array')
                                                        // As we expect anyway an array, here we convert 'EUR'=>100 to 'EUR'=>['_'=>100]
                                                        ->beforeNormalization()
                                                            ->ifTrue(function ($price) {return is_numeric($price); })
                                                            ->then(function ($price) {return ['_' => $price]; })
                                                        ->end()
                                                    ->children()
                                                        // Define acceptable subscription periods, including the artificial one '_' for scalars
                                                        ->scalarNode('monthly')->defaultNull()->end()
                                                        ->scalarNode('yearly')->defaultNull()->end()
                                                        ->scalarNode('_')->defaultNull()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end() // End Features
                            ->end()
                            ->enumNode('default_drawer')
                                ->values($this->allowedDrawers)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                // Deeply validate the full config tree
                ->ifTrue(function ($tree) {
                    return $this->validateTree($tree);
                })
                // Re-elaborate the tree removing unuseful values and preparing useful ones
                ->then(function ($tree) {
                    return $this->processTree($tree);
                })
            ->end();

        return $treeBuilder;
    }

    /**
     * @param array $tree
     *
     * @return array
     */
    private function validateTree(array $tree)
    {
        $tree['invoices']['drawers'] = $this->validateInvoiceDrawers($tree['invoices']['drawers']);
        $tree['sets']                = $this->validateSets($tree['sets']);

        return $tree;
    }

    /**
     * @param array $drawers
     */
    private function validateInvoiceDrawers(array $drawers)
    {
        foreach ($drawers as $drawer) {
            $this->validateInvoiceDrawer($drawer);
        }
    }

    /**
     * @param string $drawer
     */
    private function validateInvoiceDrawer(string $drawer)
    {
        if (false === in_array($drawer, $this->allowedDrawers)) {
            throw new InvalidConfigurationException(
                sprintf(
                    'The invoice drawer "%s" is not supported. Allowed invoice drawers are: %s.',
                    $drawer,
                    implode(', ', $this->allowedDrawers)
                )
            );
        }

        // Check the required dependency exists
        switch ($drawer) {
            case 'plain_text':
                if (false === class_exists(PHPTextMatrix::class)) {
                    throw new \RuntimeException('To use the "plain_text\' InvoiceFormatter you have to install "serendipity_hq/PHPTextMatrix dependency in your composer.json');
                }
                break;
        }
    }

    /**
     * Validates all the configured features sets.
     *
     * @param array $sets
     *
     * @return bool
     */
    private function validateSets(array $sets): bool
    {
        foreach ($sets as $set => $config) {
            // Validate the default invoice drawer if set
            if (isset($config['default_drawer'])) {
                $this->validateInvoiceDrawer($config['default_drawer']);
            }

            // Validate the features in the Set
            $this->validateFeatures($set, $config['features']);
        }

        return true;
    }

    /**
     * @param string $set
     * @param array  $features
     */
    private function validateFeatures(string $set, array $features)
    {
        foreach ($features as $feature => $config) {
            // Validate the features in the Set
            $this->validateFeatureConfig($set, $feature, $config);
        }
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array  $config
     */
    private function validateFeatureConfig(string $set, string $feature, array $config)
    {
        switch ($config['type']) {
            case 'boolean':
                $this->validateBoolean($set, $feature, $config);
                break;
            case 'countable':
                $this->validateCountable($set, $feature, $config);
                break;
            case 'rechargeable':
                $this->validateRechargeable($set, $feature, $config);
                break;
        }
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array  $config
     */
    private function validateBoolean(string $set, string $feature, array $config)
    {
        // If not set in the configuration, $config['price'] is automatically set as an empty array
        $this->validateRecurringPrice($set, $feature . '.price', $config['price']);
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array  $config
     */
    private function validateCountable(string $set, string $feature, array $config)
    {
        // If not set in the configuration, $config['price'] is automatically set as an empty array
        $this->validateRecurringPrice($set, $feature . '.packs.', $config['unitary_price']);

        // Validate the packages
        $this->validatePackages($set, $feature, $config['packs'], 'recurring');
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array  $config
     */
    private function validateRechargeable(string $set, string $feature, array $config)
    {
        // If not set in the configuration, $config['price'] is automatically set as an empty array
        $this->validateUnatantumPrice($set, $feature . '.packs', $config['unitary_price']);

        // Validate packages
        $this->validatePackages($set, $feature, $config['packs'], 'unatantum');
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array  $price
     */
    private function validateRecurringPrice(string $set, string $feature, array $price)
    {
        // If emmpty, may be because it doesn't exist and the TreeBuilder created it as an empty array, else...
        if (false === empty($price)) {
            // ... It contains Currency codes: validate each one of them and their subscription periods
            foreach ($price as $currency => $subscriptions) {
                // Validate the currency
                $this->validateCurrency($set, $feature, $currency);

                // Validate the subscription periods
                $this->validateSubscriptionPeriods($set, $feature, $currency, $subscriptions);
            }
        }
    }

    /**
     * @param string $set
     * @param string $feature
     * @param string $currency
     */
    private function validateCurrency(string $set, string $feature, string $currency)
    {
        $currencies = new ISOCurrencies();
        $currency   = new Currency($currency);
        if (false === $currencies->contains($currency)) {
            throw new InvalidConfigurationException(
                sprintf(
                    '%s.features.%s has an invalid ISO 4217 currency code "%s".',
                    $set, $feature, $currency
                )
            );
        }
    }

    /**
     * @param string $set
     * @param string $feature
     * @param string $currency
     * @param array  $subscriptions
     */
    private function validateSubscriptionPeriods(string $set, string $feature, string $currency, array $subscriptions)
    {
        // At least one subscription period has to be set
        if (null === $subscriptions['monthly'] && null === $subscriptions['yearly']) {
            throw new InvalidConfigurationException(
                sprintf(
                    '%s.features.%s.%s has no subscription period. To create a valid price, you have to set at'
                    . ' least one subscription period choosing between "monthly" and "yearly" or don\'t set the price at'
                    . ' all to make the feature free.',
                    $set, $feature, $currency
                )
            );
        }
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array  $packs
     * @param string $subscriptionType
     */
    private function validatePackages(string $set, string $feature, array $packs, string $subscriptionType)
    {
        // If emmpty, may be because it doesn't exist and the TreeBuilder created it as an empty array, else...
        if (false === empty($packs)) {
            $alreadyHasFreepack = false;
            // ... It contains packages: validate the number of units and their prices
            foreach ($packs as $numOfUnits => $price) {
                // The key has to be an integer
                if (false === is_int($numOfUnits)) {
                    throw new InvalidConfigurationException(
                        sprintf(
                            '%s.features.%s.packs.%s MUST be an integer as it has to represent the number of units in the package.',
                            $set, $feature, $numOfUnits
                        )
                    );
                }

                switch ($subscriptionType) {
                    case 'recurring':
                        // Validate the price
                        $this->validateRecurringPrice($set, $feature . '.packs.' . $numOfUnits, $price);
                        break;
                    case 'unatantum':
                        // If this is a free package
                        if (empty($price)) {
                            // We have to throw an exception as RechargeableFeatures cannot have a free package (it is useless)
                            throw new InvalidConfigurationException(
                                sprintf('%s.features.%s.packs.%s cannot be free of charge. Free packages are allowed only for CountableFeatures. Please set a price or remove this package.',
                                    $set, $feature, $numOfUnits));
                        }

                        // Validate the price
                        $this->validateUnatantumPrice($set, $feature . '.packs.' . $numOfUnits, $price);
                        break;
                }

                // Check if the current pack is free

                /** @todo Check there is only one free package **/
            }
        }
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array  $price
     */
    private function validateUnatantumPrice(string $set, string $feature, array $price)
    {
        if (false === empty($price)) {
            $currency = key($price);

            // Validate the currency
            $this->validateCurrency($set, $feature, $currency);
        }
    }

    /**
     * @param array $tree
     *
     * @return array
     */
    private function processTree(array $tree)
    {
        // Move all default drawers to the foundDrawers property to make them globally available
        $this->foundDrawers = $tree['invoices']['drawers'];

        // Set prices type: gross or net
        $this->pricesType      = $tree['prices']['are'];
        $this->pricesKey       = 'gross' === $this->pricesType ? 'gross_prices' : 'net_prices';
        $this->unitaryPriceKey = 'gross' === $this->pricesType ? 'gross_unitary_price' : 'net_unitary_price';

        // Reset the key
        $tree['invoices']['drawers'] = [];

        $tree['sets'] = $this->processSets($tree['sets']);

        // Readd the default drawers (already - now globally - existent plus the ones found in the single features sets)
        $tree['invoices']['drawers'] = array_merge($tree['invoices']['drawers'], $this->foundDrawers);

        return $tree;
    }

    /**
     * Processes all the configured features Sets.
     *
     * @param array $sets
     *
     * @return array
     */
    private function processSets(array $sets): array
    {
        foreach ($sets as $set => $config) {
            // If the set has a default invoice drawer set...
            if (isset($config['default_drawer']) && false === in_array($config['default_drawer'], $this->foundDrawers)) {
                // ... Add it to the list of the found drawers
                $this->foundDrawers[] = $config['default_drawer'];
            }

            $sets[$set]['features'] = $this->processFeatures($config['features']);
        }

        return $sets;
    }

    /**
     * @param array $features
     *
     * @return array
     */
    private function processFeatures(array $features)
    {
        foreach ($features as $feature => $config) {
            $features[$feature] = $this->processFeatureConfig($config);
        }

        return $features;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function processFeatureConfig(array $config)
    {
        $result = [];
        switch ($config['type']) {
            case 'boolean':
                $result = $this->processBoolean($config);
                break;
            case 'countable':
                $result = $this->processCountable($config);
                break;
            case 'rechargeable':
                $result = $this->processRechargeable($config);
                break;
        }

        return $result;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function processBoolean(array $config)
    {
        $config[$this->pricesKey] = $config['price'];

        unset(
            $config['cumulable'],
            $config['free_recharge'],
            $config['price'],
            $config['unitary_price'],
            $config['packs']
        );

        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function processCountable(array $config)
    {
        $config['packs'] = $this->processPackages($config['packs'], 'recurring');

        unset(
            $config['enabled'],
            $config['price'],
            $config['unitary_price'],
            $config['free_recharge']
        );

        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function processRechargeable(array $config)
    {
        unset(
            $config['cumulable'],
            $config['enabled'],
            $config['price']
        );

        $config[$this->unitaryPriceKey] = $this->processUnatantumPrice($config['unitary_price']);
        unset($config['unitary_price']);
        $config['packs'] = $this->processPackages($config['packs'], 'unatantum');

        return $config;
    }

    /**
     * @param array $prices
     *
     * @return array
     */
    private function processRecurringPrice(array $prices)
    {
        // If no prices are specified, the feature is free
        if (false === empty($prices)) {
            foreach ($prices as $currency => $price) {
                unset(
                    $prices[$currency]['_']
                );
            }
        }

        return $prices;
    }

    /**
     * @param array $packs
     * @param $subscriptionType
     *
     * @return array
     */
    private function processPackages(array $packs, $subscriptionType)
    {
        $subscriptionHasFreePackage = false;
        foreach ($packs as $numOfUnits => $prices) {
            switch ($subscriptionType) {
                case 'recurring':
                    $packs[$numOfUnits] = $this->processRecurringPrice($prices);

                    // If this is a free package
                    if (empty($prices)) {
                        // We have a free package so we haven't to create it
                        $subscriptionHasFreePackage = true;
                    }
                    break;
                case 'unatantum':
                    $packs[$numOfUnits] = $this->processUnatantumPrice($prices);
                    break;
            }
        }

        // If we are processing a recurring feature that hasn't a free package...
        if ('recurring' === $subscriptionType && false === $subscriptionHasFreePackage) {
            // ... We have to create it with 0 $numOfUnits as we always need a free package for a subscribed feature
            $packs[0] = [];
        }

        $packs['_pricesType'] = $this->pricesType;

        return $packs;
    }

    /**
     * @param array $prices
     *
     * @return array
     */
    private function processUnatantumPrice(array $prices)
    {
        foreach ($prices as $currency => $price) {
            $prices[$currency] = $price['_'];
        }

        return $prices;
    }
}
