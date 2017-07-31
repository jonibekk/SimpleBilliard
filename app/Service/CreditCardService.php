<?php
App::import('Service', 'AppService');

/**
 * Class ApiStripeService
 */
class CreditCardService extends AppService
{
    /**
     * Accept a credit card token and register it as new customer on Stripe.
     *
     * @param string      $token
     * @param string      $email
     * @param string|null $description
     *
     * @return array
     */
    public function registerCustomer(string $token, string $email, string $description = null): array
    {
        $result = [
            "error"   => false,
            "message" => null
        ];

        if (empty($token) || empty($email)) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");

            return $result;
        }

        $customer = array(
            "source"      => $token,
            "email"       => $email,
            "description" => $description
        );

        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        try {
            $response = \Stripe\Customer::create($customer);

            $result["customer_id"] = $response->id;
            $result["card"] = $response->sources->data[0];
        } catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }

            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }

        return $result;
    }

    /**
     * Delete customer from Stripe
     *
     * @param string $customerId
     *
     * @return array
     */
    public function deleteCustomer(string $customerId): array
    {
        $result = [
            "error"   => false,
            "message" => null
        ];

        if (empty($customerId)) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");

            return $result;
        }

        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        try {
            $customer = \Stripe\Customer::retrieve($customerId);
            $response = $customer->delete();

            $result["deleted"] = $response->deleted;
        } catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }

            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }

        return $result;
    }

    /**
     * Charge an existing customer from Stripe
     *
     * @param string $customerId
     * @param string $currency
     * @param float  $value
     * @param string $description
     *
     * @return array
     */
    public function chargeCustomer(string $customerId, string $currency, float $value, string $description)
    {
        $result = [
            "error"   => false,
            "message" => null
        ];

        if (empty($customerId) || empty($currency) || $value <= 0) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");

            return $result;
        }

        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $charge = [
            'customer' => $customerId,
            'amount' => $value,
            'currency' => $currency,
            'description' => $description
        ];

        try {
            $response = \Stripe\Charge::create($charge);

            $result["success"] = $response->paid;
            $result["paymentId"] = $response->id;
            $result["status"] = $response->status;
            $result["paymentData"] = $response;
        } catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }

            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }

        return $result;
    }
}
