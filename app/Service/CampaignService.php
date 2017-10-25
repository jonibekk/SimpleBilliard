<?php
App::import('Service', 'AppService');
App::uses('CampaignPricePlan', 'Model');

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
     * Returns true if the team have purchased a campaign plan
     *
     * @param int $teamId
     *
     * @return bool
     */
    function purchased(int $teamId)
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

    function findList(int $teamId): array
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init("CampaignTeam");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        $res = [];
        $campaigns = $CampaignTeam->findPricePlans($teamId);
        foreach($campaigns as $campaign) {
            $currencyType = $campaign['CampaignPriceGroup']['currency'];
            $subTotalCharge = $campaign['CampaignPricePlan']['price'];
            $tax = $currencyType == Enum\PaymentSetting\Currency::JPY ? $PaymentService->calcTax('JP', $subTotalCharge) : 0;
            $totalCharge = $subTotalCharge + $tax;
            $res[] = [
                'id'               => $campaign['CampaignPricePlan']['id'],
                'sub_total_charge' => $PaymentService->formatCharge($subTotalCharge, $currencyType),
                'tax'              => $PaymentService->formatCharge($tax, $currencyType),
                'total_charge'     => $PaymentService->formatCharge($totalCharge, $currencyType),
                'member_count'     => $campaign['CampaignPricePlan']['max_members'],
            ];
        }
        return $res;
    }
}
