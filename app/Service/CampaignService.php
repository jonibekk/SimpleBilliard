<?php
App::import('Service', 'AppService');
App::uses('CampaignTeam', 'Model');
App::uses('CampaignPricePlan', 'Model');
App::uses('CampaignPriceGroup', 'Model');
App::uses('TeamMember', 'Model');
App::import('Service', 'PaymentService');

use Goalous\Model\Enum as Enum;

/**
 * Class CampaignService
 */
class CampaignService extends AppService
{
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
                'PricePlanPurchaseTeam.price_plan_id',
                'PricePlanPurchaseTeam.price_plan_code',
                'CampaignTeam.id',
                'CampaignTeam.campaign_type',
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
        /** @var CampaignPricePlan $CampaignPricePlan */
        $CampaignPricePlan = ClassRegistry::init('CampaignPricePlan');
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $purchasedPlan = $PricePlanPurchaseTeam->getByTeamId($teamId, ['price_plan_id']);
        if (empty($purchasedPlan)) {
            CakeLog::debug("PricePlanPurchaseTeam not found to team: $teamId");
            return 0;
        }

        $priceId = $purchasedPlan['price_plan_id'];
        $pricePlan = $CampaignPricePlan->getById($priceId, ['max_members']);
        if (empty($pricePlan)) {
            CakeLog::debug("CampaignPricePlan not found with id: $priceId");
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

        $currentUserCount = $TeamMember->countChargeTargetUsers($teamId);
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
        /** @var ViewCampaignPricePlan $ViewCampaignPricePlan */
        $ViewCampaignPricePlan = ClassRegistry::init('ViewCampaignPricePlan');
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $purchasedPlan = $PricePlanPurchaseTeam->getByTeamId($teamId, ['price_plan_id']);
        if (empty($purchasedPlan)) {
            CakeLog::debug("PricePlanPurchaseTeam not found to team: $teamId");
            return null;
        }

        $priceId = $purchasedPlan['price_plan_id'];
        $pricePlan = $ViewCampaignPricePlan->getById($priceId);
        if (empty($pricePlan)) {
            CakeLog::debug("CampaignPricePlan not found with id: $priceId");
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
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init("CampaignTeam");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        $res = [];
        $campaigns = $CampaignTeam->findPricePlans($teamId);
        foreach($campaigns as $campaign) {
            $currencyType = $campaign['currency'];
            $subTotalCharge = $campaign['price'];
            $tax = $currencyType == Enum\PaymentSetting\Currency::JPY ? $PaymentService->calcTax('JP', $subTotalCharge) : 0;
            $totalCharge = $subTotalCharge + $tax;
            $res[] = [
                'id'               => $campaign['id'],
                'sub_total_charge' => $PaymentService->formatCharge($subTotalCharge, $currencyType),
                'tax'              => $PaymentService->formatCharge($tax, $currencyType),
                'total_charge'     => $PaymentService->formatCharge($totalCharge, $currencyType),
                'member_count'     => $campaign['max_members'],
            ];
        }
        return $res;
    }

    /**
     * Check is allowed price plan as team campaign groups
     *
     * @param int    $teamId
     * @param int    $pricePlanId
     * @param string $companyCountry
     *
     * @return bool
     */
    function isAllowedPricePlan(int $teamId, int $pricePlanId, string $companyCountry): bool
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init("CampaignTeam");
        /** @var CampaignPricePlan $CampaignPricePlan */
        $CampaignPricePlan = ClassRegistry::init("CampaignPricePlan");
        /** @var CampaignPriceGroup $CampaignPriceGroup */
        $CampaignPriceGroup = ClassRegistry::init("CampaignPriceGroup");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        // Check price plan belonging team
        if (!$CampaignTeam->isTeamPricePlan($teamId, $pricePlanId)) {
            return false;
        }

        // Check upper price plan max users
        $pricePlan = $CampaignPricePlan->getById($pricePlanId);
        if (empty($pricePlan)) {
            return false;
        }
        $chargeUserCount = $TeamMember->countChargeTargetUsers($teamId);
        if ($pricePlan['max_members'] < $chargeUserCount) {
            return false;
        }

        // Check currency
        $currency = $CampaignPriceGroup->getCurrency($pricePlan['group_id']);
        $requestedCountry = $PaymentService->getCurrencyTypeByCountry($companyCountry);
        if ($currency != $requestedCountry) {
            return false;
        }

        return true;
    }

    /**
     * get campaign for charging
     *
     * @param int $pricePlanId
     *
     * @return array
     */
    function getChargeInfo(int $pricePlanId): array
    {
        /** @var ViewCampaignPricePlan $ViewCampaignPricePlan */
        $ViewCampaignPricePlan = ClassRegistry::init('ViewCampaignPricePlan');
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        $campaign = $ViewCampaignPricePlan->getById($pricePlanId);
        $subTotalCharge = $campaign['price'];
        $currencyType = $campaign['currency'];
        $tax = $currencyType == Enum\PaymentSetting\Currency::JPY ? $PaymentService->calcTax('JP', $subTotalCharge) : 0;
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

        $purchasedPlan = $PricePlanPurchaseTeam->getByTeamId($teamId, ['price_plan_id']);
        if (empty($purchasedPlan)) {
            CakeLog::debug("PricePlanPurchaseTeam not found to team: $teamId");
            return [
                'sub_total_charge' => 0,
                'tax'              => 0,
                'total_charge'     => 0,
                'member_count'     => 0,
            ];
        }
        $priceId = $purchasedPlan['price_plan_id'];
        return CampaignService::getChargeInfo($priceId);
    }

    /**
     * Get Currency info from team price group
     *
     * @param int $pricePlanId
     *
     * @return int|null
     */
    function getPricePlanCurrency(int $pricePlanId)
    {
        /** @var ViewCampaignPricePlan $ViewCampaignPricePlan */
        $ViewCampaignPricePlan = ClassRegistry::init('ViewCampaignPricePlan');
        $campaign = $ViewCampaignPricePlan->getById($pricePlanId, ['currency']);

        return $campaign['currency'];
    }

    /**
     * Save PricePlanPurchaseTeam to DB
     *
     * @param int $teamId
     * @param int $pricePlanId
     *
     * @return array
     */
    function savePricePlanPurchase(int $teamId, int $pricePlanId): array
    {
        /** @var CampaignPricePlan $CampaignPricePlan */
        $CampaignPricePlan = ClassRegistry::init('CampaignPricePlan');
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $pricePlan = $CampaignPricePlan->getById($pricePlanId, ['code']);
        $pricePlanPurchase = [
            'team_id'           => $teamId,
            'price_plan_id'     => $pricePlanId,
            'price_plan_code'   => $pricePlan['code'],
            'purchase_datetime' => time(),
        ];

        $PricePlanPurchaseTeam->create();
        return $PricePlanPurchaseTeam->save($pricePlanPurchase);
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
