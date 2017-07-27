<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

/**
 * Implemented by features or packages that can be free.
 *
 * A Feature or a Pack is free if its price property is empty.
 */
interface CanBeFreeInterface
{
    /**
     * @return bool
     */
    public function isFree() : bool;
}
