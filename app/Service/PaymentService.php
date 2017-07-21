<?php
App::import('Service', 'AppService');
App::uses('Team', 'Model');
App::uses('PaymentSetting', 'Model');
App::uses('CreditCard', 'Model');

class PaymentService extends  AppService
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
    public function createCreditCardPayment($data, $customerCode)
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
                'team_id' => $data['team_id'],
                'payment_setting_id' => $paymentSettingId,
                'customer_code' => $customerCode
            ];

            $CreditCard->begin();
            if (!$CreditCard->save($creditCardData)) {
                $CreditCard->rollback();
                $PaymentSetting->rollback();
                throw new Exception(sprintf("Failed create credit card. data:%s", var_export($data, true)));
            }

            $PaymentSetting->commit();
            $CreditCard->commit();
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }
}