<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\DependencyInjection;

use SebastianBergmann\Money\Currency;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 *
 * {@inheritdoc}
 *
 * Thanks to Alex Blex for the good advice (http://stackoverflow.com/a/41491901/1399706)
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('features')->useAttributeAsKey('name')
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
                                // type === Countable
                                ->integerNode('free_amount')->defaultValue(0)->end()
                                // type === Rechargeable
                                ->scalarNode('cumulable')->defaultFalse()->end()
                                // type === Rechargeable
                                ->scalarNode('free_recharge')->defaultValue(0)->end()
                                // type === Rechargeable (integer) || type === Countable (array)
                                ->arrayNode('unitary_price')
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        // As we expect anyway an array, here we convert 'EUR'=>100 to 'EUR'=>['_'=>100]
                                        ->beforeNormalization()
                                            ->ifTrue(function($price) {return is_numeric($price);})
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
                                                ->ifTrue(function($price) {return is_numeric($price);})
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
                                ->end()
                            ->end()
                        ->end()
                    ->end() // End features
                ->end()
            ->end()
            ->validate()
                // Deeply validate the full config tree
                ->ifTrue(function($sets) {
                    return $this->validateSets($sets);
                })
                // Re-elaborate the tree removing unuseful values and preparing useful ones
                ->then(function($sets) {
                    return $this->processSets($sets);
                })
            ->end();

        return $treeBuilder;
    }

    /**
     * Validates all the configured features sets.
     *
     * @param array $sets
     * @return bool
     */
    private function validateSets(array $sets) : bool
    {
        $validatedSets = [];
        foreach ($sets as $set => $features) {
            // Check the Set has a unique name
            if (in_array($set, $validatedSets))
                throw new \LogicException(sprintf('A set with name "%s" already exists. Each set MUST have a unique name.', $set));

            // Add the set to the list of validated sets for the validation of the name of next sets
            $validatedSets[] = $set;

            // Validate the features in the Set
            $this->validateFeatures($set, $features['features']);
        }
        return true;
    }

    /**
     * @param string $set
     * @param array $features
     */
    private function validateFeatures(string $set, array $features)
    {
        $validatedFeatures = [];
        foreach ($features as $feature => $config) {
            // Check the Feature has a unique name in the Set
            if (in_array($feature, $validatedFeatures))
                throw new \LogicException(sprintf('A feature with name "%s" already exists in the Set "%s". Each feature MUST have a unique name in any given Set.', $feature, $set));

            // Add the set to the list of validated sets for the validation of the name of next sets
            $validatedFeatures[] = $feature;

            // Validate the features in the Set
            $this->validateFeatureConfig($set, $feature, $config);
        }
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array $config
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
     * @param array $config
     */
    private function validateBoolean(string $set, string $feature, array $config)
    {
        // If not set in the configuration, $config['price'] is automatically set as an empty array
        $this->validateRecurringPrice($set, $feature . '.price', $config['price']);
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array $config
     */
    private function validateCountable(string $set, string $feature, array $config)
    {
        // If not set in the configuration, $config['price'] is automatically set as an empty array
        $this->validateRecurringPrice($set, $feature . '.packs.', $config['unitary_price']);

        // Validate the packages
        $this->validatePackages($set, $feature, $config['packs'], 'subscription');
    }

    /**
     * @param string $set
     * @param string $feature
     * @param array $config
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
     * @param array $price
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
        if (false === key_exists($currency, Currency::getCurrencies())) {
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
     * @param array $subscriptions
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
     * @param array $packs
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
                    case 'subscription':
                        // Validate the price
                        $this->validateRecurringPrice($set, $feature . '.packs.' . $numOfUnits, $price);
                        break;
                    case 'unatantum':
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
     * @param array $price
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
     * Processes all the configured features Sets.
     * @param array $sets
     * @return array
     */
    private function processSets(array $sets) : array
    {
        foreach ($sets as $set => $features) {
            $sets[$set]['features'] = $this->processFeatures($features['features']);
        }

        return $sets;
    }

    /**
     * @param array $features
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
     * @return array
     */
    private function processBoolean(array $config)
    {
        unset(
            $config['free_amount'],
            $config['cumulable'],
            $config['free_recharge'],
            $config['unitary_price'],
            $config['packs']
        );

        return $config;
    }

    /**
     * @param array $config
     * @return array
     */
    private function processCountable(array $config)
    {
        unset(
            $config['enabled'],
            $config['price'],
            $config['free_recharge']
        );

        $config['prices'] = $this->processRecurringPrice($config['unitary_price']);
        $config['packs'] = $this->processPackages($config['packs'], 'subscription');

        return $config;
    }

    /**
     * @param array $config
     * @return array
     */
    private function processRechargeable(array $config)
    {
        unset(
            $config['enabled'],
            $config['free_amount'],
            $config['price']
        );

        $config['unitary_price'] = $this->processUnatantumPrice($config['unitary_price']);
        $config['packs'] = $this->processPackages($config['packs'], 'unatantum');

        return $config;
    }

    /**
     * @param array $prices
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
     * @return array
     */
    private function processPackages(array $packs, $subscriptionType)
    {
        foreach ($packs as $numOfUnits => $prices) {
            switch ($subscriptionType) {
                case 'subscription':
                    $packs[$numOfUnits] = $this->processRecurringPrice($prices);
                    break;
                case 'unatantum':
                    $packs[$numOfUnits] = $this->processUnatantumPrice($prices);
                    break;
            }
        }

        return $packs;
    }

    /**
     * @param array $prices
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
