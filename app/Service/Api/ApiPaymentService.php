<?php
App::import('Service', 'AppService');

class ApiPaymentService extends AppService
{
    private $stripePublicKey = STRIPE_PK;
    private $stripeSecretKey = STRIPE_SK;

    /**
     * Generate a Token from Stripe API.
     * This method should not be used on production but only for test cases.
     * For production use stripe.js instead.
     *
     * @param string $cardNumber
     * @param string $cardHolder
     * @param int    $expireMonth
     * @param int    $expireYear
     * @param string $cvc
     *
     * @return array
     */
    public function createToken(string $cardNumber, string $cardHolder, int $expireMonth, int $expireYear, string $cvc): array
    {
        $result = [
            "error" => false,
            "message" => null
        ];

        $request = array(
            "card" => array(
                "number" => $cardNumber,
                "exp_month" => $expireMonth,
                "exp_year" => $expireYear,
                "cvc" => $cvc,
                "name" => $cardHolder
            )
        );

        // Use public key to create token
        \Stripe\Stripe::setApiKey($this->stripePublicKey);

        try {
            $response = \Stripe\Token::create($request);

            $result["token"] = $response->id;
        }
        catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }

        return $result;
    }

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
            "error" => false,
            "message" => null
        ];

        if (empty($token) || empty($email)) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");

            return $result;
        }

        $customer = array(
            "source" => $token,
            "email" => $email,
            "description" => $description
        );

        \Stripe\Stripe::setApiKey($this->stripeSecretKey);

        try {
            $response = \Stripe\Customer::create($customer);

            $result["customer_id"] = $response->id;
            $result["card"] = $response->sources->data[0];
        }
        catch (Exception $e) {
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