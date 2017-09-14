<?php
App::import('Service', 'AppService');
App::import('Service', 'CreditCardService');
App::import('Service', 'PaymentService');
App::uses('ChargeHistory', 'Model');
App::import('View', 'Helper/TimeExHelper');

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
        $typePaymentSetting = intval($teamPaymentSetting['PaymentSetting']['type']);
        if ($typePaymentSetting !== Enum\PaymentSetting\Type::CREDIT_CARD) {
            // if team paying type is not creditcard
            return false;
        }

        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
        $chargeHistory = $ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        if (empty($chargeHistory)) {
            return false;
        }

        $resultTypeChargeHistory = intval($chargeHistory['result_type']);
        return $resultTypeChargeHistory === Enum\ChargeHistory\ResultType::FAIL;
    }

    /**
     * Get a history by id
     *
     * @param int $historyId
     *
     * @return array
     */
    function getReceipt(int $historyId): array
    {
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');

        $history = $ChargeHistory->getForReceipt($historyId);
        if (empty($history)) {
            return [];
        }
        $history = $this->processForReceipt($history);
        return $history;
    }

    /**
     * Process history data for receipt
     *
     * @param array $history
     *
     * @return array
     */
    function processForReceipt(array $history): array
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        $TimeEx = new TimeExHelper(new View());

        $subTotal = $history['ChargeHistory']['total_amount'];
        $tax = $history['ChargeHistory']['tax'];
        $currency = $PaymentService->getCurrencyTypeByCountry($history['PaymentSetting']['company_country']);
        $localChargeDate = $TimeEx->formatYearDayI18n($history['ChargeHistory']['charge_datetime']);
        $history['ChargeHistory']['local_charge_date'] = $localChargeDate;
        $history['ChargeHistory']['sub_total_with_currency'] = $PaymentService->formatCharge($subTotal, $currency);
        $history['ChargeHistory']['tax_with_currency'] = $PaymentService->formatCharge($tax, $currency);
        $history['ChargeHistory']['total_with_currency'] = $PaymentService->formatCharge($subTotal + $tax, $currency);
        $history['PaymentSetting']['is_card'] = false;
        $history['ChargeHistory']['is_monthly'] = false;

        if ($history['ChargeHistory']['payment_type'] == Enum\PaymentSetting\Type::CREDIT_CARD) {
            /** @var CreditCardService $CreditCardService */
            $CreditCardService = ClassRegistry::init('CreditCardService');

            $creditCard = $CreditCardService->retrieveCreditCard($history['CreditCard']['customer_code']);
            $creditCard = $creditCard['creditCard'];
            $history['CreditCard']['last4'] = $creditCard->last4;
            $history['PaymentSetting']['is_card'] = true;
        }

        if ($history['ChargeHistory']['charge_type'] == Enum\ChargeHistory\ChargeType::MONTHLY_FEE) {
            $teamId = $history['ChargeHistory']['team_id'];
            $nextBaseDate = $PaymentService->getNextBaseDate($teamId, $history['ChargeHistory']['charge_datetime']);
            $prevBaseDate = $PaymentService->getPreviousBaseDate($teamId, $nextBaseDate);
            $prevBaseDate = $TimeEx->formatYearDayI18nFromDate($prevBaseDate);
            $endBaseDate = $TimeEx->formatYearDayI18nFromDate(AppUtil::dateYesterday($nextBaseDate));

            $history['ChargeHistory']['term'] = "$prevBaseDate - $endBaseDate";
            $history['ChargeHistory']['is_monthly'] = true;
        }
        return $history;
    }
}
