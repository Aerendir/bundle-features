HOW TO USE THE JQuery `Cart.js`
-------------------------------

The SerendipityHQ Features Bundle comes with a built in cart that permits you to dynamically calulcate the price of chosen features so the User can know beforehand how much he will pay.

To use the cart you have to:

1. Include the `cart.js` script in your pages;
2. Render the form fields where the user can choose which features he want to enable/buy;
3. Prepare the overall template to show the information calculated by the Serendipity HQ Features Bundle.
 
## STEP 1: Include the `cart.js` script in your pages

Nothing complex here.

You have to simply include the script in your Javascripts:

    {% block javascripts %}
        {% javascripts
        '@AppBundle/Resources/public/js/jquery-1.11.3.min.js'
        '@SHQFeaturesBundle/Resources/public/js/Cart.js' 
        %}
    {% endblock %}

Now your pages will include the script that manages the features prices.

## STEP 2: Render the form fields

To render the fields in your pages that permits the User to choose which features he wants to buy, you have to create the form first.

Serendipity HQ Features Bundle provides a service that permits you to create the form automatically.

As you read in [Configuration](Configuration.md), each time you create a features set, the bundle automatically creates a `FeaturesManager` for it.

This is available in the container as a service.

So, in your controller, you have to do something like this:

```php

shq_features.manager.store.features

class StoreController extends Controller
{
    ...
    
    /**
     * @Route("/subscription", name="store_subscription")
     * @Template()
     *
     * @param Store   $store
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return array|Response
     */
    public function subscriptionAction(Store $store, Request $request)
    {
        // Here goes your business logic
        
        ...
        
        // Generate the URL to which the form has to submit data
        $actionUrl = $this->get('router')->generate('store_subscription');
        
        /** @var \Symfony\Component\Form\FormInterface $form */
        $form = $this->get('shq_features.manager.store.features')->getFeaturesFormBuilder($actionUrl, $this->getStore()->getSubscription());
        
        // Handle the request
        $form->handleRequest($request);
        
        // Check the form is submitted and data are valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Do your business logic with your new plan
        }
        
        // Return the data required by your view
        return [
            'form' => $form->createView(),
            'store'  => $store,
            'features' => $this->get('shq_features.manager.store.features')->getConfiguredFeatures()
        ];
    }
```

Now you have your features set available in your Twig templates.

Lets render the form fields!

In your Twig template, put this:

```
{% extends '::App/base.html.twig' %}

{% trans_default_domain "premium" %}

{% block title %}{% trans from "common" %}common.subscription.manage.meta.title{% endtrans %}{% endblock %}

{% block body %}
    ...
    {% set adsPrice = features.get('ads').getPrice('EUR', 'monthly') %}
    {% set adsInstantPrice = features.get('ads').getInstantPrice('EUR', 'monthly') %}
    Monthly price: {% if 0 == adsPrice.convertedAmount %}FREE{% else %}{{ adsPrice.convertedAmount|localizedcurrency('eur') }}{% endif %}.
    {% if store.subscription.isStillActive('ads') %}
        Activated until: {% if 0 == adsPrice.convertedAmount %}Forever, it's free!{% else %}{{ store.subscription.features.get('ads').activeUntil|localizeddate('long', 'none', locale) }}{% endif %}
    {% else %}
        You have to immediately pay {{ adsPrice.convertedAmount|localizedcurrency('eur') }}
    {% endif %}
    
    ...
{% endblock %}
```

Done: now your form shows the prices of your features!

At this point you probably will have more than one feature on your subscription page.

You customer would like to know how much it will pay in total after he activate the cosen features.

So, lets show him this.

In your Twig template, add this code:

```
...
<p>
    Your monthly total is:<br />
    € <span class="currencyMask total-gross-amount">0</span>
</p>
<p>
    You have to immediately pay:<br />
    € <span class="currencyMask total-gross-instant-amount">0</span>
</p>
```

Done: your cart is shown where you like!

## How does this work

The cart works reading the `data-*` attributes of the features.

This way it can get their prices and update the totals accordingly and show the price of the current selected feature.

**The only one thign to keep in consideration is that the price of `RechargeableFeature`s is ever the instant one.**

**As they are ever bought _una tantum_, their amount is ever shown in the instant amount to pay, as the recurring one is not affected by them.** 
