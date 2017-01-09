<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

/**
 * Concrete implementation of an HasQuantitiesInterface.
 */
trait HasQuantitiesProperty
{
    /** @var  int $initialQuantity The initial amount of units */
    //private $initialQuantity = 0;

    /** @var  int $remainedQuantity The amount of remained units */
    private $remainedQuantity = 0;

    /**
     * {@inheritdoc}
     */
    /*
    public function getInitialQuantity() : int
    {
        return $this->initialQuantity;
    }
    */

    /**
     * {@inheritdoc}
     */
    public function getRemainedQuantity() : int
    {
        return $this->remainedQuantity;
    }

    /**
     * @param array $details
     */
    private function setQuanity(array $details)
    {
        //$this->initialQuantity = $details['initial_quantity'] ?? 0;

        //$this->remainedQuantity = $this->initialQuantity;
        if (isset($details['remained_quantity'])) {
            $this->remainedQuantity = $details['remained_quantity'];
        }
    }
}
