<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\FeaturesFactory;

/**
 * {@inheritdoc}
 */
class SubscribedFeaturesCollection extends AbstractFeaturesCollection
{
    const KIND = 'subscribed';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $elements = [])
    {
        FeaturesFactory::setKind(self::KIND);
        parent::__construct($elements);
    }
}
