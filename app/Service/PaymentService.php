<?php
App::import('Service', 'AppService');
App::import('Service', 'CreditCardService');
App::import('Service', 'InvoiceService');
App::import('Service', 'TeamService');
App::import('Service', 'CampaignService');
App::import('Service', 'ChargeHistoryService');
App::uses('PaymentSetting', 'Model');
App::uses('Team', 'Model');
App::uses('TransactionManager', 'Model');
App::uses('TeamMember', 'Model');
App::uses('CreditCard', 'Model');
App::uses('ChargeHistory', 'Model');
App::uses('CampaignTeam', 'Model');
App::uses('PricePlanPurchaseTeam', 'Model');
App::uses('AppUtil', 'Util');
App::uses('PaymentUtil', 'Util');
App::uses('GoalousDateTime', 'DateTime');

use Goalous\Enum as Enum;

/**
 * Class PaymentService
 */
class PaymentService extends AppService
{
    const AMOUNT_PER_USER_JPY = 1980;
    const AMOUNT_PER_USER_USD = 19;

    /**
     * Get payment setting by team id
     *
     * @param       $teamId
     *
     * @return array
     */
    public function get(int $teamId): array
    {
        // 既にDBからのデータ取得は行っているが情報が存在しなかった場合
        if (array_key_exists($teamId, self::$cacheList) && empty(self::$cacheList[$teamId])) {
            return [];
        }

        // 既にDBからのデータ取得は行っていて、かつ情報が存在している場合
        if (!empty(self::$cacheList[$teamId])) {
            // キャッシュから取得
            $data = self::$cacheList[$teamId];
            return $data;
        }

        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");

        $data = self::$cacheList[$teamId] = Hash::extract($PaymentSetting->findByTeamId($teamId), 'PaymentSetting');
        if (empty($data)) {
            return [];
        }

        // キャッシュ変数に保存
        self::$cacheList[$teamId] = $data;

        // データ拡張
        return $data;
    }

    /**
     * Clear cache
     */
    public function clearCachePaymentSettings()
    {
        self::$cacheList = [];
    }

    /**
     * Validate for Create
     *
     * @param $data
     *
     * @return array|bool
     */
    public function validateCreateCc($data)
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");

        // Validates model
        $PaymentSetting->set($data);
        $PaymentSetting->validate = am($PaymentSetting->validate, $PaymentSetting->validateCreate);
        if (!$PaymentSetting->validates()) {
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

        return true;
    }

    /**
     * Validate for Create
     *
     * @param $data
     *
     * @return array
     */
    public function validateCreateInvoice($data): array
    {
        $data = is_array($data) ? $data : [];
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init("Invoice");

        $allValidationErrors = [];

        // Validates PaymentSetting model
        $checkData = Hash::get($data, 'payment_setting') ?? [];
        $PaymentSetting->set($checkData);
        if (!$PaymentSetting->validates()) {
            $allValidationErrors = am(
                $allValidationErrors,
                $PaymentSetting->_validationExtract($PaymentSetting->validationErrors)
            );
        }

        // Validates Invoice model
        $checkData = Hash::get($data, 'invoice') ?? [];
        $Invoice->set($checkData);
        if (!$Invoice->validates()) {
            $allValidationErrors = am(
                $allValidationErrors,
                $Invoice->_validationExtract($Invoice->validationErrors)
            );
        }

        return $allValidationErrors;
    }

    /**
     * Get use days from current date to next payment base date
     *
     * @param int $teamId
     *
     * @return int
     */
    public function getUseDaysByNextBaseDate(int $teamId): int
    {
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init("TeamService");
        $timezone = $TeamService->getTeamTimezone($teamId);

        $localCurrentDate = GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d');
        $nextBaseDate = $this->getNextBaseDate($teamId);
        // Calc use days
        $diffDays = AppUtil::diffDays($localCurrentDate, $nextBaseDate);
        return $diffDays;
    }

    /**
     * Get next payment base date
     *
     * @param int  $teamId
     * @param null $timestamp
     *
     * @return string
     */
    public function getNextBaseDate(int $teamId, $timestamp = null): string
    {
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init("TeamService");
        $timezone = $TeamService->getTeamTimezone($teamId);
        if (empty($timestamp)) {
            $localCurrentDate = GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d');
        } else {
            $localCurrentDate = GoalousDateTime::createFromTimestamp($timestamp)->setTimeZoneByHour($timezone)
                ->format('Y-m-d');
        }

        list($y, $m, $d) = explode('-', $localCurrentDate);

        $paymentSetting = $this->get($teamId);
        $paymentBaseDay = Hash::get($paymentSetting, 'payment_base_day');

        // Check if next base date is this month or next month.
        $isNextMonth = false;
        if ($d - $paymentBaseDay >= 0) {
            $isNextMonth = true;
        } else {
            if (checkdate($m, $paymentBaseDay, $y) === false) {
                $lastDay = date('t', strtotime($localCurrentDate));
                if ($lastDay - $d <= 0) {
                    $isNextMonth = true;
                }
            }
        }

        // Move ym
        if ($isNextMonth) {
            // Move next year if December
            list($y, $m) = AppUtil::moveMonthYm($y, $m);
        }

        // Get next payment base date
        if (checkdate($m, $paymentBaseDay, $y) === false) {
            // If not exist payment base day, set last day of the month.
            $lastDay = date('t', strtotime(AppUtil::dateFromYMD($y, $m, 1)));
            $nextBaseDate = AppUtil::dateFromYMD($y, $m, $lastDay);
        } else {
            $nextBaseDate = AppUtil::dateFromYMD($y, $m, $paymentBaseDay);
        }

        return $nextBaseDate;
    }

    /**
     * Get previous base date by next base date
     *
     * @param int    $teamId
     * @param string $nextBaseDate
     *
     * @return string
     */
    public function getPreviousBaseDate(int $teamId, string $nextBaseDate): string
    {
        list($y, $m, $d) = explode('-', $nextBaseDate);
        list($y, $m) = AppUtil::moveMonthYm($y, $m, -1);

        $paymentSetting = $this->get($teamId);
        $paymentBaseDay = Hash::get($paymentSetting, 'payment_base_day');

        if (checkdate($m, $paymentBaseDay, $y) === false) {
            $prevBaseDate = AppUtil::dateMonthLast(AppUtil::dateFromYMD($y, $m, 1));
        } else {
            $prevBaseDate = AppUtil::dateFromYMD($y, $m, $paymentBaseDay);
        }
        return $prevBaseDate;
    }

    /**
     * Get total days from previous payment base date to next payment base date
     * 現在月度の総利用日数
     *
     * @param int $teamId
     *
     * @return int
     */
    public function getCurrentAllUseDays(int $teamId): int
    {
        $nextBaseDate = $this->getNextBaseDate($teamId);
        $prevBaseDate = $this->getPreviousBaseDate($teamId, $nextBaseDate);
        $res = AppUtil::diffDays($prevBaseDate, $nextBaseDate);
        return $res;
    }

    /**
     * Calc total charge by users count when invite users.
     *
     * @param int   $teamId
     * @param int   $chargeUserCnt
     * @param null  $useDaysByNext
     * @param null  $allUseDays
     * @param array $paymentSetting
     *
     * @return array
     * @internal param int $currentTimeStamp
     */
    public function calcRelatedTotalChargeByAddUsers
    (
        int $teamId,
        int $chargeUserCnt,
        $useDaysByNext = null,
        $allUseDays = null,
        array $paymentSetting = []
    ): array
    {
        try {
            if ($chargeUserCnt == 0) {
                throw new Exception(sprintf("Invalid user count. %s",
                    AppUtil::varExportOneLine(compact('teamId', 'chargeUserCnt', 'paymentSetting'))
                ));
            }
            $paymentSetting = empty($paymentSetting) ? $this->get($teamId) : $paymentSetting;
            if (empty($paymentSetting)) {
                throw new Exception(sprintf("Not exist payment setting data. %s",
                    AppUtil::varExportOneLine(compact('teamId', 'chargeUserCnt'))
                ));
            }

            $useDaysByNext = $useDaysByNext ?? $this->getUseDaysByNextBaseDate($teamId);
            $allUseDays = $allUseDays ?? $this->getCurrentAllUseDays($teamId);
            // Ex. 3people × ¥1,980 × 20 days / 1month
            $subTotalCharge = $chargeUserCnt * $paymentSetting['amount_per_user'] * ($useDaysByNext / $allUseDays);
            $subTotalCharge = $this->processDecimalPointForAmount($paymentSetting['currency'], $subTotalCharge);

            $tax = $this->calcTax($paymentSetting['company_country'], $subTotalCharge);
            $totalCharge = $subTotalCharge + $tax;
            $res = [
                'sub_total_charge' => $subTotalCharge,
                'tax'              => $tax,
                'total_charge'     => $totalCharge,
            ];
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            $res = [
                'sub_total_charge' => 0,
                'tax'              => 0,
                'total_charge'     => 0,
            ];
        }

        return $res;
    }

