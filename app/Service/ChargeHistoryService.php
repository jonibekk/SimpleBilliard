<?php
App::import('Service', 'AppService');

use Goalous\Model\Enum as Enum;

/**
 * Class ChargeHistoryService
 */
class ChargeHistoryService extends AppService
{
    /**
     * return true if latest team creditcard payment failed
     *
     *
     * @param int $teamId
     *
     * @return bool
     */
    public function isLatestChargeFailed(int $teamId): bool
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        $teamPaymentSetting = $PaymentSetting->getCcByTeamId($teamId);
        if (empty($teamPaymentSetting)) {
            // no payment settings, no failed
            return false;
        }
        if (!(new Enum\PaymentSetting\Type(intval($teamPaymentSetting['PaymentSetting']['type'])))
            ->equals(Enum\PaymentSetting\Type::CREDIT_CARD())) {
            // if team paying type is not creditcard
            return false;
        }

        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
        $chargeHistory = $ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        if (empty($chargeHistory)) {
            return false;
        }

        return (new Enum\ChargeHistory\ResultType(intval($chargeHistory['ChargeHistory']['result_type'])))
            ->equals(Enum\ChargeHistory\ResultType::FAIL());
    }
}
