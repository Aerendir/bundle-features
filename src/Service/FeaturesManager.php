<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Traits\FeaturesManagerTrait;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesManagerInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Traits\SubscriptionTrait;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use SerendipityHQ\Bundle\FeaturesBundle\Form\Type\FeaturesType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Contains method to manage features plans.
 */
class FeaturesManager implements FeaturesManagerInterface
{
    use FeaturesManagerTrait;

    /**
     * @param string $subscriptionInterval
     * @param CurrencyInterface $currency
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return array
     */
    public function buildDefaultSubscriptionFeatures(string $subscriptionInterval, CurrencyInterface $currency = null)
    {
        $activeUntil = SubscriptionTrait::calculateActiveUntil($subscriptionInterval);
        $features = [];

        /**
         * @var string $name
         * @var FeatureInterface $details
         */
        foreach ($this->getFeaturesHandler()->getFeatures(FeatureInterface::BOOLEAN) as $name => $details) {
            $features[$name] = [
                'active_until' => false === $this->getFeaturesHandler()->getDefaultStatusForBoolean($name) ? null : $activeUntil,
                'type' => $details->getType(),
                'enabled' => $details->isEnabled()
            ];
        }

        return $features;
    }

    /**
     * @param string $actionUrl
     * @param SubscriptionInterface $subscription
     * @param array $options
     * @return FormBuilderInterface
     */
    public function getFeaturesFormBuilder(string $actionUrl, SubscriptionInterface $subscription, array $options = [])
    {
        $form = $this->getFormFactory()->createBuilder(FormType::class, [
            'action' => $actionUrl,
            'method' => 'POST',
        ])
            ->add('features', FeaturesType::class, [
                'data' => $subscription->getFeatures()->toArray(),
                'features_handler' => $this->getFeaturesHandler()
            ]);

        return $form;
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return FeaturesManagerInterface
     */
    public function setSubscription(SubscriptionInterface $subscription) : FeaturesManagerInterface
    {
        $this->getFeaturesHandler()->setSubscription($subscription);

        return $this;
    }
}
