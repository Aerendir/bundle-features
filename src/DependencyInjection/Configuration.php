<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use function Safe\sprintf;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\PHPTextMatrix\PHPTextMatrix;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 */
final class Configuration implements ConfigurationInterface
{
    /** @var string[] The allowed drawers */
    private const ALLOWED_DRAWERS = ['plain_text'];

    private const INVOICES        = 'invoices';

    private const DRAWERS         = 'drawers';

    private const SETS            = 'sets';

    private const DEFAULT_DRAWER  = 'default_drawer';

    private const FEATURES        = 'features';

    private const PRICE           = 'price';

    private const UNITARY_PRICE   = 'unitary_price';

    private const PACKS           = 'packs';

    private const RECURRING       = 'recurring';

    private const UNATANTUM       = 'unatantum';

    /** @var $foundDrawers The drawers found as default ones in features sets */
    private array $foundDrawers = [];

    /** @var string $pricesKey The type of prices set: gross or net */
    private string $pricesType;

    /** @var string $pricesKey The type of prices set: gross or net */
    private string $pricesKey;

    /** @var string $unitaryPriceKey The type of prices set: gross or net */
    private string $unitaryPriceKey;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('shq_features');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
                ->children()
                    ->arrayNode('prices')
                        ->children()
                            ->enumNode('are')->values([FeatureInterface::PRICE_NET, FeatureInterface::PRICE_GROSS])->defaultValue(FeatureInterface::PRICE_GROSS)->end()
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
                                                ->values([FeatureInterface::TYPE_BOOLEAN, FeatureInterface::TYPE_COUNTABLE, FeatureInterface::TYPE_RECHARGEABLE])
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                            // type === Boolean
                                            ->scalarNode('enabled')->defaultFalse()->end()
                                            // type === Rechargeable || type === Countable
                                            ->scalarNode('cumulable')->defaultFalse()->end()
                                            // type === Countable
                                            ->enumNode('refresh_period')->defaultValue(SubscriptionInterface::MONTHLY)->values([SubscriptionInterface::MONTHLY, SubscriptionInterface::YEARLY])->end()
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
                                                        ->scalarNode(SubscriptionInterface::MONTHLY)->defaultNull()->end()
                                                        ->scalarNode(SubscriptionInterface::YEARLY)->defaultNull()->end()
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
                                                        ->integerNode(SubscriptionInterface::MONTHLY)->defaultNull()->end()
                                                        ->integerNode(SubscriptionInterface::YEARLY)->defaultNull()->end()
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
                                                        ->scalarNode(SubscriptionInterface::MONTHLY)->defaultNull()->end()
                                                        ->scalarNode(SubscriptionInterface::YEARLY)->defaultNull()->end()
                                                        ->scalarNode('_')->defaultNull()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end() // End Features
                            ->end()
                            ->enumNode('default_drawer')
                                ->values(self::ALLOWED_DRAWERS)
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

    private function validateTree(array $tree): array
    {
        $this->validateInvoiceDrawers($tree[self::INVOICES][self::DRAWERS]);
        $this->validateSets($tree[self::SETS]);

        return $tree;
    }

    private function validateInvoiceDrawers(array $drawers): void
    {
        foreach ($drawers as $drawer) {
            $this->validateInvoiceDrawer($drawer);
        }
    }

    private function validateInvoiceDrawer(string $drawer): void
    {
        if (false === \in_array($drawer, self::ALLOWED_DRAWERS)) {
            throw new InvalidConfigurationException(sprintf('The invoice drawer "%s" is not supported. Allowed invoice drawers are: %s.', $drawer, \implode(', ', self::ALLOWED_DRAWERS)));
        }

        if ('plain_text' === $drawer && false === \class_exists(PHPTextMatrix::class)) {
            throw new \RuntimeException('To use the "plain_text\' InvoiceFormatter you have to install "serendipity_hq/PHPTextMatrix dependency in your composer.json');
        }
    }

    /**
     * Validates all the configured features sets.
     */
    private function validateSets(array $sets): void
    {
        foreach ($sets as $set => $config) {
            // Validate the default invoice drawer if set
            if (isset($config[self::DEFAULT_DRAWER])) {
                $this->validateInvoiceDrawer($config[self::DEFAULT_DRAWER]);
            }

            // Validate the features in the Set
            $this->validateFeatures($set, $config[self::FEATURES]);
        }
    }

    private function validateFeatures(string $set, array $features): void
    {
        foreach ($features as $feature => $config) {
            // Validate the features in the Set
            $this->validateFeatureConfig($set, $feature, $config);
        }
    }