    /**
     * Calc balance for upgrading plan.
     * Ex. Upgrade plan from max members 50 to 200 and use days until next payment base date:20
     * (100,000 - 50,000) × 20 days / 1 month
     *
     * @param int                                $teamId
     * @param Enum\Model\PaymentSetting\Currency $currencyType
     * @param string                             $upgradePlanCode
     * @param string                             $currentPlanCode
     *
     * @return array
     * @internal param int $upgradePlanPrice
     * @internal param int $currentPlanPrice
     */
    function calcRelatedTotalChargeForUpgradingPlan
    (
        int $teamId,
        Enum\Model\PaymentSetting\Currency $currencyType,
        string $upgradePlanCode,
        string $currentPlanCode
    ): array
    {
        try {
            /** @var CampaignService $CampaignService */
            $CampaignService = ClassRegistry::init("CampaignService");

            if ($upgradePlanCode === $currentPlanCode) {
                throw new Exception(sprintf("Upgrading plan and current plan are same plan. %s",
                    AppUtil::jsonOneLine(compact('teamId', 'upgradePlanCode', 'currentPlanCode'))
                ));
            }

            $currentPlan = $CampaignService->getPlanByCode($currentPlanCode);
            if (empty($currentPlan)) {
                throw new Exception(sprintf("Current plan doesn't exit. %s",
                    AppUtil::jsonOneLine(compact('teamId', 'currentPlanCode'))
                ));
            }

            $upgradePlan = $CampaignService->getPlanByCode($upgradePlanCode);
            if (empty($upgradePlan)) {
                throw new Exception(sprintf("Upgrading plan doesn't exit. %s",
                    AppUtil::jsonOneLine(compact('teamId', 'upgradePlanCode'))
                ));
            }

            $paymentSetting = $this->get($teamId);
            if (empty($paymentSetting)) {
                throw new Exception(sprintf("Payment setting doesn't exist. %s",
                    AppUtil::jsonOneLine(compact('teamId'))
                ));
            }

            $useDaysByNext = $this->getUseDaysByNextBaseDate($teamId);
            $allUseDays = $this->getCurrentAllUseDays($teamId);
            // Ex. Upgrade plan from max members 50 to 200 and use days until next payment base date:20
            // (100,000 - 50,000) × 20 days / 1 month
            $subTotalCharge = ($upgradePlan['price'] - $currentPlan['price']) * ($useDaysByNext / $allUseDays);
            $subTotalCharge = $this->processDecimalPointForAmount($currencyType->getValue(), $subTotalCharge);

            $tax = $this->calcTax($paymentSetting['company_country'], $subTotalCharge);
            $totalCharge = $subTotalCharge + $tax;
            $res = [
                'sub_total_charge' => $subTotalCharge,
                'tax'              => $tax,
                'total_charge'     => $totalCharge,
            ];
        } catch (Exception $e) {
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            $res = [
                'sub_total_charge' => 0,
                'tax'              => 0,
                'total_charge'     => 0,
            ];
        }

        return $res;
    }

    /**
     * Calc decimal point by currency
     *
     * @param int   $currency
     * @param float $amount
     *
     * @return float
     */
    public function processDecimalPointForAmount(int $currency, float $amount): float
    {
        // Change decimal point by currency
        // Ref: No1 in this document
        // http://confluence.goalous.com/display/GOAL/Specifications+confirmation
        if ($currency == PaymentSetting::CURRENCY_TYPE_JPY) {
            $amount = AppUtil::floor($amount, 0);
        } else {
            $amount = AppUtil::floor($amount, 2);
        }
        return $amount;
    }

    /**
     * Format total charge by users count when invite users.
     *
     * @param int                                $teamId
     * @param int                                $userCnt
     * @param Enum\Model\PaymentSetting\Currency $currency
     * @param null                               $useDaysByNext
     * @param null                               $allUseDays
     *
     * @return string
     */
    public function formatTotalChargeByAddUsers(
        int $teamId,
        int $userCnt,
        Enum\Model\PaymentSetting\Currency $currency,
        $useDaysByNext = null,
        $allUseDays = null
    ): string
    {
        $chargeRes = $this->calcRelatedTotalChargeByAddUsers($teamId, $userCnt, $useDaysByNext, $allUseDays);
        // Format ex 1980 → ¥1,980
        $res = $this->formatCharge($chargeRes['total_charge'], $currency->getValue());
        return $res;
    }

    /**
     * Calc total charge by charge type
     *
     * @param int                                 $teamId
     * @param int                                 $chargeUserCnt
     * @param Enum\Model\ChargeHistory\ChargeType $chargeType
     * @param array                               $paymentSetting
     *
     * @return array
     */
    public function calcRelatedTotalChargeByType(
        int $teamId,
        int $chargeUserCnt,
        Enum\Model\ChargeHistory\ChargeType $chargeType,
        array $paymentSetting = []
    ): array
    {
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");
        $isCampaign = $CampaignService->purchased($teamId);

        // Get price for monthly campaign
        if ($isCampaign && $chargeType->getValue() == Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE) {
            $info = $CampaignService->getTeamChargeInfo($teamId);
            if (empty($info)) {
                CakeLog::emergency("PricePlanPurchaseTeam not found for team: $teamId");
                throw new Exception("PricePlanPurchaseTeam not found for team: $teamId");
            }
            return $info;
        }

        // Activation and user increment for campaign
        if ($isCampaign) {
            return [
                'sub_total_charge' => 0,
                'tax'              => 0,
                'total_charge'     => 0,
            ];
        }

        // Monthly fee
        if ($chargeType->getValue() == Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE) {
            return $this->calcRelatedTotalChargeByUserCnt($teamId, $chargeUserCnt, $paymentSetting);
        }

        // Day fee
        return $this->calcRelatedTotalChargeByAddUsers($teamId, $chargeUserCnt, null, null, $paymentSetting);
    }

    /**
     * Calc these by charge user count
     * ・sub total charge
     * ・tax
     * ・total charge(include tax)
     *
     * @param int   $teamId
     * @param int   $chargeUserCnt
     * @param array $paymentSetting
     *
     * @return array
     */
    public function calcRelatedTotalChargeByUserCnt(int $teamId, int $chargeUserCnt, array $paymentSetting = []): array
    {
        try {
            if ($chargeUserCnt == 0) {
                throw new Exception(sprintf("Invalid user count. %s",
                    AppUtil::varExportOneLine(compact('teamId', 'chargeUserCnt', 'paymentSetting'))
                ));
            }
            $paymentSetting = empty($paymentSetting) ? $this->get($teamId) : $paymentSetting;
            if (empty($paymentSetting)) {
                throw new Exception(sprintf("Not exist payment setting data. %s",
                    AppUtil::varExportOneLine(compact('teamId', 'chargeUserCnt'))
                ));
            }
            $subTotalCharge = $this->processDecimalPointForAmount($paymentSetting['currency'],
                $paymentSetting['amount_per_user'] * $chargeUserCnt);
            $tax = $this->calcTax($paymentSetting['company_country'], $subTotalCharge);
            $totalCharge = $subTotalCharge + $tax;

            $res = [
                'sub_total_charge' => $subTotalCharge,
                'tax'              => $tax,
                'total_charge'     => $totalCharge,
            ];
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            $res = [
                'sub_total_charge' => 0,
                'tax'              => 0,
                'total_charge'     => 0,
            ];
        }
        return $res;
    }

    /**
     * Get tax rate by country code
     *
     * @param string $countryCode
     *
     * @return float
     */
    public function getTaxRateByCountryCode(string $countryCode): float
    {
        // Get tax_rate by team country
        $countries = Configure::read("countries");
        $countries = Hash::combine($countries, '{n}.code', '{n}');
        $taxRate = Hash::check($countries, $countryCode . '.tax_rate') ? Hash::get($countries,
            $countryCode . '.tax_rate') : 0;
        return $taxRate;
    }

    /**
     * Get amount per user by country code
     *
     * @param string $countryCode
     *
     * @return int
     */
    public function getDefaultAmountPerUserByCountry(string $countryCode): int
    {
        return $countryCode === 'JP' ? self::AMOUNT_PER_USER_JPY : self::AMOUNT_PER_USER_USD;
    }

    /**
     * Get currency type by country code
     *
     * @param string $countryCode
     *
     * @return int
     */
    public function getCurrencyTypeByCountry(string $countryCode): int
    {
        return $countryCode === 'JP' ? PaymentSetting::CURRENCY_TYPE_JPY : PaymentSetting::CURRENCY_TYPE_USD;
    }

    /**
     * Calc tax
     *
     * @param string $country
     * @param float  $amount
     *
     * @return float
     */
    public function calcTax(string $country, float $amount): float
    {
        $currency = $this->getCurrencyTypeByCountry($country);
        $taxRate = $this->getTaxRateByCountryCode($country);
        if ($taxRate == 0) {
            return 0;
        }
        $tax = $this->processDecimalPointForAmount($currency, $amount * $taxRate);
        return $tax;
    }

    /**
     * Format charge based payment setting
     * - Number format
     * - Currency format
     *
     * @param float|int $charge
     * @param int       $currencyType
     *
     * @return string
     */
    public function formatCharge(float $charge, int $currencyType): string
    {
        // Format ex 1980 → ¥1,980
        $num = number_format($charge, 2);
        if ($currencyType == Enum\Model\PaymentSetting\Currency::JPY) {
            $num = preg_replace("/\.?0+$/", "", $num);
        }
        $res = PaymentSetting::CURRENCY_SYMBOLS_EACH_TYPE[$currencyType] . $num;
        return $res;
    }

