<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;


use AppBundle\Entity\StoreSubscription;
use SerendipityHQ\Bundle\FeaturesBundle\Entity\Subscription;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Traits\FeaturesManagerTrait;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesManagerInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Traits\SubscriptionTrait;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;
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
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return array
     */
    public function buildDefaultSubscriptionFeatures(string $subscriptionInterval, Currency $currency = null)
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
     * @return array
     */
    public function getPrices()
    {
        $return = [];

        // Process boolean features
        foreach ($this->getFeaturesHandler()->getFeatures() as $feature => $details) {
            dump($feature, $details);
            // Process prices
            /*
            foreach ($details['price'] as $currency => $prices) {
                $amountMonth = $details['enabled'] ? 0 : $prices['month'];
                $amountYear = $details['enabled'] ? 0 : $prices['year'];
                $return[$feature][$currency]['month'] = new Money(['amount' => $amountMonth, 'currency' => new Currency($currency)]);
                $return[$feature][$currency]['year'] = new Money(['amount' => $amountYear, 'currency' => new Currency($currency)]);
                $instantMont = $this->calculateInstantPrice($this->getSubscription(), $feature);
                $return[$feature][$currency]['instantMonth'] = new Money(['amount' => $instantMont, 'currency' => new Currency($currency)]);
            }
            */
        }
        die;

        return $return;
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param string            $feature
     *
     * @return int
     */
    private function calculateInstantPrice(SubscriptionInterface $subscription, $feature)
    {
        if (null !== $subscription && ! $subscription instanceof SubscriptionInterface) {
            throw new \InvalidArgumentException('You have to pass a Subscription as first parameter.');
        }

        $subscriptionInterval = (null === $subscription)
            // By default set the monthly interval
            ? 1
            // Else get the chosen interval
            : $subscription->getInterval();

        $price = $this->getFeaturesHandler()->getPriceForBoolean(
            $feature, $subscription->getCurrency(), $subscriptionInterval
        );

        // If a subscription doesn't already exist or if it was created today
        if (null === $subscription || ($subscription->getCreatedOn()->format('Y-m-d') === (new \DateTime())->format('Y-m-d'))) {
            // ...the user has never paid, so he has no remaining days of subscription and has to pay the full price
            return $price->getAmount();
        }

        // Our ideal month is ever of 31 days
        $daysInInterval = 0;
        if (1 === $subscriptionInterval) {
            $daysInInterval = 31;
        }

        // Our ideal year is ever of 365 days
        elseif (12 === $subscriptionInterval) {
            $daysInInterval = 365;
        }

        $pricePerDay = (int) floor($price->getAmount() / $daysInInterval);

        // Calculate the remaining days
        $remainingDays = $subscription->getNextPaymentOn()->diff(new \DateTime());

        $instantPrice = $pricePerDay * $remainingDays->days;

        return $instantPrice;
    }
}
