<?php
App::import('Service', 'AppService');
App::uses('CampaignTeam', 'Model');
App::uses('CampaignPricePlan', 'Model');
App::uses('CampaignPriceGroup', 'Model');
App::uses('TeamMember', 'Model');
App::uses('PaymentUtil', 'Util');
App::import('Service', 'PaymentService');

use Goalous\Enum as Enum;

/**
 * Class CampaignService
 */
class CampaignService extends AppService
{
    // Cached campaign price plans each group id
    private $cache_plans = [];

    /**
     * Return true if the team is entitled to monthly campaign
     *
     * @param int $teamId
     *
     * @return bool
     */
    function isCampaignTeam(int $teamId): bool
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init('CampaignTeam');

        return $CampaignTeam->isCampaignTeam($teamId);
    }

    /**
     * Get Campaign assigned to the team.
     *
     * @param int   $teamId
     * @param array $fields
     *
     * @return array
     */
    function getCampaignTeam(int $teamId, array $fields = []): array
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init('CampaignTeam');

        return $CampaignTeam->getByTeamId($teamId, $fields);
    }

    /**
     * For unit test
     * @return array
     */
    function getCachePlans(): array
    {
        return $this->cache_plans;
    }

    /**
     * For unit test
     */
    function clearCachePlans()
    {
        $this->cache_plans = [];
    }

    /**
     * Validate upgrade plan
     *
     * @param     $planCode Why no type hinting is that empty value is possible for validation
     *
     * @return array
     */
    function validateUpgradePlan($planCode): array
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init("PricePlanPurchaseTeam");

        $PricePlanPurchaseTeam->set(['price_plan_code' => $planCode]);
        if (!$PricePlanPurchaseTeam->validates()) {
            $errors = $this->validationExtract($PricePlanPurchaseTeam->validationErrors);
            return $errors;
        }
        return [];
    }

    /**
     * Get Price plan information for campaign team
     *
     * @param int $teamId
     *
     * @return array
     */
    function getPricePlanPurchaseTeam(int $teamId): array
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $options = [
            'fields' => [
                'PricePlanPurchaseTeam.id',
                'PricePlanPurchaseTeam.price_plan_code',
                'CampaignTeam.id',
                'CampaignTeam.price_plan_group_id',
            ],
            'joins'  => [
                [
                    'type'       => 'INNER',
                    'table'      => 'campaign_teams',
                    'alias'      => 'CampaignTeam',
                    'conditions' => [
                        'PricePlanPurchaseTeam.team_id = CampaignTeam.team_id',
                        'CampaignTeam.team_id' => $teamId,
                        'CampaignTeam.del_flg' => false,
                    ]
                ]
            ]
        ];

        $res = $PricePlanPurchaseTeam->find('first', $options);
        return $res;
    }

    /**
     * Returns true if the team have purchased a campaign plan
     *
     * @param int $teamId
     *
     * @return bool
     */
    function purchased(int $teamId): bool
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        return $PricePlanPurchaseTeam->purchased($teamId);
    }

    /**
     * Returns the maximum number of users allowed for the team subscribed plan
     *
     * @param int $teamId
     *
     * @return int
     */
    function getMaxAllowedUsers(int $teamId): int
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $purchasedPlan = $PricePlanPurchaseTeam->getByTeamId($teamId, ['price_plan_code']);
        if (empty($purchasedPlan)) {
            return 0;
        }

        $pricePlanCode = $purchasedPlan['price_plan_code'];
        $pricePlan = $this->getPlanByCode($pricePlanCode);
        if (empty($pricePlan)) {
            CakeLog::debug("CampaignPricePlan not found with price plan code: $pricePlanCode");
            return 0;
        }

        $maxMembers = $pricePlan['max_members'];
        return $maxMembers;
    }

    /**
     * Check if additional users will exceed the campaign limit
     *
     * @param int $teamId
     * @param int $additionalUsersCount
     *
     * @return bool
     */
    function willExceedMaximumCampaignAllowedUser(int $teamId, int $additionalUsersCount)
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $currentUserCount = $TeamMember->countHeadCount($teamId);
        $campaignMaximumUsers = CampaignService::getMaxAllowedUsers($teamId);

        return $campaignMaximumUsers < ($currentUserCount + $additionalUsersCount);
    }

    /**
     * Return team current price plan
     *
     * @param int $teamId
     *
     * @return array|null
     */
    function getTeamPricePlan(int $teamId)
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $purchasedPlan = $PricePlanPurchaseTeam->getByTeamId($teamId, ['price_plan_code']);
        if (empty($purchasedPlan)) {
            return null;
        }

        $pricePlanCode = $purchasedPlan['price_plan_code'];
        $pricePlan = $this->getPlanByCode($pricePlanCode);
        if (empty($pricePlan)) {
            CakeLog::debug("CampaignPricePlan not found with price_plan_code: $pricePlanCode");
            return null;
        }

        return $pricePlan;
    }

    /*
     * find price plans belongs team campaign group
     *
     * @param int $teamId
     * @return array
     */
    function findList(int $teamId): array
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        $res = [];
        $campaignTeam = $this->getCampaignTeam($teamId);
        $chargeUserCnt = $TeamMember->countHeadCount($teamId);
        $pricePlans = $this->findAllPlansByGroupId($campaignTeam['price_plan_group_id']);
        foreach ($pricePlans as $plan) {
            $currencyType = $plan['currency'];
            $subTotalCharge = $plan['price'];
            $tax = $currencyType == Enum\Model\PaymentSetting\Currency::JPY ? $PaymentService->calcTax('JP',
                $subTotalCharge) : 0;
            $totalCharge = $subTotalCharge + $tax;
            $formatSubTotal = $PaymentService->formatCharge($subTotalCharge, $currencyType);
            $res[] = am($plan, [
                'can_select' => ($chargeUserCnt <= $plan['max_members']),
                'format_price' => $formatSubTotal,
                'sub_total_charge' => $formatSubTotal,
                'tax'              => $PaymentService->formatCharge($tax, $currencyType),
                'total_charge'     => $PaymentService->formatCharge($totalCharge, $currencyType),
            ]);
        }
        return $res;
    }

    /*
     * find price plans by group id
     *
     * @param int $groupId
     * @return array
     */
    function findAllPlansByGroupId(int $groupId): array
    {
        // Get cached data from class variable
        $plans = Hash::get($this->cache_plans, $groupId);
        if (!empty($plans)) {
            return $plans;
        }

        // Get cached data from Redis
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");
        $plans = $GlRedis->getMstCampaignPlans($groupId);
        if (!empty($plans)) {
            // Set class variable to cache
            $this->cache_plans[$groupId] = $plans;
            return $plans;
        }

        // Get DB data
        /** @var ViewCampaignPricePlan $ViewCampaignPricePlan */
        $ViewCampaignPricePlan = ClassRegistry::init('ViewCampaignPricePlan');
        $plans = $ViewCampaignPricePlan->findAllPlansByGroupId($groupId);
        if (empty($plans)) {
            return [];
        }

        // Cache data
        $GlRedis->saveMstCampaignPlans($groupId, $plans);
        $this->cache_plans[$groupId] = $plans;

        return $plans;
    }

    /*
     * Get price plan by code
     *
     * @param int $planCode
     * @return array
     */
    function getPlanByCode(string $planCode): array
    {
        $parsedPlanCode = PaymentUtil::parsePlanCode($planCode);
        $groupId = Hash::get($parsedPlanCode, 'group_id');
        if (empty($groupId)) {
            return [];
        }

        $plans = $this->findAllPlansByGroupId($groupId);
        if (empty($plans)) {
            return [];
        }

        $plans = Hash::combine($plans, '{n}.code', '{n}');
        $plan = Hash::get($plans, $planCode) ?? [];
        return $plan;
    }

    /*
     * find price plans for upgrading
     *
     * @param int $teamId
     * @return array
     */
    function findPlansForUpgrading(int $teamId, array $currentPlan): array
    {
        if (empty($currentPlan)) {
            return [];
        }

        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        /** @var ViewCampaignPricePlan $ViewCampaignPricePlan */
        $ViewCampaignPricePlan = ClassRegistry::init('ViewCampaignPricePlan');

        $codeInfo = PaymentUtil::parsePlanCode($currentPlan['code']);
        $pricePlans = $this->findAllPlansByGroupId($codeInfo['group_id']);

        // Calc remaining days by next base data
        foreach ($pricePlans as $k => $plan) {
            $pricePlans[$k]['is_current_plan'] = $currentPlan['id'] == $plan['id'];

            $currencyType = (int)$plan['currency'];
            $pricePlans[$k]['format_price'] = $PaymentService->formatCharge($plan['price'], $currencyType);
            if ($plan['max_members'] <= $currentPlan['max_members']) {
                $pricePlans[$k]['can_select'] = false;
                continue;
            }
            $pricePlans[$k]['can_select'] = true;

            // Calc charge amount
            $chargeInfo = $PaymentService->calcRelatedTotalChargeForUpgradingPlan(
                $teamId,
                new Enum\Model\PaymentSetting\Currency($currencyType),
                $plan['code'],
                $currentPlan['code']
            );

            $pricePlans[$k] = am($pricePlans[$k], [
                'sub_total_charge' => $PaymentService->formatCharge($chargeInfo['sub_total_charge'], $currencyType),
                'tax'              => $PaymentService->formatCharge($chargeInfo['tax'], $currencyType),
                'total_charge'     => $PaymentService->formatCharge($chargeInfo['total_charge'], $currencyType),
            ]);
        }
        return $pricePlans;
    }

    /*
     * Parse price plan code
     * Ex. code "1-2"→ ["group_id" => 1, "detail_no" => 2"]
     * @param string $code
     *
     * @return array
     */
    function parsePlanCode(string $code): array
    {
        try {
            $ar = explode('-', $code);
            if (count($ar) != 2) {
                throw new Exception(sprintf("Failed to parse price plan code. code:%s", $code));
            }
            if (!AppUtil::isInt($ar[0]) || !AppUtil::isInt($ar[1])) {
                throw new Exception(sprintf("Failed to parse price plan code. %s", AppUtil::jsonOneLine($ar)));
            }
            $res = ['group_id' => $ar[0], 'detail_no' => $ar[1]];
        } catch (Exception $e) {
            throw $e;
        }
        return $res;
    }

    /**
     * Check is allowed price plan as team campaign groups
     *
     * @param int    $teamId
     * @param string    $pricePlanCode
     * @param string $companyCountry
     *
     * @return bool
     */
    function isAllowedPricePlan(int $teamId, string $pricePlanCode, string $companyCountry): bool
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init("CampaignTeam");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        // Check price plan belonging team
        if (!$CampaignTeam->isTeamPricePlan($teamId, $pricePlanCode)) {
            return false;
        }

        // Check upper price plan max users
        $pricePlan = $this->getPlanByCode($pricePlanCode);
        if (empty($pricePlan)) {
            return false;
        }
        $chargeUserCount = $TeamMember->countHeadCount($teamId);
        if ($pricePlan['max_members'] < $chargeUserCount) {
            return false;
        }

        // Check currency
        $currency = $pricePlan['currency'];
        $requestedCountry = $PaymentService->getCurrencyTypeByCountry($companyCountry);
        if ($currency != $requestedCountry) {
            return false;
        }

        return true;
    }

    /**
     * get campaign for charging
     *
     * @param string $pricePlanCode
     *
     * @return array
     */
    function getChargeInfo(string $pricePlanCode): array
    {
        /** @var ViewCampaignPricePlan $ViewCampaignPricePlan */
        $ViewCampaignPricePlan = ClassRegistry::init('ViewCampaignPricePlan');
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        $campaign = $this->getPlanByCode($pricePlanCode);
        $subTotalCharge = $campaign['price'];
        $currencyType = $campaign['currency'];
        $tax = $currencyType == Enum\Model\PaymentSetting\Currency::JPY ? $PaymentService->calcTax('JP', $subTotalCharge) : 0;
        $totalCharge = $subTotalCharge + $tax;
        $chargeInfo = [
            'id'               => $campaign['id'],
            'sub_total_charge' => $subTotalCharge,
            'tax'              => $tax,
            'total_charge'     => $totalCharge,
            'member_count'     => $campaign['max_members'],
        ];

        return $chargeInfo;
    }

    /**
     * Get charge info for campaign team
     *
     * @param int $teamId
     *
     * @return array
     */
    function getTeamChargeInfo(int $teamId): array
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $purchasedPlan = $PricePlanPurchaseTeam->getByTeamId($teamId, ['price_plan_code']);
        if (empty($purchasedPlan)) {
            return null;
        }
        $pricePlanCode = $purchasedPlan['price_plan_code'];
        return CampaignService::getChargeInfo($pricePlanCode);
    }

    /**
     * Get Currency info from team price group
     *
     * @param string $pricePlanCode
     *
     * @return int|null
     */
    function getPricePlanCurrency(string $pricePlanCode)
    {
        $campaign = $this->getPlanByCode($pricePlanCode);
        if (empty($campaign)) {
            return null;
        }

        return $campaign['currency'];
    }

    /**
     * Save PricePlanPurchaseTeam to DB
     *
     * @param int $teamId
     * @param string $pricePlanCode
     *
     * @return array
     */
    function savePricePlanPurchase(int $teamId, string $pricePlanCode): array
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $pricePlanPurchase = [
            'team_id'           => $teamId,
            'price_plan_code'   => $pricePlanCode,
            'purchase_datetime' => time(),
        ];

        $PricePlanPurchaseTeam->create();
        return $PricePlanPurchaseTeam->save($pricePlanPurchase);
    }

    /**
     * Upgrade price plan
     *
     * @param int    $teamId
     * @param string $pricePlanCode
     * @param int    $opeUserId
     *
     * @return bool
     */
    function upgradePlan(int $teamId, string $pricePlanCode, int $opeUserId): bool
    {
        /** @var ViewCampaignPricePlan $ViewCampaignPricePlan */
        $ViewCampaignPricePlan = ClassRegistry::init('ViewCampaignPricePlan');
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');

        try {
            $this->TransactionManager->begin();

            $currentPlan = $this->getTeamPricePlan($teamId);
            if (empty($currentPlan)) {
                throw new Exception(sprintf("Current price plan doesn't exist. team_id:%s", $teamId));
            }

            $upgradePlan = $this->getPlanByCode($pricePlanCode);
            if (empty($upgradePlan)) {
                throw new Exception(sprintf("Upgrade price plan doesn't exist. plan_id:%s", $pricePlanCode));
            }

            // Delete current plan
            // The reason why recode doesn't update is to save history when the team purchased new plan .
            $PricePlanPurchaseTeam->softDeleteAll(['team_id' => $teamId], false);

            $pricePlanPurchase = [
                'team_id'           => $teamId,
                'price_plan_code'   => $pricePlanCode,
                'purchase_datetime' => time(),
            ];
            // Purchase new plan
            $PricePlanPurchaseTeam->create();
            if (!$PricePlanPurchaseTeam->save($pricePlanPurchase)) {
                throw new Exception(
                    sprintf("Failed to purchase new plan. %s", AppUtil::jsonOneLine(compact('pricePlanPurchase')
                    ))
                );
            }

            // Charge diff amount by upgrading plan
            $PaymentService->chargeForUpgradingCampaignPlan($teamId, $currentPlan['code'], $pricePlanCode,  $opeUserId);

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
     * Save CampaignChargeHistory to DB
     *
     * @param int $teamId
     * @param int $historyId
     * @param int $pricePlanPurchaseId
     *
     * @return array
     */
    function saveCampaignChargeHistory(int $teamId, int $historyId, int $pricePlanPurchaseId): array
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init('CampaignTeam');
        /** @var CampaignChargeHistory $CampaignChargeHistory */
        $CampaignChargeHistory = ClassRegistry::init('CampaignChargeHistory');

        $campaignTeam = $CampaignTeam->getByTeamId($teamId, ['id']);
        $campaignHistory = [
            'charge_history_id'           => $historyId,
            'campaign_team_id'            => $campaignTeam['id'],
            'price_plan_purchase_team_id' => $pricePlanPurchaseId,

        ];
        $CampaignChargeHistory->create();
        return $CampaignChargeHistory->save($campaignHistory);
    }
}
