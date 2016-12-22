<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Traits\FeaturesManagerTrait;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesManagerInterface;
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
                'data' => $subscription->getFeatures(),
            ]);

        return $form;
    }
}