    /**
     * Apply Credit card charge for a specified team.
     *
     * @param int                                     $teamId
     * @param Enum\Model\ChargeHistory\ChargeType|int $chargeType
     * @param int                                     $usersCount
     * @param int                                     $opeUserId
     * @param int|null                                $timestampChargeDateTime timestamp of
     *                                                                         charge_histories.charge_datetime
     * @param array                                   $chargeInfo
     *
     * @return array charge response
     * @throws Exception
     */
    public function applyCreditCardCharge(
        int $teamId,
        Enum\Model\ChargeHistory\ChargeType $chargeType,
        int $usersCount,
        $opeUserId = null,
        $timestampChargeDateTime = null,
        $chargeInfo = []
    )
    {
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");
        $isCampaign = $CampaignService->purchased($teamId);
        $pricePlanPurchaseId = null;
        $campaignTeamId = null;

        try {
            CakeLog::info(sprintf('apply credit card charge: %s', AppUtil::jsonOneLine([
                'teams.id'     => $teamId,
                'charge_type'  => $chargeType->getValue(),
                'users_count'  => $usersCount,
                'ope_users.id' => $opeUserId,
                'is_campaign'  => $isCampaign,
            ])));
            // Validate user count
            if (!$isCampaign && $usersCount <= 0) {
                throw new Exception(
                    sprintf("Charge user count is 0. data:%s",
                        AppUtil::varExportOneLine(compact('teamId', 'chargeType', 'usersCount'))
                    )
                );
            }

            // Get Payment settings
            /** @var PaymentSetting $PaymentSetting */
            $PaymentSetting = ClassRegistry::init('PaymentSetting');
            $paymentSettings = $PaymentSetting->getCcByTeamId($teamId);
            if (empty($paymentSettings)) {
                throw new Exception(
                    sprintf("Payment setting or Credit card settings does not exist. data:%s",
                        AppUtil::varExportOneLine(compact('teamId', 'chargeType', 'usersCount'))
                    )
                );
            }
            CakeLog::info(sprintf('payment setting at charge: %s', AppUtil::jsonOneLine([
                'teams.id'       => $teamId,
                'PaymentSetting' => $paymentSettings
            ])));

            $creditCard = Hash::get($paymentSettings, 'CreditCard');
            $customerId = Hash::get($creditCard, 'customer_code');
            $paySetting = Hash::get($paymentSettings, 'PaymentSetting');
            $amountPerUser = Hash::get($paySetting, 'amount_per_user');
            $currency = Hash::get($paySetting, 'currency');
            $currencyName = $currency == PaymentSetting::CURRENCY_TYPE_JPY ? PaymentSetting::CURRENCY_JPY : PaymentSetting::CURRENCY_USD;

            // Apply the user charge on Stripe
            /** @var CreditCardService $CreditCardService */
            $CreditCardService = ClassRegistry::init("CreditCardService");
            if (empty($chargeInfo)) {
                $chargeInfo = $this->calcRelatedTotalChargeByType($teamId, $usersCount, $chargeType, $paySetting);
            }

            CakeLog::info(sprintf('payment charge info: %s', AppUtil::jsonOneLine([
                'teams.id'    => $teamId,
                'charge_info' => $chargeInfo,
            ])));

            $chargeDateTime = is_null($timestampChargeDateTime)
                // $chargeDateTime does not affect by GoalousDateTime::setTestNow()
                ? GoalousDateTime::createFromTimestamp(time())
                : GoalousDateTime::createFromTimestamp($timestampChargeDateTime);
            $maxChargeUserCnt = $this->getChargeMaxUserCnt($teamId, $chargeType, $usersCount);

            if ($isCampaign) {
                $campaignPurchaseInfo = $CampaignService->getPricePlanPurchaseTeam($teamId);
                $pricePlanPurchaseId = Hash::get($campaignPurchaseInfo, 'PricePlanPurchaseTeam.id');
                $campaignTeamId = Hash::get($campaignPurchaseInfo, 'CampaignTeam.id');
            }

            // ChargeHistory temporary insert
            $historyData = [
                'team_id'                     => $teamId,
                'user_id'                     => $opeUserId,
                'payment_type'                => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
                'charge_type'                 => $chargeType->getValue(),
                'amount_per_user'             => $amountPerUser,
                'total_amount'                => $chargeInfo['sub_total_charge'],
                'tax'                         => $chargeInfo['tax'],
                'charge_users'                => $usersCount,
                'currency'                    => $currency,
                'charge_datetime'             => $chargeDateTime->getTimestamp(),
                'result_type'                 => Enum\Model\ChargeHistory\ResultType::ERROR,
                'max_charge_users'            => $maxChargeUserCnt,
                'campaign_team_id'            => $campaignTeamId,
                'price_plan_purchase_team_id' => $pricePlanPurchaseId,
            ];

            /** @var ChargeHistory $ChargeHistory */
            $ChargeHistory = ClassRegistry::init('ChargeHistory');
            $ChargeHistory->clear();
            if (!$ChargeHistory->save($historyData)) {
                throw new Exception(sprintf("Failed create charge history. data:%s",
                    AppUtil::varExportOneLine($historyData)));
            }
            $historyId = $ChargeHistory->getLastInsertID();

            /* Charge */
            $metaData = [
                'env'        => ENV_NAME,
                'team_id'    => $teamId,
                'history_id' => $historyId,
                'type'       => $chargeType->getValue(),
                'campaign'   => $isCampaign,
            ];
            if ($isCampaign) {
                $metaData['plan_purchase_id'] = $pricePlanPurchaseId;
                $metaData['campaign_team_id'] = $campaignTeamId;
            }
            $paymentDescription = "";
            foreach ($metaData as $k => $v) {
                $paymentDescription .= $k . ":" . $v . " ";
            }
            $chargeRes = $CreditCardService->chargeCustomer(
                $customerId,
                $currencyName,
                $chargeInfo['total_charge'],
                $paymentDescription,
                $metaData
            );

            CakeLog::info(sprintf('stripe result: %s', AppUtil::jsonOneLine([
                'teams.id'      => $teamId,
                'stripe_result' => [
                    'error'               => $chargeRes['error'],
                    'message'             => $chargeRes['message'],
                    'isApiRequestSucceed' => $chargeRes['isApiRequestSucceed'],
                ],
            ])));

            // Save charge history
            if ($chargeRes['isApiRequestSucceed'] === false) {
                // This Exception is Stripe system matter.
                throw new StripeApiException(
                    sprintf("Failed to charge. A request to Stripe API was failed. data:%s",
                        AppUtil::varExportOneLine(
                            compact('chargeRes', 'customerId', 'currencyName', 'chargeInfo')
                        )
                    )
                );
            } elseif ($chargeRes['error'] && $chargeType->getValue() !== $chargeType::MONTHLY_FEE) {
                // This Exception is an user's card matter.
                throw new CreditCardStatusException(
                    sprintf("Failed to charge. In adding/activating members case, all transaction should be rollback. data:%s",
                        AppUtil::varExportOneLine(
                            compact('chargeRes', 'customerId', 'currencyName', 'chargeInfo')
                        )
                    )
                );
            }

            /* Transaction commit */
            // [Important]
            // Charge history updating is out of transaction.
            // Because it should refund charge if include updating in transaction.
            $this->TransactionManager->commit();

            $updateHistory = [];
            if ($chargeRes['success']) {
                $updateHistory['result_type'] = Enum\Model\ChargeHistory\ResultType::SUCCESS;
                $updateHistory['stripe_payment_code'] = $chargeRes['paymentId'];

                // If this charging is reordering, set reorder_charge_history_id to the new record
                if ($chargeType->equals(Enum\Model\ChargeHistory\ChargeType::RECHARGE())) {
                    $updateHistory['reorder_charge_history_id'] = $chargeInfo['reorder_charge_history_id'];
                }
            } else {
                $updateHistory['result_type'] = Enum\Model\ChargeHistory\ResultType::FAIL;
                $updateHistory['stripe_payment_code'] = $chargeRes['paymentId'];
            }

            // Update Charge history
            $ChargeHistory->clear();
            $ChargeHistory->id = $historyId;
            if (!$ChargeHistory->save($updateHistory, false)) {
                throw new Exception(sprintf("Failed update charge history. data:%s",
                    AppUtil::varExportOneLine($updateHistory)));
            }

            CakeLog::info(sprintf('update charge history: %s', AppUtil::jsonOneLine([
                'teams.id'              => $teamId,
                'charge_histories.id'   => $historyId,
                'update_charge_history' => $updateHistory,
            ])));
        } catch (Exception $e) {
            /* Transaction rollback */
            $this->TransactionManager->rollback();
            throw $e;
        }
        return $chargeRes;
    }

    /**
     * Reordering specified charge history
     *
     * @param array $targetChargeHistory
     *
     * @return array
     * @throws Exception
     */
    public function reorderCreditCardCharge(array $targetChargeHistory): array
    {
        $targetChargeHistoryId = $targetChargeHistory['id'];
        $teamId = $targetChargeHistory['team_id'];
        $opeUserId = $targetChargeHistory['user_id'];
        $usersCount = $targetChargeHistory['charge_users'];
        $timeStampChargeTime = GoalousDateTime::now()->getTimestamp();

        $subTotalCharge = $targetChargeHistory['total_amount'];
        $tax = $targetChargeHistory['tax'];

        // TODO: Currently several codes are using bcmath with magic number.
        // We have to replace this.
        $totalCharge = bcadd($subTotalCharge, $tax, 2);
        $chargeInfo = [
            'sub_total_charge'          => $subTotalCharge,
            'tax'                       => $tax,
            'total_charge'              => $totalCharge,
            'reorder_charge_history_id' => $targetChargeHistoryId,
        ];

        return $this->applyCreditCardCharge(
            $teamId,
            Enum\Model\ChargeHistory\ChargeType::RECHARGE(),
            $usersCount,
            $opeUserId,
            $timeStampChargeTime,
            $chargeInfo
        );
    }

    /**
     * Get charge max user cnt by charge type
     *
     * @param int                                 $teamId
     * @param Enum\Model\ChargeHistory\ChargeType $chargeType
     * @param int                                 $usersCount
     *
     * @return array
     */
    public function getChargeMaxUserCnt(
        int $teamId,
        Enum\Model\ChargeHistory\ChargeType $chargeType,
        int $usersCount
    )
    {
        if ($chargeType->getValue() == Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE) {
            return $usersCount;
        }
        if ($chargeType->getValue() == Enum\Model\ChargeHistory\ChargeType::RECHARGE) {
            return $usersCount;
        }

        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init("ChargeHistory");
        $latestMaxChargeUserCnt = $ChargeHistory->getLatestMaxChargeUsers($teamId);
        return $latestMaxChargeUserCnt + $usersCount;
    }

