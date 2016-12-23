<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Entity\Subscription;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;


abstract class AbstractFeature implements FeatureInterface
{
    /** @var  bool $enabled */
    private $enabled = false;

    /** @var  string $name */
    private $name;

    /**
     * @param array $details
     */
    public function __construct(string $name, array $details = [])
    {
        $this->name = $name;
        $this->disable();

        if (true === $details['enabled'])
            $this->enable();
    }

    public function disable() : FeatureInterface
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * @return FeatureInterface
     */
    public function enable() : FeatureInterface
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }
}