    private function validateFeatureConfig(string $set, string $feature, array $config): void
    {
        switch ($config[FeatureInterface::FIELD_TYPE]) {
            case FeatureInterface::TYPE_BOOLEAN:
                $this->validateBoolean($set, $feature, $config);

                break;
            case FeatureInterface::TYPE_COUNTABLE:
                $this->validateCountable($set, $feature, $config);

                break;
            case FeatureInterface::TYPE_RECHARGEABLE:
                $this->validateRechargeable($set, $feature, $config);

                break;
        }
    }

    private function validateBoolean(string $set, string $feature, array $config): void
    {
        // If not set in the configuration, $config['price'] is automatically set as an empty array
        $this->validateRecurringPrice($set, $feature . '.price', $config[self::PRICE]);
    }

    private function validateCountable(string $set, string $feature, array $config): void
    {
        // If not set in the configuration, $config['price'] is automatically set as an empty array
        $this->validateRecurringPrice($set, $feature . '.packs.', $config[self::UNITARY_PRICE]);

        // Validate the packages
        $this->validatePackages($set, $feature, $config[self::PACKS], self::RECURRING);
    }

    private function validateRechargeable(string $set, string $feature, array $config): void
    {
        // If not set in the configuration, $config['price'] is automatically set as an empty array
        $this->validateUnatantumPrice($set, $feature . '.packs', $config[self::UNITARY_PRICE]);

        // Validate packages
        $this->validatePackages($set, $feature, $config[self::PACKS], self::UNATANTUM);
    }

    private function validateRecurringPrice(string $set, string $feature, array $price): void
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

    private function validateCurrency(string $set, string $feature, string $currency): void
    {
        $currencies = new ISOCurrencies();
        $currency   = new Currency($currency);
        if (false === $currencies->contains($currency)) {
            throw new InvalidConfigurationException(sprintf('%s.features.%s has an invalid ISO 4217 currency code "%s".', $set, $feature, $currency));
        }
    }

    private function validateSubscriptionPeriods(string $set, string $feature, string $currency, array $subscriptions): void
    {
        // At least one subscription period has to be set
        if (null === $subscriptions[SubscriptionInterface::MONTHLY] && null === $subscriptions[SubscriptionInterface::YEARLY]) {
            throw new InvalidConfigurationException(sprintf('%s.features.%s.%s has no subscription period. To create a valid price, you have to set at' . ' least one subscription period choosing between "monthly" and "yearly" or don\'t set the price at' . ' all to make the feature free.', $set, $feature, $currency));
        }
    }

