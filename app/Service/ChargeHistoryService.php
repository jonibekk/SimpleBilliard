<?php
App::import('Service', 'AppService');
App::import('Service', 'CreditCardService');
App::import('Service', 'PaymentService');
App::uses('ChargeHistory', 'Model');
App::import('View', 'Helper/TimeExHelper');

use Goalous\Enum as Enum;

/**
 * Class ChargeHistoryService
 */
class ChargeHistoryService extends AppService
{
    /**
     * return true if latest team creditcard payment failed
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
        if ($typePaymentSetting !== Enum\Model\PaymentSetting\Type::CREDIT_CARD) {
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
        return $resultTypeChargeHistory === Enum\Model\ChargeHistory\ResultType::FAIL;
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
        /** @var InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
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

        if ($history['ChargeHistory']['payment_type'] == Enum\Model\PaymentSetting\Type::CREDIT_CARD) {
            /** @var CreditCardService $CreditCardService */
            $CreditCardService = ClassRegistry::init('CreditCardService');

            $creditCard = $CreditCardService->retrieveCreditCard($history['CreditCard']['customer_code']);
            $creditCard = $creditCard['creditCard'];
            $history['CreditCard']['last4'] = $creditCard->last4;
            $history['PaymentSetting']['is_card'] = true;
        }

        $teamId = $history['ChargeHistory']['team_id'];
        switch ((int)$history['ChargeHistory']['charge_type']) {
            case Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE:
                $nextBaseDate = $PaymentService->getNextBaseDate($teamId, $history['ChargeHistory']['charge_datetime']);
                $prevBaseDate = $PaymentService->getPreviousBaseDate($teamId, $nextBaseDate);
                $prevBaseDate = $TimeEx->formatYearDayI18nFromDate($prevBaseDate);
                $endBaseDate = $TimeEx->formatYearDayI18nFromDate(AppUtil::dateYesterday($nextBaseDate));

                $history['ChargeHistory']['term'] = "$prevBaseDate - $endBaseDate";
                $history['ChargeHistory']['is_monthly'] = true;

                break;
            case Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF:
                $nextBaseDate = $PaymentService->getNextBaseDate($teamId, $history['ChargeHistory']['charge_datetime']);
                $endBaseDate = $TimeEx->formatYearDayI18nFromDate(AppUtil::dateYesterday($nextBaseDate));
                $history['ChargeHistory']['term'] = "$localChargeDate - $endBaseDate";
                $history['ChargeHistory']['is_monthly'] = true;
                break;
            case Enum\Model\ChargeHistory\ChargeType::RECHARGE:
                $paymentType = new Enum\Model\PaymentSetting\Type(intval($history['ChargeHistory']['payment_type']));
                switch ($paymentType->getValue()) {
                    case Enum\Model\PaymentSetting\Type::CREDIT_CARD:
                        $history['ChargeHistory']['recharge_history_ids'] = [$history['ChargeHistory']['reorder_charge_history_id']];
                        break;
                    case Enum\Model\PaymentSetting\Type::INVOICE:
                        $reorderTargetInvoiceHistory = $InvoiceHistory->getByChargeHistoryId($history['ChargeHistory']['id']);
                        if (empty($reorderTargetInvoiceHistory)) {
                            GoalousLog::emergency(
                                sprintf("Reorder target of invoice history doesn't exist. history_id:%s",
                                    $history['ChargeHistory']['id'])
                            );
                            break;
                        }
                        $reorderTargetOrderCode = Hash::get($reorderTargetInvoiceHistory,
                            'InvoiceHistory.reorder_target_code');
                        $reorderChargeHistories = $ChargeHistory->findByInvoiceOrderCode($teamId, $reorderTargetOrderCode);

                        $history['ChargeHistory']['recharge_history_ids'] = Hash::extract($reorderChargeHistories, '{n}.id');
                        break;
                }
        }
        return $history;
    }

    /**
     * Add charge history for recharging invoice
     *
     * @param int   $teamId
     * @param array $targetChargeHistories
     *
     * @return array
     * @throws Exception
     */
    public function addInvoiceRecharge(
        int $teamId,
        array $targetChargeHistories
    ): array {
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init('CampaignService');
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');

        // Sum up sub total and tax
        // Notice:
        //  it is unnecessary to consider decimal point.
        //  Because It can only be the amount of yen if payment type is invoice.
        $subTotal = 0;
        $tax = 0;
        foreach ($targetChargeHistories as $history) {
            $subTotal += $history['total_amount'];
            $tax += $history['tax'];
        }

        $purchaseTeam = $CampaignService->getPricePlanPurchaseTeam($teamId);
        $campaignTeamId = Hash::get($purchaseTeam, 'CampaignTeam.id');
        $pricePlanPurchaseId = Hash::get($purchaseTeam, 'PricePlanPurchaseTeam.id');

        $time = GoalousDateTime::now()->getTimestamp();

        // 履歴保存
        $historyData = [
            'team_id'                     => $teamId,
            'payment_type'                => Enum\Model\PaymentSetting\Type::INVOICE,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::RECHARGE,
            'amount_per_user'             => !empty($pricePlanPurchaseId) ? 0 : PaymentService::AMOUNT_PER_USER_JPY,
            'total_amount'                => $subTotal,
            'tax'                         => $tax,
            'charge_users'                => 0,
            'currency'                    => Enum\Model\PaymentSetting\Currency::JPY,
            'charge_datetime'             => $time,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'            => 0,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $pricePlanPurchaseId,
        ];
        $ChargeHistory->create();
        $ret = $ChargeHistory->save($historyData);
        $ret = Hash::get($ret, 'ChargeHistory');
        return $ret;
    }

}
