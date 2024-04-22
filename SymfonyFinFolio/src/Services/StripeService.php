<?php

namespace App\Services;


use App\Entity\Commande;

class StripeService
{
    private $privateKey;

    public function __construct()
    {
        if($_ENV['APP_ENV']  === 'dev') {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
    }

    /**
     * @param string $prix
     * @return \Stripe\PaymentIntent
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function paymentIntent(string $prix)
    {
        \Stripe\Stripe::setApiKey($this->privateKey);

        return \Stripe\PaymentIntent::create([
            'amount' => $prix * 100,
            'currency' => Commande::DEVISE, // Utilisation de l'euro comme devise
            'payment_method_types' => ['card']
        ]);
    }

    public function paiement(
        $amount,
        $currency,
        $description,
        array $stripeParameter
    )
    {
        \Stripe\Stripe::setApiKey($this->privateKey);
        $payment_intent = null;

        if(isset($stripeParameter['stripeIntentId'])) {
            $payment_intent = \Stripe\PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
        }

        if($stripeParameter['stripeIntentStatus'] === 'succeeded') {
            //TODO
        } else {
            $payment_intent->cancel();
        }

        return $payment_intent;
    }

    /**
     * @param array $stripeParameter
     * @param string $prix
     * @param string $utilite
     * @return \Stripe\PaymentIntent|null
     */
    public function stripe(array $stripeParameter, string $prix,string $utilite)
    {
        return $this->paiement(
            $prix * 100,
            Commande::DEVISE,
            $utilite,
            $stripeParameter
        );
    }
}
