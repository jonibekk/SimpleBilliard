<?php
App::import('Service', 'AppService');
App::import('Service', 'CreditCardService');
App::uses('PaymentSetting', 'Model');
App::uses('Team', 'Model');
App::uses('CreditCard', 'Model');

/**
 * Class PaymentService
 */
class PaymentService extends AppService
{

    /**
     * Validate for Create
     *
     * @param $data
     *
     * @return array|bool
     */
    public function validateCreate($data)
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");

        // Validates model
        $PaymentSetting->set($data);
        $PaymentSetting->validate = am($PaymentSetting->validate, $PaymentSetting->validateCreate);
        if (!$PaymentSetting->validates()) {
            return $PaymentSetting->_validationExtract($PaymentSetting->validationErrors);
        }

        // Validate if team exists
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $teamId = Hash::get($data, 'team_id');
        if ($Team->exists($teamId) === false) {
            $PaymentSetting->invalidate('team_id', __("Not exist"));
            return $PaymentSetting->_validationExtract($PaymentSetting->validationErrors);
        }

        // Validate Credit card token
        $paymentType = Hash::get($data, 'type');
        if ($paymentType == $PaymentSetting::PAYMENT_TYPE_CREDIT_CARD) {
            $token = Hash::get($data, 'token');

            if (empty($token)) {
                $PaymentSetting->invalidate('token', __("Input is required."));
                return $PaymentSetting->_validationExtract($PaymentSetting->validationErrors);
            }
        }

        // TODO: Validate INVOICE data

        return true;
    }

    /**
     * Create a payment settings as its related credit card
     *
     * @param $data
     * @param $customerCode
     *
     * @return bool
     * @throws Exception
     */
    public function registerCreditCardPayment($data, $customerCode, $userId)
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");

        try {
            // Create PaymentSettings
            $PaymentSetting->begin();
            if (!$PaymentSetting->save($data)) {
                $PaymentSetting->rollback();
                throw new Exception(sprintf("Failed create payment settings. data:%s", var_export($data, true)));
            }
            $paymentSettingId = $PaymentSetting->getLastInsertID();

            // Create CreditCards
            $creditCardData = [
                'team_id'            => $data['team_id'],
                'payment_setting_id' => $paymentSettingId,
                'customer_code'      => $customerCode
            ];

            $CreditCard->begin();
            if (!$CreditCard->save($creditCardData)) {
                $CreditCard->rollback();
                $PaymentSetting->rollback();
                throw new Exception(sprintf("Failed create credit card. data:%s", var_export($data, true)));
            }

            // Save snapshot
            /** @var PaymentSettingChangeLog $PaymentSettingChangeLog */
            $PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
            $PaymentSettingChangeLog->saveSnapshot($paymentSettingId, $userId);

            // Commit changes
            $PaymentSetting->commit();
            $CreditCard->commit();
        } catch (Exception $e) {
            $CreditCard->rollback();
            $PaymentSetting->rollback();
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * Apply Credit card charge for a specified team.
     *
     * @param int         $teamId
     * @param int         $chargeType
     * @param int         $usersCount
     * @param string      $description
     *
     * @return array
     */
    public function applyCreditCardCharge(int $teamId, int $chargeType, int $usersCount, string $description)
    {
        $result = [
            'error'   => false,
            'errorCode' => 200,
            'message' => null
        ];

        // Validate payment type
        if (!($chargeType == PaymentSetting::CHARGE_TYPE_MONTHLY_FEE ||
            $chargeType == PaymentSetting::CHARGE_TYPE_USER_INCREMENT_FEE ||
            $chargeType == PaymentSetting::CHARGE_TYPE_USER_ACTIVATION_FEE)) {

            $result['error'] = true;
            $result['field'] = 'chargeType';
            $result['message'] = __('Parameter is invalid.');
            $result['errorCode'] = 400;
            return $result;
        }

        // Validate user count
        if ($usersCount <= 0) {
            $result['error'] = true;
            $result['field'] = 'usersCount';
            $result['message'] = __('Parameter is invalid.');
            $result['errorCode'] = 400;
            return $result;
        }

        // Get Payment settings
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        $paymentSettings = $Team->PaymentSetting->getByTeamId($teamId);
        if (!$paymentSettings) {
            $result['error'] = true;
            $result['message'] = __('Payment settings does not exists.');
            $result['errorCode'] = 500;

            return $result;
        }

        // Get credit card settings
        if (empty(Hash::get($paymentSettings, 'CreditCard')) || !isset($paymentSettings['CreditCard'][0])) {
            $result['error'] = true;
            $result['message'] = __('Credit card settings does not exists.');
            $result['errorCode'] = 500;

            return $result;
        }
        $creditCard = $paymentSettings['CreditCard'][0];
        $customerId =  Hash::get($creditCard, 'customer_code');
        $amountPerUser = Hash::get($paymentSettings, 'PaymentSetting.amount_per_user');
        $currency = Hash::get($paymentSettings, 'PaymentSetting.currency');
        $currencyName =  $currency== PaymentSetting::CURRENCY_CODE_JPY ? PaymentSetting::CURRENCY_JPY : PaymentSetting::CURRENCY_USD;

        // Apply the user charge on Stripe
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        $totalAmount = $amountPerUser * $usersCount;
        $charge = $CreditCardService->chargeCustomer($customerId, $currencyName, $totalAmount, $description);

        // Save charge history
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');

        if ($charge['error'] === true) {
            $resultType = ChargeHistory::TRANSACTION_RESULT_ERROR;
            $result['success'] = false;
        }  else if ($charge['success'] === true) {
            $resultType = ChargeHistory::TRANSACTION_RESULT_SUCCESS;
            $result['success'] = true;
        }  else {
            $resultType = ChargeHistory::TRANSACTION_RESULT_FAIL;
            $result['success'] = false;
        }
        $result['resultType'] = $resultType;

        $historyData = [
            'team_id' => $teamId,
            'payment_type' => PaymentSetting::PAYMENT_TYPE_CREDIT_CARD,
            'charge_type' => $chargeType,
            'amount_per_user' => $amountPerUser,
            'total_amount' => $totalAmount,
            'charge_users' => $usersCount,
            'currency' => $currency,
            'charge_datetime' => time(),
            'result_type' => $resultType,
            'max_charge_users' => $usersCount
        ];

        try {
            // Create Charge history
            $ChargeHistory->begin();

            if (!$ChargeHistory->save($historyData)) {
                $ChargeHistory->rollback();
                throw new Exception(sprintf("Failed create charge history. data:%s", var_export($historyData, true)));
            }

            if (isset($charge['paymentData'])) {
                $result['paymentData'] = $charge['paymentData'];
            }
            $ChargeHistory->commit();
        } catch (Exception $e) {
            $ChargeHistory->rollback();
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());

            $result["error"] = true;
            $result["message"] = $e->getMessage();
            $result['errorCode'] = 500;

            if (property_exists($e, "stripeCode")) {
                $result["errorCode"] = $e->stripeCode;
            }
        }
        return $result;
    }
}
