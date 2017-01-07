<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

/**
 * Concrete implementetion of the CanBeFreeInterface.
 */
trait CanBeFreeProperty
{
    /**
     * @return bool
     */
    public function isFree() : bool
    {
        return empty($this->prices);
    }
}
