<?php
App::import('Service', 'AppService');
App::import('Service', 'CreditCardService');
App::uses('PaymentSetting', 'Model');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('CreditCard', 'Model');
App::uses('AppUtil', 'Util');

/**
 * Class PaymentService
 */
class PaymentService extends AppService
{
    /* Payment settings variable cache */
    private static $cacheList = [];

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
     * Get use days from current date to next payment base date
     *
     * @param int $currentTimeStamp
     *
     * @return int
     */
    public function getUseDaysByNextBaseDate(int $currentTimeStamp = REQUEST_TIMESTAMP): int
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $timezone = $Team->getTimezone();
        $localCurrentDate = AppUtil::dateYmdLocal($currentTimeStamp, $timezone);
        $nextBaseDate = $this->getNextBaseDate($currentTimeStamp);
        // Calc use days
        $diffDays = AppUtil::diffDays($localCurrentDate, $nextBaseDate);
        return $diffDays;
    }

    /**
     * Get next payment base date
     *
     * @param int $currentTimeStamp
     *
     * @return string
     */
    public function getNextBaseDate(int $currentTimeStamp = REQUEST_TIMESTAMP): string
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $timezone = $Team->getTimezone();
        $localCurrentDate = AppUtil::dateYmdLocal($currentTimeStamp, $timezone);
        list($y, $m, $d) = explode('-', $localCurrentDate);

        $paymentSetting = $this->get($Team->current_team_id);
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
     * Get total days from previous payment base date to next payment base date
     * 現在月度の総利用日数
     *
     * @param int $currentTimeStamp
     *
     * @return int
     */
    public function getCurrentAllUseDays(int $currentTimeStamp = REQUEST_TIMESTAMP): int
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $nextBaseDate = $this->getNextBaseDate($currentTimeStamp);
        list($y, $m, $d) = explode('-', $nextBaseDate);
        list($y, $m) = AppUtil::moveMonthYm($y, $m, -1);

        $paymentSetting = $this->get($Team->current_team_id);
        $paymentBaseDay = Hash::get($paymentSetting, 'payment_base_day');

        if (checkdate($m, $paymentBaseDay, $y) === false) {
            AppUtil::dateFromYMD($y, $m, 1);
            $prevBaseDate = AppUtil::dateMonthLast(AppUtil::dateFromYMD($y, $m, 1));
        } else {
            $prevBaseDate = AppUtil::dateFromYMD($y, $m, $paymentBaseDay);
        }

        $res = AppUtil::diffDays($prevBaseDate, $nextBaseDate);
        return $res;
    }

    /**
     * Calc total charge by users count when invite users.
     *
     * @param int  $userCnt
     * @param int  $currentTimeStamp
     * @param null $useDaysByNext
     * @param null $allUseDays
     *
     * @return float
     */
    public function calcTotalChargeByAddUsers
    (
        int $userCnt,
        int $currentTimeStamp = REQUEST_TIMESTAMP,
        $useDaysByNext = null,
        $allUseDays = null
    ): float {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $useDaysByNext = $useDaysByNext ?? $this->getUseDaysByNextBaseDate($currentTimeStamp);
        $allUseDays = $allUseDays ?? $this->getCurrentAllUseDays($currentTimeStamp);

        $paymentSetting = $this->get($Team->current_team_id);
        $totalCharge = $userCnt * $paymentSetting['amount_per_user'] * ($useDaysByNext / $allUseDays);

        // Ex. 3people × ¥1,980 × 20 days / 1month
        if ($paymentSetting['currency'] == PaymentSetting::CURRENCY_JPY) {
            $totalCharge = AppUtil::floor($totalCharge, 0);
        } else {
            $totalCharge = AppUtil::floor($totalCharge, 2);
        }
        return $totalCharge;
    }

    /**
     * Format total charge by users count when invite users.
     *
     * @param int  $userCnt
     * @param int  $currentTimeStamp
     * @param null $useDaysByNext
     * @param null $allUseDays
     *
     * @return string
     */
    public function formatTotalChargeByAddUsers(
        int $userCnt,
        int $currentTimeStamp = REQUEST_TIMESTAMP,
        $useDaysByNext = null,
        $allUseDays = null
    ): string {
        $totalCharge = $this->calcTotalChargeByAddUsers($userCnt, $currentTimeStamp, $useDaysByNext, $allUseDays);
        // Format ex 1980 → ¥1,980
        $res = $this->formatCharge($totalCharge);
        return $res;
    }

    /**
     * Format charge based payment setting
     * - Number format
     * - Currency format
     *
     * @param int $charge
     *
     * @return string
     */
    public function formatCharge(int $charge): string
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $paymentSetting = $this->get($Team->current_team_id);
        // Format ex 1980 → ¥1,980
        $res = PaymentSetting::CURRENCY_LABELS[$paymentSetting['currency']] . number_format($charge);
        return $res;
    }

    /*
     * Apply Credit card charge for a specified team.
     *
     * @param int         $teamId
     * @param int         $chargeType
     * @param int         $usersCount
     * @param string      $description
     *
     * @return array
     */
    public function applyCreditCardCharge(int $teamId, int $chargeType, int $usersCount, string $description = "")
    {
        $result = [
            'error'     => false,
            'errorCode' => 200,
            'message'   => null
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
            $result['message'] = __('Credit card settings does not exist.');
            $result['errorCode'] = 500;

            return $result;
        }
        $creditCard = $paymentSettings['CreditCard'][0];
        $customerId = Hash::get($creditCard, 'customer_code');
        $amountPerUser = Hash::get($paymentSettings, 'PaymentSetting.amount_per_user');
        $currency = Hash::get($paymentSettings, 'PaymentSetting.currency');
        $currencyName = $currency == PaymentSetting::CURRENCY_CODE_JPY ? PaymentSetting::CURRENCY_JPY : PaymentSetting::CURRENCY_USD;

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
        } else {
            if ($charge['success'] === true) {
                $resultType = ChargeHistory::TRANSACTION_RESULT_SUCCESS;
                $result['success'] = true;
            } else {
                $resultType = ChargeHistory::TRANSACTION_RESULT_FAIL;
                $result['success'] = false;
            }
        }
        $result['resultType'] = $resultType;

        $historyData = [
            'team_id'          => $teamId,
            'payment_type'     => PaymentSetting::PAYMENT_TYPE_CREDIT_CARD,
            'charge_type'      => $chargeType,
            'amount_per_user'  => $amountPerUser,
            'total_amount'     => $totalAmount,
            'charge_users'     => $usersCount,
            'currency'         => $currency,
            'charge_datetime'  => time(),
            'result_type'      => $resultType,
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
    public function findMonthlyChargeCcTeams(int $time = REQUEST_TIMESTAMP): array
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init("ChargeHistory");
        // Get teams only credit card payment type
        $targetChargeTeams = $PaymentSetting->findMonthlyChargeCcTeams();

        // Filtering
        $targetChargeTeams = array_filter($targetChargeTeams, function ($v) use ($time, $ChargeHistory) {
            $timezone = Hash::get($v, 'Team.timezone');
            $localCurrentTs = $time + ($timezone * HOUR);
            $paymentBaseDay = Hash::get($v, 'PaymentSetting.payment_base_day');
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
}
