<?php
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagClient');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagKey');

class PaymentTiming
{
    /*
     * check if it is in new payment peroid
     *
     */

    public function checkIfPaymentTiming(int $teamId): bool
    {
        /* get payment flag */
        $redisClient = new PaymentFlagClient();
        $switchFlagKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_FLAG_NAME);
        $switchStartDateKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_START_DATE_NAME);
        $switchFlag = $redisClient->read($switchFlagKey);
        $switchStartDate = $redisClient->read($switchStartDateKey);
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        if (!$Team->isPaidPlan($teamId)) {
            return true;
        }
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        $paymentSetting = $PaymentSetting->getUnique($teamId);
        $paymentBaseDay = Hash::get($paymentSetting, 'payment_base_day');

        if (empty($switchFlag) or intval($switchFlag) == 1 and empty($switchStartDate)){
            $validationErrors = 'Switch Value Error!';
            throw new Exception(sprintf("Failed to get correct switch value. teamId:%s validationErrors:%s",
                $teamId, var_export($validationErrors, true)));
        }

        if (intval($switchFlag) == 2 or intval($switchFlag) == 1 and !empty($switchStartDate) and PaymentFlagClient::isInPeriod($paymentBaseDay, $switchStartDate, $teamId)) {
            return true;
        }
        return false;

    }

}
