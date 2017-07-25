<?php
App::import('Service', 'AppService');
App::uses('Team', 'Model');
App::uses('PaymentSetting', 'Model');
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
    public function get($teamId): array
    {
        if (empty($teamId)) {
            return [];
        }

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
    public function createCreditCardPayment($data, $customerCode, $userId)
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
    public function getUseDaysByNext(int $currentTimeStamp = REQUEST_TIMESTAMP): int
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
            list($y, $m) = AppUtil::nextYm($y, $m);
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
     *
     * @param int $currentTimeStamp
     *
     * @return int
     */
    public function getAllUseDaysOfMonth(int $currentTimeStamp = REQUEST_TIMESTAMP): int
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $nextBaseDate = $this->getNextBaseDate($currentTimeStamp);
        list($y, $m, $d) = explode('-', $nextBaseDate);
        list($y, $m) = AppUtil::prevYm($y, $m);

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
     * @param int $userCnt
     * @param int $currentTimeStamp
     *
     * @return float
     */
    public function calcTotalChargeByAddUsers(int $userCnt, int $currentTimeStamp = REQUEST_TIMESTAMP) : float
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $useDaysByNext = $this->getUseDaysByNext($currentTimeStamp);
        $allUseDaysOfMonth = $this->getAllUseDaysOfMonth($currentTimeStamp);

        $paymentSetting = $this->get($Team->current_team_id);
        $totalCharge = $userCnt * $paymentSetting['amount_per_user'] * ($useDaysByNext / $allUseDaysOfMonth);

        // Ex. 3people × ¥1,980 × 20 days / 1month
        if ($paymentSetting['currency'] == PaymentSetting::CURRENCY_JPY) {
            $totalCharge = AppUtil::floor($totalCharge, 0);
        } else {
            $totalCharge = AppUtil::floor($totalCharge,2);
        }
        return $totalCharge;
    }

    /**
     * Format total charge by users count when invite users.
     *
     * @param int $userCnt
     * @param int $currentTimeStamp
     *
     * @return string
     */
    public function formatTotalChargeByAddUsers(int $userCnt, int $currentTimeStamp = REQUEST_TIMESTAMP) : string
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $paymentSetting = $this->get($Team->current_team_id);
        $totalCharge = $this->calcTotalChargeByAddUsers($userCnt, $currentTimeStamp);
        // Format ex 1980 → ¥1,980
        $res =  PaymentSetting::CURRENCY_LABELS[$paymentSetting['currency']] . number_format($totalCharge);
        return $res;
    }

}
