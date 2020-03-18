<?php
App::import('Service', 'AppService');

/**
 * Class ApiStripeService
 */
class CreditCardService extends AppService
{
    const CACHE_TTL_SECONDS_TEAM_CREDIT_CARD_EXPIRE_DATE = 7 * 86400;

    public function retrieveToken(string $token)
    {
        $result = [
            "error"   => false,
            "message" => null
        ];

        if (empty($token)) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");
            return $result;
        }

        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        try {
            $response = \Stripe\Token::retrieve($token);
            $result['creditCard'] = $response->card;
        } catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }

            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
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
            // Try to get the credit card from the list on customer object
            // will not be necessary to call $response->sources->retrieve()
            // because after creation the customer will not have a long
            // list of sources.
            $defaultSource = $response->default_source;
            foreach ($response->sources->data as $source) {
                if ($source->id == $defaultSource) {
                    $result["card"] = $source;
                    break;
                }
            }
            // Check if the the card was acquired
            // This should not happen in any case. Logging an Emergency
            if (!isset($result["card"])) {
                CakeLog::emergency(sprintf("[%s] Customer credit card not acquired. customer_id: %s, sourceId: %s",
                    __METHOD__, $response->id, $defaultSource));

                $result["error"] = true;
                $result["message"] = __("An error occurred while processing.");
                return $result;
            }
        } catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }

            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
        }

        return $result;
    }

    /**
     * get credit card expire datetime from cache or Stripe api
     *
     * @param int $teamId
     *
     * @return GoalousDateTime|null
     */
    public function getExpirationDateTimeOfTeamCreditCard(int $teamId)
    {
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        $expiration = $CreditCardService->getExpirationDateTimeOfTeamCreditCardFromCache($teamId);
        if ($expiration['expire'] instanceof GoalousDateTime) {
            return $expiration['expire'];
        }
        if ($expiration['error']) {
            // cached error on redis
            return null;
        }

        // no redis cache, fetch card expiration from StripeAPI
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        $customerId = $CreditCard->getCustomerCode($teamId);

        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        try {
            $response = $this->retrieveCreditCard($customerId);
            if ($response['error'] || is_null($response['creditCard'])) {
                throw new RuntimeException('credit card info not exists');
            }
            $card = $response['creditCard'];
            $CreditCardService->cacheTeamCreditCardExpiration([
                'error' => false,
                'year'  => $card['exp_year'] ?? 0,
                'month' => $card['exp_month'] ?? 0,
            ], $teamId);
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            $CreditCardService->cacheTeamCreditCardExpiration([
                'error' => true,
                'year'  => 0,
                'month' => 0,
            ], $teamId);
        }
        return $CreditCardService->getExpirationDateTimeOfTeamCreditCardFromCache($teamId)['expire'];
    }

    /**
     * cache team credit card expiration data by array
     *
     * @param array $data
     * @param int   $teamId
     */
    public function cacheTeamCreditCardExpiration(array $data, int $teamId)
    {
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        $keyRedisCache = $CreditCard->getCacheKey(CACHE_KEY_TEAM_CREDIT_CARD_EXPIRE_DATE, false, null, $teamId);
        CakeLog::info("cache credit card expiration date to redis: {$keyRedisCache}");
        Cache::set('duration', self::CACHE_TTL_SECONDS_TEAM_CREDIT_CARD_EXPIRE_DATE, 'user_data');
        Cache::write($keyRedisCache, msgpack_pack($data), 'user_data');
    }

    /**
     * get team credit card expiration date by GoalousDateTime
     *
     * @param int $teamId
     *
     * @return array
     * [
     *     'error'  => bool,
     *     'expire' => GoalousDateTime|null,
     * ]
     */
    public function getExpirationDateTimeOfTeamCreditCardFromCache(int $teamId)
    {
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        $keyRedisCache = $CreditCard->getCacheKey(CACHE_KEY_TEAM_CREDIT_CARD_EXPIRE_DATE, false, null, $teamId);
        $cachedCreditCardExpireData = Cache::read($keyRedisCache, 'user_data');
        if (false === $cachedCreditCardExpireData) {
            // no cache, returning null, this is not error pattern
            return [
                'error'  => false,
                'expire' => null,
            ];
        }
        // cached as error, stripe api is failed
        $expireDates = msgpack_unpack($cachedCreditCardExpireData);
        if ($expireDates['error']) {
            return [
                'error'  => true,
                'expire' => null,
            ];
        }
        // credit card expire date cached successfully
        return [
            'error'  => false,
            'expire' => self::getRealExpireDateTimeFromCreditCardExpireDate($expireDates['year'],
                $expireDates['month']),
        ];
    }

    /**
     * get "real" credit card expire datetime by GoalousDateTime
     * e.g. if StripeApi return
     *  - exp_year = 2018
     *  - exp_month = 8
     * the card "real" expire datetime is "9/1/2018 00:00:00"
     * in this example, this method returns
     *     new GoalousDateTime("9/1/2018 00:00:00");
     *
     * @param int $year
     * @param int $month
     *
     * @return GoalousDateTime
     */
    public function getRealExpireDateTimeFromCreditCardExpireDate(int $year, int $month): GoalousDateTime
    {
        return GoalousDateTime::create(
            $year,
            $month,
            1, 0, 0, 0
        )->addMonth(1);
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

            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
        }

        return $result;
    }

    /**
     * Charge an existing customer from Stripe
     * TODO.Payment: method argument $currencyName is unnecessary. because $currencyName is got by payment_settings.currency
     *
     * @param string $customerId
     * @param string $currencyName
     * @param float  $amount
     * @param string $description
     * @param array  $metaData
     *
     * @return array
     */
    public function chargeCustomer(
        string $customerId,
        string $currencyName,
        float $amount,
        string $description,
        array $metaData = []
    ) {
        $result = [
            "error"               => false,
            "message"             => null,
            // does request to stripe is succeed
            "isApiRequestSucceed" => false,
        ];

        // Validate Customer
        if (empty($customerId)) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");
            $result["field"] = 'customerId';
            return $result;
        }

        // validate currency
        if (empty($currencyName)) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");
            $result["field"] = 'currency';
            return $result;
        }

        // Validate Value
        if ($amount <= 0) {
            $result["error"] = true;
            $result["message"] = __("Parameter is invalid.");
            $result["field"] = 'value';
            return $result;
        }

        // check if need charge
        if ($this->checkIfNotNeedCharge($amount, $currencyName)){
            $result["error"] = false;
            $result["isApiRequestSucceed"] = true;
            $result["success"] = true;
            $result["paymentId"] = null;
            $result["noCharge"] = true;
            return $result;
        }

        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        // Stripe specification
        // Ref: https://stripe.com/docs/currencies#zero-decimal
        if ($currencyName === PaymentSetting::CURRENCY_USD) {
            // Because of PHP float bug, we should use this value as string
            // ref) http://php.net/manual/ja/language.types.float.php
            //      http://www.psi-net.co.jp/blog/?p=277
            $amount = (string)($amount * 100);
        } else {
            $amount = (int)$amount;
        }

        $charge = [
            'customer'    => $customerId,
            'amount'      => $amount,
            'currency'    => $currencyName,
            'description' => $description,
            'metadata'    => $metaData
        ];

        try {
            $response = \Stripe\Charge::create($charge);

            $result["isApiRequestSucceed"] = true;
            $result["success"] = $response->paid;
            $result["paymentId"] = $response->id;
            $result["status"] = $response->status;
            $result["paymentData"] = $response->jsonSerialize();
        } catch (\Stripe\Error\Card $e) {
            /**
             * in this catch case, API request is success
             * but credit card can not use for charge
             *
             * @see https://stripe.com/docs/api#error_handling
             */
            $jsonBody = $e->getJsonBody();
            // $jsonBody['error']['charge'] containing stripe charge id when failed
            $paymentId = $jsonBody['error']['charge'] ?? '';
            $result["isApiRequestSucceed"] = true;
            $result["success"] = false;
            $result["error"] = true;
            $result["message"] = $e->getMessage();
            $result["paymentId"] = $paymentId;

            CakeLog::notice(sprintf("[%s]%s  data:%s",
                __METHOD__,
                $e->getMessage(),
                AppUtil::varExportOneLine(compact('charge'))
            ));
            CakeLog::notice($e->getTraceAsString());
        } catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }

            CakeLog::emergency(sprintf("[%s]%s  data:%s",
                __METHOD__,
                $e->getMessage(),
                AppUtil::varExportOneLine(compact('charge')
                )
            ));
            CakeLog::emergency($e->getTraceAsString());
        }

        return $result;
    }

    /**
     * update credit card info
     *
     * @param string $customerId
     * @param string $token
     * @param int    $teamId
     *
     * @return array
     */
    public function updateCreditCard(string $customerId, string $token, int $teamId): array
    {
        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            \Stripe\Customer::update($customerId, ['source' => $token]);

            $CreditCard = ClassRegistry::init("CreditCard");
            $keyRedisCache = $CreditCard->getCacheKey(CACHE_KEY_TEAM_CREDIT_CARD_EXPIRE_DATE, false, null, $teamId);
            Cache::delete($keyRedisCache, 'user_data');
        } catch (Exception $e) {
            $message = $e->getMessage();
            $stripeCode = property_exists($e, "stripeCode") ? $e->stripeCode : null;
            $errorLog = sprintf("Failed to update credit card info. customerId: %s, token: %s, message: %s, stripeCode: %s",
                $customerId, $token, $message, $stripeCode);
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $errorLog));
            CakeLog::emergency($e->getTraceAsString());

            $result["error"] = true;
            $result["message"] = $message;
            $result["errorCode"] = $stripeCode;

            return $result;
        }

        $result = [
            'error'   => false,
            'message' => null
        ];
        return $result;
    }

    /**
     * Retrieve a single customer from Stripe
     *
     * @param string $customerId
     *
     * @return array
     */
    public function retrieveCustomer(string $customerId)
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
            $response = \Stripe\Customer::retrieve($customerId);

            // Check for deleted customer
            if ($response->deleted === true) {
                $result["error"] = true;
                $result["message"] = __("Not found");
                $result["errorCode"] = 404;
                return $result;
            }

            $result['customer'] = $response;
        } catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }

            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
        }

        return $result;
    }

    /**
     * Retrieve the default credit card from given customer.
     * If the default credit card is not stored on customer object
     * (maybe due a large number of registered credit cards), a
     * new api call will be made to Stripe.
     *
     * @param string $customerId
     *
     * @return array
     */
    public function retrieveCreditCard(string $customerId)
    {
        // Retrieve the customer
        $response = $this->retrieveCustomer($customerId);
        if ($response['error'] === true) {
            return $response;
        }

        $result = [
            "error"   => false,
            "message" => null
        ];
        $defaultSource = $response['customer']->default_source;

        // Try to get the credit card from the list on customer object
        foreach ($response['customer']->sources->data as $source) {
            if ($source->id == $defaultSource) {
                $result['creditCard'] = $source;
                return $result;
            }
        }

        // Call retrieve api otherwise
        try {
            $creditCard = $response['customer']->sources->retrieve($defaultSource);
            $result['creditCard'] = $creditCard;
            return $result;
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            return [
                'error'   => true,
                'message' => __('Credit card settings does not exist.'),
            ];
        }
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
            "error"     => false,
            "message"   => null,
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
        } catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }

            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
        }
        return $result;
    }

    /*
     * Return a list with all registered charges on Stripe filtering specifying created range.
     * Documentation about the returned data can be found on
     * https://stripe.com/docs/api#list_charges
     *
     * @return array
     */
    /**
     * @param int    $startTimestamp
     * @param int    $endTimestamp
     * @param string $customerCode
     *
     * @return array
     * @throws Exception
     */
    public function findChargesByCreatedRange(int $startTimestamp, int $endTimestamp, string $customerCode)
    {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        $options = array(
            "created[gte]" => $startTimestamp, // Process in blocks of 20 accounts
            "created[lte]" => $endTimestamp,
            "customer" => $customerCode
        );
        try {
            // Get the customer list
            $response = \Stripe\Charge::all($options);
        } catch (Exception $e) {
            throw $e;
        }
        return $response->data;
    }

    /*
     * check if amount need to be charged
     * Documentation about the returned data can be found on
     * https://stripe.com/docs/currencies
     *
     * @return bool
     */
    /**
     * @param float  $amount
     * @param string $currencyName
     *
     * @return bool
     */
    public function checkIfNotNeedCharge(float $amount, string $currencyName) : bool
    {
        $currencyMap = array(
            PaymentSetting::CURRENCY_JPY => 50,
            PaymentSetting::CURRENCY_USD => 0.5,
        );

        return $amount < $currencyMap[$currencyName];
    }
}