    /**
     * Register Credit Card Payment and apply charge in a single transaction.
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $creditCardToken
     * @param array  $paymentData
     *
     * @return array
     */
    public function registerCreditCardPaymentAndCharge(
        int $userId,
        int $teamId,
        string $creditCardToken,
        array $paymentData
    )
    {
        $result = [
            'error'     => false,
            // TODO: fix key name to `statusCode`. 200 is not error code
            'errorCode' => 200,
            'message'   => null
        ];

        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');

        // Check if already registered.
        if (!empty($PaymentSetting->getByTeamId($teamId))) {
            CakeLog::error(sprintf("[%s] Payment setting has already been registered. teamId: %s", __METHOD__,
                $teamId));
            return false;
        }

        // Register payment settings
        try {
            // Register Credit Card to stripe
            // Set description as "Team ID: 2" to identify it on Stripe Dashboard
            $contactEmail = Hash::get($paymentData, 'contact_person_email');
            $customerDescription = "Team ID: $teamId";
            $stripeResponse = $CreditCardService->registerCustomer($creditCardToken, $contactEmail,
                $customerDescription);
            if ($stripeResponse['error'] === true) {
                $result['error'] = true;
                $result['message'] = $stripeResponse['message'];
                $result['errorCode'] = 400;
                return $result;
            }

            // Create PaymentSettings
            $this->TransactionManager->begin();

            // Stripe customer id
            $customerId = $stripeResponse['customer_id'];
            if (empty($customerId)) {
                throw new Exception(sprintf("Error on Stripe call. stripeResponse:%s",
                    AppUtil::varExportOneLine($stripeResponse)));
            }

            // Variable to later use
            $result['customerId'] = $customerId;
            $isCampaign = $CampaignService->isCampaignTeam($teamId);
            $pricePlanCode = $isCampaign ? Hash::get($paymentData, 'price_plan_code') : null;
            $companyCountry = Hash::get($paymentData, 'company_country');
            $currency = $isCampaign ? $CampaignService->getPricePlanCurrency($pricePlanCode) :
                $this->getCurrencyTypeByCountry($companyCountry);
            $timezone = $Team->getTimezone();
            $date = GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d');

            $paymentData['team_id'] = $teamId;
            $paymentData['amount_per_user'] = $amountPerUser = $this->getAmountPerUserBeforePayment($teamId,
                $companyCountry);
            $paymentData['currency'] = $currency;
            $timezone = $Team->getTimezone();
            $paymentData['payment_base_day'] = date('d', strtotime(AppUtil::todayDateYmdLocal($timezone)));
            $paymentData['type'] = Enum\Model\PaymentSetting\Type::CREDIT_CARD;
            $paymentData['start_date'] = $date;

            // Create PaymentSetting
            $PaymentSetting->create();
            if (!$PaymentSetting->save($paymentData)) {
                throw new Exception(sprintf("Failed create payment settings. data: %s",
                    AppUtil::varExportOneLine($paymentData)));
            }
            $paymentSettingId = $PaymentSetting->getLastInsertID();

            // Create CreditCards
            $creditCardData = [
                'team_id'            => $teamId,
                'payment_setting_id' => $paymentSettingId,
                'customer_code'      => $customerId
            ];
            $CreditCard->create();
            if (!$CreditCard->save($creditCardData)) {
                throw new Exception(sprintf("Failed create credit card. data:%s",
                    AppUtil::varExportOneLine($paymentData)));
            }

            // Save snapshot
            /** @var PaymentSettingChangeLog $PaymentSettingChangeLog */
            $PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
            $PaymentSettingChangeLog->saveSnapshot($paymentSettingId, $userId);

            // Set team status
            // Up to this point any failure do not directly affect user accounts or charge its credit card.
            // Team status will be set first in case of any failure team will be able to continue to use.
            if (!$Team->updatePaidPlan($teamId, $date)) {
                throw new Exception(sprintf("Failed to update team status to paid plan. team_id: %s", $teamId));
            }

            /* Create charge history */
            // [Note]
            // ChargeHistory result_type will be updated after charge
            $membersCount = $TeamMember->countChargeTargetUsersEachTeam([$teamId]);
            $membersCount = $membersCount[$teamId];

            // If campaign team, pay as campaign price
            $pricePlanPurchaseId = null;
            $campaignTeamId = null;
            if ($isCampaign) {
                // Register campaign purchase
                $pricePlanPurchase = $CampaignService->savePricePlanPurchase($teamId, $pricePlanCode);
                if (!$pricePlanPurchase) {
                    throw new Exception(sprintf("Failed create PricePlanPurchaseTeam. teamId: %s, pricePlanCode: %s",
                        $teamId, $pricePlanCode));
                }
                $pricePlanPurchaseId = Hash::get($pricePlanPurchase, 'PricePlanPurchaseTeam.id');
                $campaignTeamId = Hash::get($CampaignService->getCampaignTeam($teamId, ['id']), 'id');

                // Get campaign price
                $chargeInfo = $CampaignService->getChargeInfo($pricePlanCode);
            } else {
                $chargeInfo = $this->calcRelatedTotalChargeByUserCnt($teamId, $membersCount, $paymentData);
            }

            // Charge history
            $historyData = [
                'team_id'                     => $teamId,
                'user_id'                     => $userId,
                'payment_type'                => PaymentSetting::PAYMENT_TYPE_CREDIT_CARD,
                'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
                'amount_per_user'             => $amountPerUser,
                'total_amount'                => $chargeInfo['sub_total_charge'],
                'tax'                         => $chargeInfo['tax'],
                'charge_users'                => $membersCount,
                'currency'                    => $currency,
                'charge_datetime'             => time(),
                'result_type'                 => Enum\Model\ChargeHistory\ResultType::ERROR,
                'max_charge_users'            => $membersCount,
                'campaign_team_id'            => $campaignTeamId,
                'price_plan_purchase_team_id' => $pricePlanPurchaseId,
            ];

            $ChargeHistory->create();
            if (!$ChargeHistory->save($historyData)) {
                throw new Exception(sprintf("Failed create charge history. data:%s",
                    AppUtil::varExportOneLine($historyData)));
            }
            $historyId = $ChargeHistory->getLastInsertID();

            // Apply the user charge on Stripe
            $metaData = [
                'env'          => ENV_NAME,
                'team_id'      => $teamId,
                'history_id'   => $historyId,
                'charge_type'  => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
                'first_charge' => true,
                'campaign'     => $isCampaign,
            ];
            if ($isCampaign) {
                $metaData['plan_purchase_id'] = $pricePlanPurchaseId;
                $metaData['campaign_team_id'] = $campaignTeamId;
            }
            $paymentDescription = "";
            foreach ($metaData as $k => $v) {
                $paymentDescription .= $k . ":" . $v . " ";
            }

            $currencyName = $currency == PaymentSetting::CURRENCY_TYPE_JPY ? PaymentSetting::CURRENCY_JPY : PaymentSetting::CURRENCY_USD;
            // Charge
            $chargeResult = $CreditCardService->chargeCustomer(
                $customerId,
                $currencyName,
                $chargeInfo['total_charge'],
                $paymentDescription,
                $metaData
            );

            // Delete cache
            $Team->resetCurrentTeam();

            // Error charging customer using Stripe API. Might be network,  API problem or card rejected
            if ($chargeResult['error'] === true) {
                // Rollback transaction
                $this->TransactionManager->rollback();

                // Remove the customer from Stripe
                $CreditCardService->deleteCustomer($customerId);

                $result['error'] = true;
                $result['message'] = $chargeResult['message'];
                $result['errorCode'] = 500;
                return $result;
            }

            // Commit changes
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            // Remove the customer from Stripe
            $CreditCardService->deleteCustomer($customerId);

            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());

            $result['error'] = true;
            $result['message'] = __("Failed to register paid plan.") . " " . __("Please try again later.");
            $result['errorCode'] = 500;
            return $result;
        }

        // [Important]
        // Updating ChargeHistory is not include transaction.
        // Because we avoid to refund charge.
        // If this process failed, Return success abd we recovery data later.
        try {
            // Save history
            $updateHistory = [];
            if ($chargeResult['success']) {
                $updateHistory['result_type'] = Enum\Model\ChargeHistory\ResultType::SUCCESS;
                $updateHistory['stripe_payment_code'] = $chargeResult['paymentId'];
            } else {
                $updateHistory['result_type'] = Enum\Model\ChargeHistory\ResultType::FAIL;
            }

            $ChargeHistory->clear();
            $ChargeHistory->id = $historyId;
            if (!$ChargeHistory->save($updateHistory)) {
                throw new Exception(sprintf("Failed update result type of charge history. data:%",
                    AppUtil::varExportOneLine(compact('historyId', 'updateHistory'))));
            }
        } catch (Exception $e) {
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            return $result;
        }

