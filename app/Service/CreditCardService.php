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

        // Validate Customer
        if (empty($customerId)) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");
            $result["field"] = 'customerId';
            return $result;
        }

        // validate currency
        if (empty($currency) || $value <= 0) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");
            $result["field"] = 'currency';
            return $result;
        }

        // Validate Value
        if ($value <= 0) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");
            $result["field"] = 'value';
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

    /**
     * update credit card info
     *
     * @param string $customerId
     * @param string $token
     *
     * @return array
     */
    function update(string $customerId, string $token): array
    {
        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            \Stripe\Customer::update($customerId, ['source' => $token]);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $stripeCode = property_exists($e, "stripeCode") ? $e->stripeCode : null;
            $errorLog = sprintf("Failed to update credit card info. customerId: %s, token: %s, message: %s, stripeCode: %s", $customerId, $token, $message, $stripeCode);
            $this->log(sprintf("[%s]%s", __METHOD__, $errorLog));
            $this->log($e->getTraceAsString());

            $result["error"] = true;
            $result["message"] = $message;
            $result["errorCode"] = $stripeCode;

            return $result;
        }

        $result = [
            'error' => false,
            'message' => null
        ];
        return $result;
    }

    /*
     * Return a list with all registered customers on Stripe
     * Documentation about the returned data can be found on
     * https://stripe.com/docs/api#list_customers
     *
     * @param string|null $startAfter A cursor for use in pagination.
     *                                $startAfter is an object ID that defines your place in the list.
     *                                For instance, if you make a list request and receive 100 objects,
     *                                ending with obj_foo, your subsequent call can include
     *                                $startAfter=obj_foo in order to fetch the next page of the list.
     *
     * @return array
     */
    public function listCustomers(string $startAfter = null)
    {
        $result = [
            "error"   => false,
            "message" => null,
            "customers" => []
        ];

        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        $options = array(
            "limit" => 10 // Process in blocks of 20 accounts
        );

        // If start after is set add as parameter
        if ($startAfter != null) {
            $options = am($options, ["starting_after" => $startAfter]);
        }

        try {
            // Get the customer list
            $response = \Stripe\Customer::all($options);

            // Set the array with format customer_id/customer data
            foreach ($response->data as $index => $value) {
                $result["customers"][$value->id] = $value;
                unset($response->data[$index]);
            }
            $result["hasMore"] = $response->has_more;
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
