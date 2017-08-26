<?php
App::import('Service', 'AppService');
App::import('Service', 'CreditCardService');
App::import('Service', 'InvoiceService');
App::uses('PaymentSetting', 'Model');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('CreditCard', 'Model');
App::uses('ChargeHistory', 'Model');
App::uses('AppUtil', 'Util');

use Goalous\Model\Enum as Enum;

/**
 * Class PaymentService
 */
class PaymentService extends AppService
{
    const AMOUNT_PER_USER_JPY = 1980;
    // TODO.Payment: Fix amount per user case $ after final decision
    const AMOUNT_PER_USER_USD = 16;

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
    public function validateCreateCc($data)
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");

        // Validates model
        $PaymentSetting->set($data);
        $PaymentSetting->validate = am($PaymentSetting->validate, $PaymentSetting->validateCreate);

        $companyCountry = Hash::get($data, 'company_country');
        if ($companyCountry === 'JP') {
            $PaymentSetting->validate = am($PaymentSetting->validate, $PaymentSetting->validateJp);
        }
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
     * Create a payment settings as its related credit card
     *
     * @param        $data
     * @param string $customerCode
     * @param int    $userId
     *
     * @return bool
     */
    public function registerCreditCardPayment($data, string $customerCode, int $userId)
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
                'customer_code'      => $customerCode,
            ];

            $CreditCard->begin();
            if (!$CreditCard->save($creditCardData)) {
                $CreditCard->rollback();
                $PaymentSetting->rollback();
                throw new Exception(sprintf("Failed create credit card. data:%s",
                    AppUtil::varExportOneLine($creditCardData)));
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
        // Ex. 3people × ¥1,980 × 20 days / 1month
        $subTotalCharge = $userCnt * $paymentSetting['amount_per_user'] * ($useDaysByNext / $allUseDays);
        $subTotalCharge = $this->processDecimalPointForAmount($paymentSetting['currency'], $subTotalCharge);

        $tax = $this->calcTax($paymentSetting['company_country'], $subTotalCharge);
        $totalCharge = $subTotalCharge + $tax;
        return $totalCharge;
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
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
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
     * @param int $charge
     * @param int $currencyType
     *
     * @return string
     */
    public function formatCharge(float $charge, int $currencyType): string
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        // Format ex 1980 → ¥1,980
        $res = PaymentSetting::CURRENCY_SYMBOLS_EACH_TYPE[$currencyType] . number_format($charge);
        return $res;
    }

    /**
     * Apply Credit card charge for a specified team.
     *
     * @param int                               $teamId
     * @param Enum\ChargeHistory\ChargeType|int $chargeType
     * @param int                               $usersCount
     *
     * @return array
     * @throws Exception
     */
    public function applyCreditCardCharge(
        int $teamId,
        Enum\ChargeHistory\ChargeType $chargeType,
        int $usersCount
    ) {
        try {
            // Validate user count
            if ($usersCount <= 0) {
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

            $creditCard = Hash::get($paymentSettings, 'CreditCard');
            $customerId = Hash::get($creditCard, 'customer_code');
            $paySetting = Hash::get($paymentSettings, 'PaymentSetting');
            $amountPerUser = Hash::get($paySetting, 'amount_per_user');
            $currency = Hash::get($paySetting, 'currency');
            $currencyName = $currency == PaymentSetting::CURRENCY_TYPE_JPY ? PaymentSetting::CURRENCY_JPY : PaymentSetting::CURRENCY_USD;

            // Apply the user charge on Stripe
            /** @var CreditCardService $CreditCardService */
            $CreditCardService = ClassRegistry::init("CreditCardService");
            $chargeInfo = $this->calcRelatedTotalChargeByUserCnt($teamId, $usersCount,
                $paySetting);

            $maxChargeUserCnt = $this->getChargeMaxUserCnt($teamId, $chargeType, $usersCount);
            // ChargeHistory temporary insert
            $historyData = [
                'team_id'          => $teamId,
                'payment_type'     => Enum\PaymentSetting\Type::CREDIT_CARD,
                'charge_type'      => $chargeType->getValue(),
                'amount_per_user'  => $amountPerUser,
                'total_amount'     => $chargeInfo['sub_total_charge'],
                'tax'              => $chargeInfo['tax'],
                'charge_users'     => $usersCount,
                'currency'         => $currency,
                'charge_datetime'  => time(),
                'result_type'      => Enum\ChargeHistory\ResultType::ERROR,
                'max_charge_users' => $maxChargeUserCnt
            ];

            /** @var ChargeHistory $ChargeHistory */
            $ChargeHistory = ClassRegistry::init('ChargeHistory');
            if (!$ChargeHistory->save($historyData)) {
                throw new Exception(sprintf("Failed create charge history. data:%s",
                    AppUtil::varExportOneLine($historyData)));
            }

            /* Charge */
            $paymentDescription = "Team: $teamId Unit: $amountPerUser Users: $usersCount";
            $chargeRes = $CreditCardService->chargeCustomer($customerId, $currencyName, $chargeInfo['total_charge'],
                $paymentDescription);

            // Save charge history
            if ($chargeRes['error'] === true) {

                /* Transaction rollback */
                $this->TransactionManager->rollback();

                throw new Exception(
                    sprintf("Failed to charge. data:%s",
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

            if ($chargeRes['success'] === true) {
                $resultType = Enum\ChargeHistory\ResultType::SUCCESS;
            } else {
                $resultType = Enum\ChargeHistory\ResultType::FAIL;
            }

            $historyId = $ChargeHistory->getLastInsertID();
            // Update Charge history
            $updateHistory = ['id' => $historyId, 'result_type' => $resultType];
            if (!$ChargeHistory->save($updateHistory, false)) {

                /* TODO.Payment: Insert error log to table */

                throw new Exception(sprintf("Failed update charge history. data:%s",
                    AppUtil::varExportOneLine($updateHistory)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get charge max user cnt by charge type
     *
     * @param int                           $teamId
     * @param Enum\ChargeHistory\ChargeType $chargeType
     * @param int                           $usersCount
     *
     * @return array
     */
    public function getChargeMaxUserCnt(
        int $teamId,
        Enum\ChargeHistory\ChargeType $chargeType,
        int $usersCount
    ) {
        if ($chargeType->getValue() == Enum\ChargeHistory\ChargeType::MONTHLY_FEE) {
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
    ) {
        $result = [
            'error'     => false,
            'errorCode' => 200,
            'message'   => null
        ];

        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init("CreditCardService");
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        // Register Credit Card to stripe
        // Set description as "Team ID: 2" to identify it on Stripe Dashboard
        $contactEmail = Hash::get($paymentData, 'contact_person_email');
        $customerDescription = "Team ID: $teamId";
        $stripeResponse = $CreditCardService->registerCustomer($creditCardToken, $contactEmail, $customerDescription);
        if ($stripeResponse['error'] === true) {
            $result['error'] = true;
            $result['message'] = $stripeResponse['message'];
            $result['errorCode'] = 400;
            return $result;
        }

        // Stripe customer id
        $customerId = $stripeResponse['customer_id'];
        if (empty($customerId)) {
            // It never should happen
            $result['error'] = true;
            $result['message'] = __("An error occurred while processing.");
            $result['errorCode'] = 500;

            $this->log(sprintf("Error on Stripe call: %s", AppUtil::varExportOneLine($stripeResponse)));
            return $result;
        }

        // Variable to later use
        $result['customerId'] = $customerId;

        $companyCountry = Hash::get($paymentData, 'company_country');
        $paymentData['amount_per_user'] = $amountPerUser = $this->getDefaultAmountPerUserByCountry($companyCountry);
        $paymentData['currency'] = $currency = $this->getCurrencyTypeByCountry($companyCountry);

        $membersCount = $TeamMember->countChargeTargetUsersEachTeam([$teamId]);
        $membersCount = $membersCount[$teamId];
        $formattedAmountPerUser = $this->formatCharge($amountPerUser, $currency);
        $chargeInfo = $this->calcRelatedTotalChargeByUserCnt($teamId, $membersCount, $paymentData);
        $historyData = [
            'team_id'          => $teamId,
            'user_id'          => $userId,
            'payment_type'     => PaymentSetting::PAYMENT_TYPE_CREDIT_CARD,
            'charge_type'      => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'  => $amountPerUser,
            'total_amount'     => $chargeInfo['sub_total_charge'],
            'tax'              => $chargeInfo['tax'],
            'charge_users'     => $membersCount,
            'currency'         => $currency,
            'charge_datetime'  => time(),
            'result_type'      => 0,
            'max_charge_users' => $membersCount
        ];

        // Register payment settings
        try {
            // Create PaymentSettings
            $PaymentSetting->begin();
            if (!$PaymentSetting->save($paymentData)) {
                throw new Exception(sprintf("Failed create payment settings. data: %s",
                    AppUtil::varExportOneLine($paymentData)));
            }
            $paymentSettingId = $PaymentSetting->getLastInsertID();

            // Create CreditCards
            $creditCardData = [
                'team_id'            => $paymentData['team_id'],
                'payment_setting_id' => $paymentSettingId,
                'customer_code'      => $customerId
            ];

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
            $timezone = $Team->getTimezone();
            $date = AppUtil::todayDateYmdLocal($timezone);
            if (!$Team->updatePaidPlan($teamId, $date)) {
                throw new Exception(sprintf("Failed to update team status to paid plan. team_id: %s", $teamId));
            }

            // Apply the user charge on Stripe
            /** @var CreditCardService $CreditCardService */
            $CreditCardService = ClassRegistry::init("CreditCardService");
            $paymentDescription = "Team: $teamId Unit: $formattedAmountPerUser Users: $membersCount";
            $currencyName = $currency == PaymentSetting::CURRENCY_TYPE_JPY ? PaymentSetting::CURRENCY_JPY : PaymentSetting::CURRENCY_USD;
            $chargeResult = $CreditCardService->chargeCustomer($customerId, $currencyName, $chargeInfo['total_charge'],
                $paymentDescription);

            // Error charging customer using Stripe API. Might be network,  API problem or card rejected
            if ($chargeResult['error'] === true || $chargeResult['success'] == false) {
                // Rollback transaction
                $PaymentSetting->rollback();

                // Remove the customer from Stripe
                $CreditCardService->deleteCustomer($customerId);

                // Save history
                if ($chargeResult['error'] === true) {
                    $historyData['result_type'] = Enum\ChargeHistory\ResultType::ERROR;
                } else {
                    $historyData['result_type'] = Enum\ChargeHistory\ResultType::FAIL;
                }
                $this->_saveChargeHistory($historyData);

                $result['error'] = true;
                $result['message'] = $chargeResult['message'];
                $result['errorCode'] = 400;
                return $result;
            }

            // Commit changes
            $PaymentSetting->commit();
        } catch (Exception $e) {
            // Remove the customer from Stripe
            $CreditCardService->deleteCustomer($customerId);

            $PaymentSetting->rollback();
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());

            $result['error'] = true;
            $result['message'] = __("Failed to register paid plan.") . " " . __("Please try again later.");
            $result['errorCode'] = 500;
            return $result;
        }

        // Save card history
        // Charge history is kept outside the transaction so in case of history recording
        // failure, the error will be logged for later investigation but the charging
        // processes will not be affected.
        $historyData['result_type'] = Enum\ChargeHistory\ResultType::SUCCESS;
        $this->_saveChargeHistory($historyData);

        // Delete cache
        $Team->resetCurrentTeam();

        return $result;
    }

    /**
     * Create Payment Setting, Invoice records and register an invoice for the team.
     *
     * @param int   $userId
     * @param int   $teamId
     * @param array $paymentData
     *
     * @return
     * $result = [
     *       'errorCode' => 200,
     *       'message'   => null
     *  ];
     * or
     * true
     */
    public function registerInvoicePayment(int $userId, int $teamId, array $paymentData)
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $membersCount = $TeamMember->countChargeTargetUsersEachTeam([$teamId]);
        $membersCount = $membersCount[$teamId];

        try {
            $PaymentSetting->begin();

            // Save Payment Settings
            if (!$PaymentSetting->save($paymentData)) {
                throw new Exception(sprintf("Failed create payment settings. data: %s",
                    AppUtil::varExportOneLine($paymentData)));
            }
            $paymentSettingId = $PaymentSetting->getLastInsertID();

            // Create Invoice
            $invoiceData = $paymentData;
            $invoiceData['payment_setting_id'] = $paymentSettingId;
            $invoiceData['credit_status'] = Invoice::CREDIT_STATUS_WAITING;
            if (!$Invoice->save($invoiceData)) {
                throw new Exception(sprintf("Failed create invoice record. data: %s",
                    AppUtil::varExportOneLine($paymentData)));
            }

            // Save snapshot
            /** @var PaymentSettingChangeLog $PaymentSettingChangeLog */
            $PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
            $PaymentSettingChangeLog->saveSnapshot($paymentSettingId, $userId);

            // Set team status
            $timezone = $Team->getTimezone();
            $date = AppUtil::todayDateYmdLocal($timezone);
            if (!$Team->updatePaidPlan($teamId, $date)) {
                throw new Exception(sprintf("Failed to update team status to paid plan. team_id: %s", $teamId));
            }


            $res = $this->registerInvoice($teamId, $membersCount, REQUEST_TIMESTAMP);
            if ($res == false) {
                throw new Exception(sprintf("Error creating invoice payment: ",
                    AppUtil::varExportOneLine($paymentData)));
            }

            $PaymentSetting->commit();
        } catch (Exception $e) {
            $PaymentSetting->rollback();

            // TODO: Payment: add message translations
            $result = [];
            $result['errorCode'] = 500;
            $result['message'] = __("Failed to register paid plan.") . " " . __("Please try again later.");
            return $result;
        }

        return true;
    }

    /**
     * Register Invoice including requesting to atobarai.com and saving data in the following:
     * - charge_histories -> monthly charge
     * - invoice_histories -> status of response of atobarai.com
     * - invoice_histories_charge_histories -> intermediate table for invoice_histories and charge_histories.
     *
     * @param int $teamId
     * @param int $chargeMemberCount
     * @param int $time
     *
     * @return bool
     * @internal param float $timezone
     */
    public function registerInvoice(int $teamId, int $chargeMemberCount, int $time): bool
    {
        // Invoices for only Japanese team. So, $timezone will be always Japan time.
        $timezone = 9;

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

        $localCurrentDate = AppUtil::dateYmdLocal($time, $timezone);
        // if already send an invoice, return
        if ($InvoiceService->isSentInvoice($teamId, $localCurrentDate)) {
            return false;
        }
        $chargeInfo = $this->calcRelatedTotalChargeByUserCnt($teamId, $chargeMemberCount);
        $paymentSetting = $PaymentSetting->getByTeamId($teamId);

        $targetChargeHistories = $PaymentService->findTargetInvoiceChargeHistories($teamId, $time);

        $ChargeHistory->begin();
        try {
            // save monthly charge
            $ChargeHistory->clear();
            $monthlyChargeHistory = $ChargeHistory->addInvoiceMonthlyCharge(
                $teamId,
                $time,
                $chargeInfo['sub_total_charge'],
                $chargeInfo['tax'],
                $paymentSetting['amount_per_user'],
                $chargeMemberCount
            );
            if (!$monthlyChargeHistory) {
                throw new Exception(sprintf("Failed to save monthly charge history. validationErrors: %s"),
                    AppUtil::varExportOneLine($ChargeHistory->validationErrors)
                );
            }

            // monthly dates
            $monthlyChargeHistory['monthlyStartDate'] = $localCurrentDate;
            $nextMonthTs = strtotime('+ 1 month', strtotime($localCurrentDate));
            $nextBaseDate = AppUtil::correctInvalidDate(
                date('Y', $nextMonthTs),
                date('m', $nextMonthTs),
                $paymentSetting['payment_base_day']
            );
            $monthlyChargeHistory['monthlyEndDate'] = AppUtil::dateYesterday($nextBaseDate);

            // save the invoice history
            $invoiceHistoryData = [
                'team_id'           => $teamId,
                'order_date'        => $localCurrentDate,
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
            $resAtobarai = $InvoiceService->registerOrder(
                $teamId,
                $targetChargeHistories,
                $monthlyChargeHistory,
                $localCurrentDate
            );
            if ($resAtobarai['status'] == 'error') {
                throw new Exception(sprintf("Request to atobarai.com was failed. errorMsg: %s, chargeHistories: %s, requestData: %s",
                    AppUtil::varExportOneLine($resAtobarai['messages']),
                    AppUtil::varExportOneLine($targetChargeHistories),
                    AppUtil::varExportOneLine($resAtobarai['requestData'])
                ));
            }

        } catch (Exception $e) {
            $ChargeHistory->rollback();
            $this->log(sprintf("Failed monthly charge of invoice. teamId: %s, errorDetail: %s",
                $teamId,
                $e->getMessage()
            ));
            return false;
        }

        $ChargeHistory->commit();

        // update system order code.
        $invoiceHistoryUpdate = [
            'id'                => $invoiceHistoryId,
            'system_order_code' => $resAtobarai['systemOrderId'],
        ];
        $InvoiceHistory->clear();
        $resUpdate = $InvoiceHistory->save($invoiceHistoryUpdate);
        if (!$resUpdate) {
            $this->log(sprintf("Failed update invoice history. It should be recovered!!! teamId: %s, data: %s, validationErrors: %s",
                $teamId,
                AppUtil::varExportOneLine($invoiceHistoryUpdate),
                AppUtil::varExportOneLine($InvoiceHistory->validationErrors)
            ));
        }

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
    ) {
        // Invoices for only Japanese team. So, $timezone will be always Japan time.
        $timezone = 9;

        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');
        $localCurrentDate = AppUtil::dateYmdLocal($time, $timezone);
        // fetching charge histories
        $yesterdayLocalDate = AppUtil::dateYesterday($localCurrentDate);
        $targetEndTs = AppUtil::getEndTimestampByTimezone($yesterdayLocalDate, $timezone);
        $previousMonthFirstTs = strtotime("-1 month", strtotime(date('Y-m-01', strtotime($yesterdayLocalDate))));

        // target start date will be base day in previous month
        $targetStartDate = AppUtil::correctInvalidDate(
            date('Y', $previousMonthFirstTs),
            date('m', $previousMonthFirstTs),
            date('d', strtotime($localCurrentDate))
        );
        $targetStartTs = AppUtil::getStartTimestampByTimezone($targetStartDate, $timezone);
        $targetPaymentHistories = $ChargeHistory->findForInvoiceByStartEnd($teamId, $targetStartTs, $targetEndTs);
        return $targetPaymentHistories;
    }

    /**
     * Save Charge history
     *
     * @param $historyData
     *
     * @return bool
     */
    private function _saveChargeHistory($historyData)
    {
        /** @var ChargeHistory $ChargeHistory */
        $ChargeHistory = ClassRegistry::init('ChargeHistory');

        try {
            // Create Charge history
            $ChargeHistory->begin();

            if (!$ChargeHistory->save($historyData)) {
                $ChargeHistory->rollback();
                throw new Exception(sprintf("Failed create charge history. data:%s",
                    AppUtil::varExportOneLine($historyData)));
            }
            $ChargeHistory->commit();
        } catch (Exception $e) {
            $ChargeHistory->rollback();
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());

            return false;
        }
        return true;
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
        $targetChargeTeams = $PaymentSetting->findMonthlyChargeTeams(Enum\PaymentSetting\Type::CREDIT_CARD());

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
    public function findMonthlyChargeInvoiceTeams(int $time = REQUEST_TIMESTAMP): array
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        /** @var InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init("InvoiceHistory");
        // Get teams only credit card payment type
        $targetChargeTeams = $PaymentSetting->findMonthlyChargeTeams(Enum\PaymentSetting\Type::INVOICE());
        // Filtering
        $targetChargeTeams = array_filter($targetChargeTeams,
            function ($v) use ($time, $InvoiceHistory) {
                // Invoices for only Japanese team. So, $timezone will be always Japan time.
                $timezone = 9;

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
    public function updatePayerInfo(int $teamId, int $userId, array $payerData)
    {
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init("PaymentSetting");
        $paySetting = $PaymentSetting->getUnique($teamId);
        // Check if payment exists
        if (empty($paySetting)) {
            return ['errorCode' => 400, 'message' => __('Payment settings does not exists.')];
        }

        $data = [
            'id'                             => $paySetting['id'],
            'team_id'                        => $paySetting['team_id'],
            'type'                           => $paySetting['type'],
            'company_name'                   => $payerData['company_name'],
            'company_country'                => $payerData['company_country'],
            'company_post_code'              => $payerData['company_post_code'],
            'company_region'                 => $payerData['company_region'],
            'company_city'                   => $payerData['company_city'],
            'company_street'                 => $payerData['company_street'],
            'company_tel'                    => $payerData['company_tel'],
            'contact_person_first_name'      => $payerData['contact_person_first_name'],
            'contact_person_first_name_kana' => $payerData['contact_person_first_name_kana'],
            'contact_person_last_name'       => $payerData['contact_person_last_name'],
            'contact_person_last_name_kana'  => $payerData['contact_person_last_name_kana'],
            'contact_person_tel'             => $payerData['contact_person_tel'],
            'contact_person_email'           => $payerData['contact_person_email'],
        ];

        try {
            // Update PaymentSettings
            $PaymentSetting->begin();

            // Save Payment Settings
            if (!$PaymentSetting->save($data)) {
                throw new Exception(sprintf("Fail to update payment settings. data: %s",
                    AppUtil::varExportOneLine($data)));
            }
            $paymentSettingId = $PaymentSetting->getLastInsertID();

            // Save snapshot
            /** @var PaymentSettingChangeLog $PaymentSettingChangeLog */
            $PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
            $PaymentSettingChangeLog->saveSnapshot($paymentSettingId, $userId);

            $PaymentSetting->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());

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
     * @param int                               $teamId
     * @param Enum\ChargeHistory\ChargeType|int $chargeType
     * @param int                               $usersCount
     *
     * @return array
     * @throws Exception
     */
    public function charge(
        int $teamId,
        Enum\ChargeHistory\ChargeType $chargeType,
        int $usersCount
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

        if (Hash::get($paymentSetting, 'type') == Enum\PaymentSetting\Type::CREDIT_CARD) {
            $res = $this->applyCreditCardCharge(
                $teamId,
                $chargeType,
                $usersCount
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
                $chargeInfo = $this->calcRelatedTotalChargeByUserCnt($teamId, $usersCount,
                    $paymentSetting);

                $maxChargeUserCnt = $this->getChargeMaxUserCnt($teamId, $chargeType, $usersCount);
                // Insert ChargeHistory
                $historyData = [
                    'team_id'          => $teamId,
                    'payment_type'     => Enum\PaymentSetting\Type::INVOICE,
                    'charge_type'      => $chargeType,
                    'amount_per_user'  => $paymentSetting['amount_per_user'],
                    'total_amount'     => $chargeInfo['sub_total_charge'],
                    'tax'              => $chargeInfo['tax'],
                    'charge_users'     => $usersCount,
                    'currency'         => $paymentSetting['currency'],
                    'charge_datetime'  => time(),
                    'result_type'      => Enum\ChargeHistory\ResultType::SUCCESS,
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

    /*
     * Update invoice company information
     *
     * @param int   $teamId
     * @param array $invoiceData
     *
     * @return array|bool
     */
    public function updateInvoice(int $teamId, array $invoiceData)
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
            $Invoice->begin();
            if (!$Invoice->save($data)) {
                throw new Exception(sprintf("Fail to update invoice. data: %s",
                    AppUtil::varExportOneLine($data)));
            }
            $Invoice->commit();
        } catch (Exception $e) {
            $Invoice->rollback();
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
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

        $allValidationErrors = [];
        // PaymentSetting validation
        if (!empty(Hash::get($fields, 'PaymentSetting'))) {
            $companyCountry = Hash::get($data, 'payment_setting.company_country');
            if ($companyCountry === 'JP') {
                $PaymentSetting->validate = am($PaymentSetting->validate, $PaymentSetting->validateJp);
            }
            $allValidationErrors = am(
                $allValidationErrors,
                $this->validateSingleModelFields($data, $fields, 'payment_setting', 'PaymentSetting', $PaymentSetting)
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
            $companyCountry = Hash::get($data, 'payment_setting.company_country');
            if ($companyCountry === 'JP') {
                $Invoice->validate = am($Invoice->validate, $Invoice->validateJp);
            }

            $allValidationErrors = am(
                $allValidationErrors,
                $this->validateSingleModelFields($data, $fields, 'invoice', 'Invoice', $Invoice)
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
        $defaultAamountPerUser = $this->getDefaultAmountPerUserByCountry($userCountryCode);

        if (!$teamId) {
            return $defaultAamountPerUser;
        }

        $teamAmountPerUser = $PaymentSetting->getAmountPerUser($teamId);
        if ($teamAmountPerUser !== null) {
            return $teamAmountPerUser;
        }

        $teamCountry = $Team->getCountry($teamId);
        if ($teamCountry) {
            return $this->getDefaultAmountPerUserByCountry($teamCountry);
        }

        return $defaultAamountPerUser;
    }

}