    private function validatePackages(string $set, string $feature, array $packs, string $subscriptionType): void
    {
        // If empty, may be because it doesn't exist and the TreeBuilder created it as an empty array, else...
        if (false === empty($packs)) {
            // ... It contains packages: validate the number of units and their prices
            foreach ($packs as $numOfUnits => $price) {
                // The key has to be an integer
                if (false === \is_int($numOfUnits)) {
                    throw new InvalidConfigurationException(sprintf('%s.features.%s.packs.%s MUST be an integer as it has to represent the number of units in the package.', $set, $feature, $numOfUnits));
                }

                switch ($subscriptionType) {
                    case self::RECURRING:
                        // Validate the price
                        $this->validateRecurringPrice($set, $feature . '.packs.' . $numOfUnits, $price);

                        break;
                    case self::UNATANTUM:
                        // If this is a free package
                        if (empty($price)) {
                            // We have to throw an exception as RechargeableFeatures cannot have a free package (it is useless)
                            throw new InvalidConfigurationException(sprintf('%s.features.%s.packs.%s cannot be free of charge. Free packages are allowed only for CountableFeatures. Please set a price or remove this package.', $set, $feature, $numOfUnits));
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

    private function validateUnatantumPrice(string $set, string $feature, array $price): void
    {
        if (false === empty($price)) {
            $currency = \key($price);

            // Validate the currency
            $this->validateCurrency($set, $feature, $currency);
        }
    }

    private function processTree(array $tree): array
    {
        // Move all default drawers to the foundDrawers property to make them globally available
        $this->foundDrawers = $tree[self::INVOICES][self::DRAWERS];

        // Set prices type: gross or net
        $this->pricesType      = $tree['prices']['are'];
        $this->pricesKey       = FeatureInterface::PRICE_GROSS === $this->pricesType ? 'gross_prices' : 'net_prices';
        $this->unitaryPriceKey = FeatureInterface::PRICE_GROSS === $this->pricesType ? 'gross_unitary_price' : 'net_unitary_price';

        // Reset the key
        $tree[self::INVOICES][self::DRAWERS] = [];

        $tree[self::SETS] = $this->processSets($tree[self::SETS]);

        // Readd the default drawers (already - now globally - existent plus the ones found in the single features sets)
        $tree[self::INVOICES][self::DRAWERS] = \array_merge($tree[self::INVOICES][self::DRAWERS], $this->foundDrawers);

        return $tree;
    }

    /**
     * Processes all the configured features Sets.
     */
    private function processSets(array $sets): array
    {
        foreach ($sets as $set => $config) {
            // If the set has a default invoice drawer set...
            if (isset($config[self::DEFAULT_DRAWER]) && false === \in_array($config[self::DEFAULT_DRAWER], $this->foundDrawers)) {
                // ... Add it to the list of the found drawers
                $this->foundDrawers[] = $config[self::DEFAULT_DRAWER];
            }

            $sets[$set][self::FEATURES] = $this->processFeatures($config[self::FEATURES]);
        }

        return $sets;
    }

    private function processFeatures(array $features): array
    {
        foreach ($features as $feature => $config) {
            $features[$feature] = $this->processFeatureConfig($config);
        }

        return $features;
    }

    private function processFeatureConfig(array $config): array
    {
        $result = [];
        switch ($config[FeatureInterface::FIELD_TYPE]) {
            case FeatureInterface::TYPE_BOOLEAN:
                $result = $this->processBoolean($config);

                break;
            case FeatureInterface::TYPE_COUNTABLE:
                $result = $this->processCountable($config);

                break;
            case FeatureInterface::TYPE_RECHARGEABLE:
                $result = $this->processRechargeable($config);

                break;
        }

        return $result;
    }

    private function processBoolean(array $config): array
    {
        $config[$this->pricesKey] = $config[self::PRICE];

        unset(
            $config['cumulable'],
            $config['free_recharge'],
            $config[self::PRICE],
            $config[self::UNITARY_PRICE],
            $config[self::PACKS]
        );

        return $config;
    }

    private function processCountable(array $config): array
    {
        $config[self::PACKS] = $this->processPackages($config[self::PACKS], self::RECURRING);

        unset(
            $config['enabled'],
            $config[self::PRICE],
            $config[self::UNITARY_PRICE],
            $config['free_recharge']
        );

        return $config;
    }

    private function processRechargeable(array $config): array
    {
        unset(
            $config['cumulable'],
            $config['enabled'],
            $config[self::PRICE]
        );

        $config[$this->unitaryPriceKey] = $this->processUnatantumPrice($config[self::UNITARY_PRICE]);
        unset($config[self::UNITARY_PRICE]);
        $config[self::PACKS] = $this->processPackages($config[self::PACKS], self::UNATANTUM);

        return $config;
    }

    private function processRecurringPrice(array $prices): array
    {
        // If no prices are specified, the feature is free
        if (false === empty($prices)) {
            foreach (\array_keys($prices) as $currency) {
                unset(
                    $prices[$currency]['_']
                );
            }
        }

        return $prices;
    }

    /**
     * @param $subscriptionType
     */
    private function processPackages(array $packs, $subscriptionType): array
    {
        $subscriptionHasFreePackage = false;
        foreach ($packs as $numOfUnits => $prices) {
            switch ($subscriptionType) {
                case self::RECURRING:
                    $packs[$numOfUnits] = $this->processRecurringPrice($prices);

                    // Once the free package is found, we don't need the check anymore
                    if (false === $subscriptionHasFreePackage) {
                        $subscriptionHasFreePackage = $this->recurringFeatureHasFreePackage($prices);
                    }

                    break;
                case self::UNATANTUM:
                    $packs[$numOfUnits] = $this->processUnatantumPrice($prices);

                    break;
            }
        }

        // If we are processing a recurring feature that hasn't a free package...
        if (self::RECURRING === $subscriptionType && false === $subscriptionHasFreePackage) {
            // ... We have to create it with 0 $numOfUnits as we always need a free package for a subscribed feature
            $packs[0] = [
                SubscriptionInterface::MONTHLY => 0,
                SubscriptionInterface::YEARLY  => 0,
            ];
        }

        $packs['_pricesType'] = $this->pricesType;

        return $packs;
    }

    private function processUnatantumPrice(array $prices): array
    {
        foreach ($prices as $currency => $price) {
            $prices[$currency] = $price['_'];
        }

        return $prices;
    }

    private function recurringFeatureHasFreePackage(array $prices): bool
    {
        foreach ($prices as $currency => $localizedPrices) {
            $monthly = $localizedPrices[SubscriptionInterface::MONTHLY] ?? null;
            $yearly  = $localizedPrices[SubscriptionInterface::YEARLY]  ?? null;

            // If this is a free package
            if (0 !== $monthly || 0 !== $yearly) {
                // We have a free package so we haven't to create it
                return false;
            }
        }

        return true;
    }
}