        return $result;
    }

    /**
     * Create Payment Setting, Invoice records and register an invoice for the team.
     *
     * @param int   $userId
     * @param int   $teamId
     * @param array $paymentData
     * @param array $invoiceData
     * @param bool  $checkSentInvoice
     * @param       $pricePlanCode
     *
     * @return bool
     */
    public function registerInvoicePayment(
        int $userId,
        int $teamId,
        array $paymentData,
        array $invoiceData,
        bool $checkSentInvoice = true,
        $pricePlanCode = null
    )
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var PaymentSettingChangeLog $PaymentSettingChangeLog */
        $PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init('CampaignService');

        $membersCount = $TeamMember->countChargeTargetUsers($teamId);
        // Count should never be zero.
        if ($membersCount == 0) {
            CakeLog::emergency(sprintf("[%s] Invalid member count for teamId: %s", __METHOD__, $teamId));
            return false;
        }

        // Check if already registered.
        if (!empty($PaymentSetting->getByTeamId($teamId))) {
            CakeLog::error(sprintf("[%s] Payment setting has already been registered. teamId: %s", __METHOD__,
                $teamId));
            return false;
        }

        try {
            $this->TransactionManager->begin();

            // Prepare data for saving
            $timezone = $Team->getTimezone();
            $date = GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d');

            $paymentData['team_id'] = $teamId;
            $paymentData['payment_base_day'] = date('d', strtotime(AppUtil::todayDateYmdLocal($timezone)));
            $paymentData['currency'] = Enum\Model\PaymentSetting\Currency::JPY;
            $paymentData['type'] = Enum\Model\PaymentSetting\Type::INVOICE;
            $paymentData['amount_per_user'] = $this->getAmountPerUserBeforePayment($teamId, 'JP');
            $paymentData['start_date'] = $date;
            // Create Payment Setting
            if (!$PaymentSetting->save($paymentData, true)) {
                throw new Exception(sprintf("Failed create payment settings. data: %s",
                    AppUtil::varExportOneLine($paymentData)));
            }
            $paymentSettingId = $PaymentSetting->getLastInsertID();

            // Prepare data for saving
            $invoiceData['team_id'] = $teamId;
            $invoiceData['payment_setting_id'] = $paymentSettingId;
            $invoiceData['credit_status'] = Enum\Model\Invoice\CreditStatus::WAITING;
            // Create Invoice
            if (!$Invoice->save($invoiceData, true)) {
                throw new Exception(sprintf("Failed create invoice record. data: %s",
                    AppUtil::varExportOneLine($invoiceData)));
            }

            // Save snapshot
            if (!$PaymentSettingChangeLog->saveSnapshot($paymentSettingId, $userId)) {
                throw new Exception(sprintf("Failed to create payment setting change log. data: %s",
                    AppUtil::varExportOneLine(compact('paymentSettingId', 'userId'))
                ));
            }

            // Set team status
            if (!$Team->updatePaidPlan($teamId, $date)) {
                throw new Exception(sprintf("Failed to update team status to paid plan. team_id: %s", $teamId));
            }

            // Save CampaignPurchase
            if ($pricePlanCode !== null && $CampaignService->isCampaignTeam($teamId)) {
                $pricePlanPurchase = $CampaignService->savePricePlanPurchase($teamId, $pricePlanCode);
                if (!$pricePlanPurchase) {
                    throw new Exception(sprintf("Failed create PricePlanPurchaseTeam. teamId: %s, pricePlanCode: %s",
                        $teamId, $pricePlanCode));
                }
            }

            $res = $this->registerInvoice($teamId, $membersCount, REQUEST_TIMESTAMP, $userId, $checkSentInvoice);
            if ($res === false) {
                throw new Exception(sprintf("Error creating invoice payment: ",
                    AppUtil::varExportOneLine(compact('teamId', 'membersCount'))));
            }

            // Delete cache
            $Team->resetCurrentTeam();
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            return false;
        }

        return true;
    }

    /**
     * delete teams all payment settings
     *
     * @param int $teamId
     */
    public function deleteTeamsAllPaymentSetting(int $teamId)
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init('CreditCard');
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init('CampaignTeam');
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        try {
            $this->TransactionManager->begin();

            if (!empty($PaymentSetting->getByTeamId($teamId, ['id']))) {
                if (!$PaymentSetting->softDeleteAll(['PaymentSetting.team_id' => $teamId], false)) {
                    throw new RuntimeException(sprintf('failed soft delete payment_settings: %s', AppUtil::jsonOneLine([
                        'teams.id' => $teamId,
                    ])));
                }
            }
            if (!empty($Invoice->getByTeamId($teamId, ['id']))) {
                if (!$Invoice->softDeleteAll(['Invoice.team_id' => $teamId], false)) {
                    throw new RuntimeException(sprintf('failed soft delete invoices: %s', AppUtil::jsonOneLine([
                        'teams.id' => $teamId,
                    ])));
                }
            }
            if (!empty($CreditCard->getByTeamId($teamId, ['id']))) {
                if (!$CreditCard->softDeleteAll(['CreditCard.team_id' => $teamId], false)) {
                    throw new RuntimeException(sprintf('failed soft delete credit_cards: %s', AppUtil::jsonOneLine([
                        'teams.id' => $teamId,
                    ])));
                }
            }
            if (!empty($PricePlanPurchaseTeam->getByTeamId($teamId, ['id']))) {
                if (!$PricePlanPurchaseTeam->softDeleteAll(['PricePlanPurchaseTeam.team_id' => $teamId], false)) {
                    throw new RuntimeException(sprintf('failed soft delete price_plan_purchase_team: %s',
                        AppUtil::jsonOneLine([
                            'teams.id' => $teamId,
                        ])));
                }
            }
            if (!empty($CampaignTeam->getByTeamId($teamId, ['id']))) {
                if (!$CampaignTeam->softDeleteAll(['CampaignTeam.team_id' => $teamId], false)) {
                    throw new RuntimeException(sprintf('failed soft delete campaign_team: %s', AppUtil::jsonOneLine([
                        'teams.id' => $teamId,
                    ])));
                }
            }
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("Failed to delete payment data. teamId: %s, errorDetail: %s",
                $teamId,
                $e->getMessage()
            ));
            CakeLog::emergency($e->getTraceAsString());
            return false;
        }

        return true;
    }

    /**
     * Register Invoice including requesting to atobarai.com and saving data in the following:
     * - charge_histories -> monthly charge
     * - invoice_histories -> status of response of atobarai.com
     * - invoice_histories_charge_histories -> intermediate table for invoice_histories and charge_histories.
     *
     * @param int      $teamId
     * @param int      $chargeMemberCount
     * @param int      $time
     * @param int|null $userId
     * @param bool     $checkSentInvoice
     * @param int|null $rechargeTargetHistoryId
     *
     * @return bool
     * @internal param float $timezone
     */
    public function registerInvoice(
        int $teamId,
        int $chargeMemberCount,
        int $time,
        $userId = null,
        bool $checkSentInvoice = true,
        $rechargeTargetHistoryId = null
    ): bool
    {
        CakeLog::info(sprintf('register invoice: %s', AppUtil::jsonOneLine([
            'teams.id'     => $teamId,
            'charge_count' => $chargeMemberCount,
            'time'         => $time,
            'users.id'     => $userId,
        ])));
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
        /** @var InvoiceService $InvoiceService */
        $InvoiceService = ClassRegistry::init('InvoiceService');
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        /** @var  InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        /** @var  InvoiceHistoriesChargeHistory $InvoiceHistoriesChargeHistory */
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init('CampaignService');

        $timezone = $Team->getById($teamId)['timezone'];
        $localCurrentDate = AppUtil::dateYmdLocal($time, $timezone);
        // if already send an invoice, return
        if ($checkSentInvoice && $InvoiceService->isSentInvoice($teamId, $localCurrentDate)) {
            CakeLog::info(sprintf('invoice sent already: %s', AppUtil::jsonOneLine([
                'teams.id'           => $teamId,
                'local_current_date' => $localCurrentDate,
            ])));
            return false;
        }

        $this->TransactionManager->begin();
        try {
            // Check if its a campaign and charge the correct price
            $paymentSetting = $PaymentSetting->getByTeamId($teamId);
            $targetChargeHistories = $PaymentService->findTargetInvoiceChargeHistories($teamId, $time);
            $pricePlanPurchase = $CampaignService->getPricePlanPurchaseTeam($teamId);
            $isCampaign = ($pricePlanPurchase != null);
            $pricePlanCode = Hash::get($pricePlanPurchase, 'PricePlanPurchaseTeam.price_plan_code');
            $pricePlanPurchaseId = Hash::get($pricePlanPurchase, 'PricePlanPurchaseTeam.id');
            $campaignTeamId = Hash::get($pricePlanPurchase, 'CampaignTeam.id');

            if ($isCampaign && $pricePlanCode) {
                $chargeInfo = $CampaignService->getChargeInfo($pricePlanCode);
            } else {
                $chargeInfo = $this->calcRelatedTotalChargeByUserCnt($teamId, $chargeMemberCount, $paymentSetting);
            }

            // save monthly charge
            $ChargeHistory->clear();
            $monthlyChargeHistory = $ChargeHistory->addInvoiceMonthlyCharge(
                $teamId,
                $time,
                $chargeInfo['sub_total_charge'],
                $chargeInfo['tax'],
                $paymentSetting['amount_per_user'],
                $chargeMemberCount,
                $userId,
                PaymentSetting::CURRENCY_TYPE_JPY,
                $campaignTeamId,
                $pricePlanPurchaseId
            );
            if (!$monthlyChargeHistory) {
                throw new Exception(sprintf("Failed to save monthly charge history. validationErrors: %s"),
                    AppUtil::varExportOneLine($ChargeHistory->validationErrors)
                );
            }
            CakeLog::info(sprintf('add invoice monthly charge_histories: %s',
                AppUtil::jsonOneLine($monthlyChargeHistory)));

            // save the invoice history
            $invoiceHistoryData = [
                'team_id'           => $teamId,
                'order_datetime'    => $time,
                'system_order_code' => '',
            ];
            $InvoiceHistory->clear();
            $invoiceHistory = $InvoiceHistory->save($invoiceHistoryData);
            if (!$invoiceHistory) {
                throw new Exception(sprintf("Failed save an InvoiceHistory. saveData: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($invoiceHistoryData),
                    AppUtil::varExportOneLine($InvoiceHistory->validationErrors)
                ));
            }
            CakeLog::info(sprintf('add invoice_histories: %s', AppUtil::jsonOneLine($invoiceHistory)));

            // save invoice histories and charge histories relation
            $invoiceHistoryId = $InvoiceHistory->getLastInsertID();
            $invoiceHistoriesChargeHistories = [];
            foreach (am($targetChargeHistories, [$monthlyChargeHistory]) as $history) {
                $invoiceHistoriesChargeHistories[] = [
                    'invoice_history_id' => $invoiceHistoryId,
                    'charge_history_id'  => $history['id'],
                ];
            }
            $InvoiceHistoriesChargeHistory->clear();
            $resSaveInvoiceChargeHistory = $InvoiceHistoriesChargeHistory->saveAll($invoiceHistoriesChargeHistories);
            if (!$resSaveInvoiceChargeHistory) {
                throw new Exception(sprintf("Failed save an InvoiceChargeHistories. saveData: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($invoiceHistoriesChargeHistories),
                    AppUtil::varExportOneLine($InvoiceHistoriesChargeHistory->validationErrors)
                ));
            }

            // send invoice to atobarai.com
            $targetChargeHistories[] = $monthlyChargeHistory;
            $resAtobarai = $InvoiceService->registerOrder(
                $teamId,
                $targetChargeHistories,
                $localCurrentDate
            );
            if ($resAtobarai['status'] == 'error') {
                throw new Exception(sprintf("Request to atobarai.com was failed. errorMsg: %s, chargeHistories: %s, requestData: %s",
                    AppUtil::varExportOneLine($resAtobarai['messages']),
                    AppUtil::varExportOneLine($targetChargeHistories),
                    AppUtil::varExportOneLine($resAtobarai['requestData'])
                ));
            }
            CakeLog::info(sprintf('response of atobarai.com: %s', AppUtil::jsonOneLine([
                'teams.id'          => $teamId,
                'response_atobarai' => $resAtobarai,
            ])));
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("Failed monthly charge of invoice. teamId: %s, errorDetail: %s",
                $teamId,
                $e->getMessage()
            ));
            CakeLog::emergency($e->getTraceAsString());
            return false;
        }

        $this->TransactionManager->commit();

        // Update status after order
        $this->updateAfterInvoiceOrder($teamId, $invoiceHistoryId, $resAtobarai);

        return true;
    }

    /**
     * [Important]
     * Updating ChargeHistory is not include transaction.
     * Because It got necessary to rollback even atobarai.com data if include.
     * If this process failed, Return success abd we recovery data later.
     *
     * @param int   $teamId
     * @param int   $invoiceHistoryId
     * @param array $resAtobarai
     */
    public function updateAfterInvoiceOrder(int $teamId, int $invoiceHistoryId, array $resAtobarai)
    {
        /** @var  InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');

        try {
            // update system order code.
            $invoiceHistoryUpdate = [
                'id'                => $invoiceHistoryId,
                'system_order_code' => $resAtobarai['systemOrderId'],
            ];
            $InvoiceHistory->clear();
            $resUpdate = $InvoiceHistory->save($invoiceHistoryUpdate, false);
            if (!$resUpdate) {
                throw new Exception(sprintf("Failed update invoice history. It should be recovered!!! teamId: %s, data: %s, validationErrors: %s",
                    $teamId,
                    AppUtil::varExportOneLine($invoiceHistoryUpdate),
                    AppUtil::varExportOneLine($InvoiceHistory->validationErrors)
                ));
            }
            CakeLog::info(sprintf('updated invoice_histories: %s', AppUtil::jsonOneLine([
                'teams.id'          => $teamId,
                'invoice_histories' => $resUpdate,
            ])));
        } catch (Exception $e) {
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
        }
    }

    /**
     * Invoice reorder for past failed order
     *
     * @param int $teamId
     * @param int $reorderTargetId
     *
     * @return bool
     */
    public function reorderInvoice(int $teamId, int $reorderTargetId): bool
    {
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
        /** @var InvoiceService $InvoiceService */
        $InvoiceService = ClassRegistry::init('InvoiceService');
        /** @var  InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        /** @var  InvoiceHistoriesChargeHistory $InvoiceHistoriesChargeHistory */
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        /** @var ChargeHistoryService $ChargeHistoryService */
        $ChargeHistoryService = ClassRegistry::init('ChargeHistoryService');
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');

        try {
            $this->TransactionManager->begin();

            // Get invoice history
            $targetInvoiceHistory = $InvoiceHistory->getById($reorderTargetId);
            if (empty($targetInvoiceHistory)) {
                throw new Exception(sprintf("Invoice history doesn't exist. data: %s",
                    AppUtil::varExportOneLine(compact('teamId', 'reorderTargetId'))
                ));
            }

            // Get all histories related invoice order
            $targetChargeHistories = $ChargeHistory->findRelatedFailedInvoiceOrder($teamId, $reorderTargetId);
            if (empty($targetChargeHistories)) {
                throw new Exception(sprintf("Target charge history doesn't exist. data: %s",
                    AppUtil::varExportOneLine(compact('teamId', 'reorderTargetId'))
                ));
            }

            // Save a charge history(summarize to one)
            $newHistory = $ChargeHistoryService->addInvoiceRecharge($teamId, $targetChargeHistories);

            // Save an invoice history
            $time = GoalousDateTime::now()->getTimestamp();
            $invoiceHistoryData = [
                'team_id'             => $teamId,
                'order_datetime'      => $time,
                'system_order_code'   => '',
                'reorder_target_code' => $targetInvoiceHistory['system_order_code']
            ];
            $InvoiceHistory->create();
            $invoiceHistory = $InvoiceHistory->save($invoiceHistoryData);
            if (!$invoiceHistory) {
                throw new Exception(sprintf("Failed save an InvoiceHistory. saveData: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($invoiceHistoryData),
                    AppUtil::varExportOneLine($InvoiceHistory->validationErrors)
                ));
            }
            CakeLog::info(sprintf('add invoice_histories: %s', AppUtil::jsonOneLine($invoiceHistory)));

            // Save invoice history and charge history relation
            $invoiceHistoryId = $InvoiceHistory->getLastInsertID();
            $invoiceHistoriesChargeHistory = [
                'invoice_history_id' => $invoiceHistoryId,
                'charge_history_id'  => $newHistory['id'],
            ];
            $InvoiceHistoriesChargeHistory->create();
            $resSaveInvoiceChargeHistory = $InvoiceHistoriesChargeHistory->save($invoiceHistoriesChargeHistory);
            if (!$resSaveInvoiceChargeHistory) {
                throw new Exception(sprintf("Failed save an InvoiceChargeHistory. saveData: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($invoiceHistoriesChargeHistory),
                    AppUtil::varExportOneLine($InvoiceHistoriesChargeHistory->validationErrors)
                ));
            }

            // Send invoice to atobarai.com (reorder)
            $timezone = $TeamService->getTeamTimezone($teamId);
            $localCurrentDate = GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Y-m-d');
            $resAtobarai = $InvoiceService->registerOrder($teamId, $targetChargeHistories, $localCurrentDate);
            if ($resAtobarai['status'] == 'error') {
                throw new Exception(sprintf("Request to atobarai.com was failed. errorMsg: %s, chargeHistories: %s, requestData: %s",
                    AppUtil::varExportOneLine($resAtobarai['messages']),
                    AppUtil::varExportOneLine($targetChargeHistories),
                    AppUtil::varExportOneLine($resAtobarai['requestData'])
                ));
            }
            CakeLog::info(sprintf('response of atobarai.com: %s', AppUtil::jsonOneLine([
                'teams.id'          => $teamId,
                'response_atobarai' => $resAtobarai,
            ])));
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            return false;
        }
        $this->TransactionManager->commit();

        // Update status after order
        $this->updateAfterInvoiceOrder($teamId, $invoiceHistoryId, $resAtobarai);

        return true;
    }

    /**
     * Get target charge histories
     * target date range is from previous monthly charge data to yesterday.
     * target histories should be not invoiced yet.
     *
     * @param int $teamId
     * @param int $time
     *
     * @return array
     */
    public function findTargetInvoiceChargeHistories(
        int $teamId,
        int $time
    )
    {
        // Invoices for only Japanese team. So, $timezone will be always Japan time.
        $timezone = 9;

        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
        $localCurrentDate = AppUtil::dateYmdLocal($time, $timezone);
        // fetching charge histories
        $yesterdayLocalDate = AppUtil::dateYesterday($localCurrentDate);
        $targetEndTs = AppUtil::getEndTimestampByTimezone($yesterdayLocalDate, $timezone);

        $targetPaymentHistories = $ChargeHistory->findForInvoiceBeforeTs($teamId, $targetEndTs);
        return $targetPaymentHistories;
    }

    /**
     * Find target teams that charge monthly by credit card
     * main conditions
     * - payment type: credit card
     * - have not already charged
     * - payment base date = execution datetime + team timezone
     *   EX.
     *      execution datetime: 2017/9/19 15:00:00
     *      team timezone: +9 hour(Tokyo)
     *      payment base day: 20
     *      2017/9/19 15:00:00 + 9hour = 2017/9/20
     *      payment base day(20) == get day(20) from 2017/9/20 → charge target team！
     * [Note]
     * We can get target charge teams by using only one SQL.
     * But some MySQL syntax(EX. INTERVAL) can't use if run unit test
     * Because unit test use sqlite as DB.
     * So the reliability of the test is important,
     * I decided to implement process like this.
     *
     * @param int $time
     *
     * @return array
     */
    public function findMonthlyChargeCcTeams(
        int $time = REQUEST_TIMESTAMP
    ): array
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init("ChargeHistory");
        // Get teams only credit card payment type
        $targetChargeTeams = $PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::CREDIT_CARD());

        // Filtering
        $targetChargeTeams = array_filter($targetChargeTeams, function ($v) use ($time, $ChargeHistory) {
            $timezone = Hash::get($v, 'Team.timezone');
            $localCurrentTs = $time + ($timezone * HOUR);
            $paymentBaseDay = Hash::get($v, 'PaymentSetting.payment_base_day');
            $skipPayment = !empty(Hash::get($v, 'PaymentSetting.payment_skip_flg'));
            if ($skipPayment) {
                return false;
            }
            // Check if today is payment base date
            $paymentBaseDate = AppUtil::correctInvalidDate(
                date('Y', $localCurrentTs),
                date('m', $localCurrentTs),
                $paymentBaseDay
            );
            if ($paymentBaseDate != AppUtil::dateYmd($localCurrentTs)) {
                return false;
            }

            // Check if have not already charged
            $teamId = Hash::get($v, 'PaymentSetting.team_id');
            $chargeHistory = $ChargeHistory->getByChargeDate($teamId, $paymentBaseDate);
            if (!empty($chargeHistory)) {
                return false;
            }
            return true;

        });
        return $targetChargeTeams;
    }

    /**
     * Find target teams that charge monthly by invoice
     * main conditions
     * - payment type: invoice
     * - have not already charged
     * - payment base date = execution datetime + team timezone
     *   EX.
     *      execution datetime: 2017/9/19 15:00:00
     *      team timezone: +9 hour(Tokyo)
     *      payment base day: 20
     *      2017/9/19 15:00:00 + 9hour = 2017/9/20
     *      payment base day(20) == get day(20) from 2017/9/20 → charge target team！
     * [Note]
     * We can get target charge teams by using only one SQL.
     * But some MySQL syntax(EX. INTERVAL) can't use if run unit test
     * Because unit test use sqlite as DB.
     * So the reliability of the test is important,
     * I decided to implement process like this.
     *
     * @param int $time
     *
     * @return array
     * @internal param int|null $targetTimezone
     */
    public function findMonthlyChargeInvoiceTeams(
        int $time = REQUEST_TIMESTAMP
    ): array
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init("InvoiceHistory");
        // Get teams only credit card payment type
        $targetChargeTeams = $PaymentSetting->findMonthlyChargeTeams(Enum\Model\PaymentSetting\Type::INVOICE());
        CakeLog::info(sprintf('teams monthly invoice charge:%s', AppUtil::jsonOneLine($targetChargeTeams)));
        // Filtering
        $targetChargeTeams = array_filter($targetChargeTeams,
            function ($v) use ($time, $InvoiceHistory) {
                $timezone = Hash::get($v, 'Team.timezone');
                $localCurrentTs = $time + ($timezone * HOUR);
                $paymentBaseDay = Hash::get($v, 'PaymentSetting.payment_base_day');
                $skipPayment = !empty(Hash::get($v, 'PaymentSetting.payment_skip_flg'));
                if ($skipPayment) {
                    return false;
                }
                // Check if today is payment base date
                $paymentBaseDate = AppUtil::correctInvalidDate(
                    date('Y', $localCurrentTs),
                    date('m', $localCurrentTs),
                    $paymentBaseDay
                );
                if ($paymentBaseDate != AppUtil::dateYmd($localCurrentTs)) {
                    return false;
                }

                // Check if have not already charged
                $teamId = Hash::get($v, 'PaymentSetting.team_id');
                $invoiceHistory = $InvoiceHistory->getByOrderDate($teamId, $paymentBaseDate);
                if (!empty($invoiceHistory)) {
                    return false;
                }
                return true;
            });
        return $targetChargeTeams;
    }

    /**
     * Update Payment settings payer info.
     *
     * @param int   $teamId
     * @param int   $userId
     * @param array $payerData
     *
     * @return array|bool
     */
    public function updatePayerInfo(
        int $teamId,
        int $userId,
        array $payerData
    )
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        $paySetting = $PaymentSetting->getUnique($teamId);
        // Check if payment exists
        if (empty($paySetting)) {
            return ['errorCode' => 400, 'message' => __('Payment settings does not exists.')];
        }

        $data = [
            'id'                        => $paySetting['id'],
            'company_name'              => $payerData['company_name'],
            'company_post_code'         => $payerData['company_post_code'],
            'company_region'            => $payerData['company_region'],
            'company_city'              => $payerData['company_city'],
            'company_street'            => $payerData['company_street'],
            'contact_person_first_name' => $payerData['contact_person_first_name'],
            'contact_person_last_name'  => $payerData['contact_person_last_name'],
            'contact_person_tel'        => $payerData['contact_person_tel'],
            'contact_person_email'      => $payerData['contact_person_email'],
        ];

        // If payment type is invoice, user can update contact person name kana
        if ((int)Hash::get($paySetting, 'type') === Enum\Model\PaymentSetting\Type::INVOICE) {
            $data['contact_person_first_name_kana'] = $payerData['contact_person_first_name_kana'];
            $data['contact_person_last_name_kana'] = $payerData['contact_person_last_name_kana'];
        }

        try {
            // Update PaymentSettings
            $PaymentSetting->begin();

            // Save Payment Settings
            $updatedPaymentSetting = $PaymentSetting->save($data, false);
            if (false === $updatedPaymentSetting) {
                throw new Exception(sprintf("Fail to update payment settings. data: %s",
                    AppUtil::varExportOneLine($data)));
            }

            // Save snapshot
            /** @var PaymentSettingChangeLog $PaymentSettingChangeLog */
            $PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
            $PaymentSettingChangeLog->saveSnapshot($updatedPaymentSetting['PaymentSetting']['id'], $userId);

            $PaymentSetting->commit();
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());

            return ['errorCode' => 500, 'message' => __("An error occurred while processing.")];
        }
        return true;
    }

    /**
     * Charge single
     * [Important]
     * Charge processing is complicated transaction.
     * Especially if team's payment type is credit card,
     * ChargeHistory updating is out of transaction after call Stripe Charge API.
     * So if use this method, be aware of the following points
     * ■Set argument to datasource that is already used for beginning transaction
     *   e.g.
     *   $Invite->begin();
     *   ...
     *   $db = $Invite->getDataSource();
     *   $PaymentService->charge(~, $db
     * ■Don't commit/rollback in the caller because commit/rollback done in this method.
     *   e.g. don't this
     *   try {
     *      $Invite->begin();
     *      // Save other data
     *      $PaymentService->charge(**)
     *      × $Invite->commit():
     *   } catch (Exception $e) {
     *      × $Invite->rollback():
     *   }
     * ■Must catch Exception and handling in the caller
     *
     * @param int                                     $teamId
     * @param Enum\Model\ChargeHistory\ChargeType|int $chargeType
     * @param int                                     $usersCount
     * @param int                                     $opeUserId
     *
     * @return array
     * @throws Exception
     */
    public function charge(
        int $teamId,
        Enum\Model\ChargeHistory\ChargeType $chargeType,
        int $usersCount = 1,
        int $opeUserId
    )
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");

        $paymentSetting = $PaymentSetting->getUnique($teamId);
        if (empty($paymentSetting)) {
            throw new Exception(
                sprintf("Payment setting doesn't exist. data:%s",
                    AppUtil::varExportOneLine(compact('teamId', 'chargeType', 'usersCount'))
                )
            );
        }

        if (Hash::get($paymentSetting, 'type') == Enum\Model\PaymentSetting\Type::CREDIT_CARD) {
            $res = $this->applyCreditCardCharge(
                $teamId,
                $chargeType,
                $usersCount,
                $opeUserId
            );
            if ($res['error']) {
                throw new Exception(
                    sprintf("Not exist payment setting. data:%s",
                        AppUtil::varExportOneLine(compact('teamId', 'chargeType', 'usersCount'))
                    )
                );
            }
        } else {
            /** @var ChargeHistory $ChargeHistory */
            $ChargeHistory = ClassRegistry::init("ChargeHistory");

            try {

                // Apply the user charge on Stripe
                $chargeInfo = $this->calcRelatedTotalChargeByType(
                    $teamId,
                    $usersCount,
                    $chargeType,
                    $paymentSetting
                );

                $maxChargeUserCnt = $this->getChargeMaxUserCnt($teamId, $chargeType, $usersCount);
                // Insert ChargeHistory
                $historyData = [
                    'team_id'          => $teamId,
                    'user_id'          => $opeUserId,
                    'payment_type'     => Enum\Model\PaymentSetting\Type::INVOICE,
                    'charge_type'      => $chargeType->getValue(),
                    'amount_per_user'  => $paymentSetting['amount_per_user'],
                    'total_amount'     => $chargeInfo['sub_total_charge'],
                    'tax'              => $chargeInfo['tax'],
                    'charge_users'     => $usersCount,
                    'currency'         => $paymentSetting['currency'],
                    'charge_datetime'  => time(),
                    'result_type'      => Enum\Model\ChargeHistory\ResultType::SUCCESS,
                    'max_charge_users' => $maxChargeUserCnt
                ];
                if (!$ChargeHistory->save($historyData)) {
                    throw new Exception(
                        sprintf("Failed to create charge history. data:%s",
                            AppUtil::varExportOneLine(compact('historyData'))
                        )
                    );
                }
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * Charge single for upgrading plan
     *
     * @param int    $teamId
     * @param string $currentPlanCode
     * @param string $upgradePlanCode
     * @param int    $opeUserId
     *
     * @throws Exception
     */
    public function chargeForUpgradingCampaignPlan(
        int $teamId,
        string $currentPlanCode,
        string $upgradePlanCode,
        int $opeUserId
    )
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");

        $paymentSetting = $PaymentSetting->getUnique($teamId);
        if (empty($paymentSetting)) {
            throw new Exception(
                sprintf("Payment setting doesn't exist. data:%s",
                    AppUtil::varExportOneLine(compact('teamId'))
                )
            );
        }

        $chargeType = Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF();
        $usersCount = 0;
        $chargeInfo = $this->calcRelatedTotalChargeForUpgradingPlan(
            $teamId,
            new Enum\Model\PaymentSetting\Currency((int)$paymentSetting['currency']),
            $upgradePlanCode,
            $currentPlanCode
        );

        if (Hash::get($paymentSetting, 'type') == Enum\Model\PaymentSetting\Type::CREDIT_CARD) {
            $res = $this->applyCreditCardCharge(
                $teamId,
                $chargeType,
                $usersCount,
                $opeUserId,
                null,
                $chargeInfo
            );
            if ($res['error']) {
                throw new Exception(
                    sprintf("Not exist payment setting. data:%s",
                        AppUtil::varExportOneLine(compact('teamId', 'chargeInfo'))
                    )
                );
            }
        } else {
            /** @var ChargeHistory $ChargeHistory */
            $ChargeHistory = ClassRegistry::init("ChargeHistory");

            try {
                $campaignPurchaseInfo = $CampaignService->getPricePlanPurchaseTeam($teamId);
                $pricePlanPurchaseId = Hash::get($campaignPurchaseInfo, 'PricePlanPurchaseTeam.id');
                $campaignTeamId = Hash::get($campaignPurchaseInfo, 'CampaignTeam.id');

                $maxChargeUserCnt = $this->getChargeMaxUserCnt($teamId, $chargeType, $usersCount);
                // Insert ChargeHistory
                $historyData = [
                    'team_id'                     => $teamId,
                    'user_id'                     => $opeUserId,
                    'payment_type'                => Enum\Model\PaymentSetting\Type::INVOICE,
                    'charge_type'                 => $chargeType->getValue(),
                    'amount_per_user'             => 0,
                    'total_amount'                => $chargeInfo['sub_total_charge'],
                    'tax'                         => $chargeInfo['tax'],
                    'charge_users'                => $usersCount,
                    'currency'                    => $paymentSetting['currency'],
                    'charge_datetime'             => time(),
                    'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
                    'max_charge_users'            => $maxChargeUserCnt,
                    'campaign_team_id'            => $campaignTeamId,
                    'price_plan_purchase_team_id' => $pricePlanPurchaseId,
                ];
                if (!$ChargeHistory->save($historyData)) {
                    throw new Exception(
                        sprintf("Failed to create charge history. data:%s",
                            AppUtil::varExportOneLine(compact('historyData'))
                        )
                    );
                }
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    /*
     * Update invoice company information
     *
     * @param int   $teamId
     * @param array $invoiceData
     *
     * @return array|bool
     */
    public function updateInvoice(
        int $teamId,
        array $invoiceData
    )
    {
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        $invoice = $Invoice->getByTeamId($teamId);

        // Check if payment exists
        if (empty($invoice)) {
            return ['errorCode' => 400, 'message' => __('Payment settings does not exists.')];
        }
        $data = [
            'id'                             => $invoice['id'],
            'team_id'                        => $invoice['team_id'],
            'company_name'                   => $invoiceData['company_name'],
            'company_post_code'              => $invoiceData['company_post_code'],
            'company_region'                 => $invoiceData['company_region'],
            'company_city'                   => $invoiceData['company_city'],
            'company_street'                 => $invoiceData['company_street'],
            'contact_person_first_name'      => $invoiceData['contact_person_first_name'],
            'contact_person_first_name_kana' => $invoiceData['contact_person_first_name_kana'],
            'contact_person_last_name'       => $invoiceData['contact_person_last_name'],
            'contact_person_last_name_kana'  => $invoiceData['contact_person_last_name_kana'],
            'contact_person_tel'             => $invoiceData['contact_person_tel'],
            'contact_person_email'           => $invoiceData['contact_person_email'],
        ];

        try {
            $this->TransactionManager->begin();
            if (!$Invoice->save($data)) {
                throw new Exception(sprintf("Fail to update invoice. data: %s",
                    AppUtil::varExportOneLine($data)));
            }
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            return ['errorCode' => 500, 'message' => __("An error occurred while processing.")];
        }
        return true;
    }

    /**
     * Payment validation
     *
     * @param mixed $data
     * @param array $fields
     *
     * @return array
     */
    function validateSave($data, array $fields): array
    {
        $data = !is_array($data) ? [] : $data;
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init("Invoice");
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init("PricePlanPurchaseTeam");

        $allValidationErrors = [];
        // PaymentSetting validation
        if (!empty(Hash::get($fields, 'PaymentSetting'))) {
            $paymentType = Hash::get($data, 'payment_setting.type');
            if (is_null($paymentType) === false && (int)$paymentType === Enum\Model\PaymentSetting\Type::INVOICE) {
                $PaymentSetting->validate = am($PaymentSetting->validate, $PaymentSetting->validateJp);
            }
            $allValidationErrors = am(
                $allValidationErrors,
                $this->validateSingleModelFields($data, $fields, 'payment_setting', 'PaymentSetting',
                    $PaymentSetting)
            );
        }

        // CreditCard validation
        if (!empty(Hash::get($fields, 'CreditCard'))) {
            $allValidationErrors = am(
                $allValidationErrors,
                $this->validateSingleModelFields($data, $fields, 'credit_card', 'CreditCard', $CreditCard)
            );
        }

        // Invoice validation
        if (!empty(Hash::get($fields, 'Invoice'))) {
            $allValidationErrors = am(
                $allValidationErrors,
                $this->validateSingleModelFields($data, $fields, 'invoice', 'Invoice', $Invoice)
            );
        }

        // PricePlanPurchaseTeam validation
        if (!empty(Hash::get($fields, 'PricePlanPurchaseTeam'))) {
            $allValidationErrors = am(
                $allValidationErrors,
                $this->validateSingleModelFields($data, $fields, 'price_plan_purchase_team', 'PricePlanPurchaseTeam',
                    $PricePlanPurchaseTeam)
            );
        }

        return $allValidationErrors;
    }

    /*
     * Check to prevent illegal choice of dollar or yen
     *
     * @param string $ccCountry
     * @param string $companyCountry
     *
     * @return bool
     */
    function checkIllegalChoiceCountry(string $ccCountry, string $companyCountry): bool
    {
        if (($ccCountry === 'JP' && $companyCountry !== 'JP')
            || ($ccCountry !== 'JP' && $companyCountry === 'JP')
        ) {
            return false;
        }
        return true;
    }

    /**
     * get amount per user
     * # case1. not specified teamId
     *  - get default amount by using user lang setting
     * # case2. exist team amount per user data
     *  - get data from payment_settings record
     * # caes3. exist only team country code
     *  - get default amount by useing team country code
     *
     * @param int|null $teamId
     *
     * @return int
     */
    function getAmountPerUser($teamId): int
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        App::uses('LangHelper', 'View/Helper');
        $Lang = new LangHelper(new View());

        $userCountryCode = $Lang->getUserCountryCode();
        $defaultAmountPerUser = $this->getDefaultAmountPerUserByCountry($userCountryCode);

        if (!$teamId) {
            return $defaultAmountPerUser;
        }

        $teamAmountPerUser = $PaymentSetting->getAmountPerUser($teamId);
        if ($teamAmountPerUser !== null) {
            return $teamAmountPerUser;
        }

        return $defaultAmountPerUser;
    }

    /**
     * Calc charge user count
     *
     * @param int $teamId
     * @param int $addUserCnt
     *
     * @return int
     */
    function calcChargeUserCount(int $teamId, int $addUserCnt): int
    {
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init("ChargeHistory");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $maxChargedUserCnt = $ChargeHistory->getLatestMaxChargeUsers($teamId);
        $currentChargeTargetUserCnt = $TeamMember->countChargeTargetUsers($teamId);

        // Regard adding users as charge users as it is
        //  if current users does not over max charged users
        if ($currentChargeTargetUserCnt - $maxChargedUserCnt >= 0) {
            return $addUserCnt;
        }

        $chargeUserCnt = $currentChargeTargetUserCnt + $addUserCnt - $maxChargedUserCnt;
        return $chargeUserCnt;
    }

    /**
     * Is charge user activation or not
     *
     * @param int $teamId
     *
     * @return bool
     */
    function isChargeUserActivation(int $teamId): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init('CampaignService');

        if (!$Team->isPaidPlan($teamId) || $CampaignService->purchased($teamId)) {
            return false;
        }
        $chargeUserCount = $this->calcChargeUserCount($teamId, 1);
        return $chargeUserCount === 1;
    }

    /**
     * Return Payment type: INVOICE / CREDIT_CARD
     *
     * @param int $teamId
     *
     * @return null
     */
    function getPaymentType(int $teamId)
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        $paymentSettings = $PaymentSetting->getByTeamId($teamId);

        if (empty($paymentSettings)) {
            return null;
        }

        return $paymentSettings['type'];
    }

    /**
     * Get amount per user by team or default
     *
     * @param int    $teamId
     * @param string $country
     *
     * @return int
     */
    function getAmountPerUserBeforePayment(int $teamId, string $country): int
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init("CampaignService");

        // Campaign team have no price for individual users
        if ($CampaignService->isCampaignTeam($teamId)) {
            return 0;
        }

        $teamAmountPerUser = $Team->getAmountPerUser($teamId);
        if ($teamAmountPerUser !== null) {
            return $teamAmountPerUser;
        }

        $defaultAmountPerUser = $this->getDefaultAmountPerUserByCountry($country);
        return $defaultAmountPerUser;
    }

    /**
     * Get payment base date of this month
     *
     * @param int $teamId
     * @param int $currentTimeStamp
     *
     * @return string
     */
    public function getCurrentMonthBaseDate(int $teamId, int $currentTimeStamp): string
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $teamTimezone = Hash::get($Team->getById($teamId), 'Team.timezone');
        $localCurrentTs = $currentTimeStamp + ($teamTimezone * HOUR);

        $paymentSetting = $PaymentSetting->getUnique($teamId);

        $paymentBaseDay = Hash::get($paymentSetting, 'payment_base_day');

        $paymentBaseDate = AppUtil::correctInvalidDate(
            date('Y', $localCurrentTs),
            date('m', $localCurrentTs),
            $paymentBaseDay
        );

        return $paymentBaseDate;
    }
}
