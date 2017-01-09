<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasQuantitiesInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedCountableFeatureInterface extends SubscribedFeatureInterface, SubscribedRecurringFeatureInterface, HasQuantitiesInterface
{
    /**
     * It is an integer when the feature is loaded from the database.
     *
     * Then, once called FeaturesManager::setSubscription(), this is transformed into the correspondent
     * ConfiguredFeaturePackInterface object.
     *
     * @return int|ConfiguredFeaturePackInterface
     */
    public function getSubscribedPack();
}
