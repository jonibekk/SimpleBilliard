<?php
App::uses('AppModel', 'Model');
App::uses('CampaignPricePlan', 'Model');

/**
 * Class PricePlanPurchaseTeam
 *
 * Teams that purchased the campaigns plan
 * 料金プランを購入したチーム
 */
class PricePlanPurchaseTeam extends AppModel
{
    public $useTable = 'price_plan_purchase_teams';

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

        $purchasedPlan = $this->getByTeamId($teamId, ['price_plan_id']);
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
     * Returns true if the team have purchased a campaign plan
     *
     * @param int $teamId
     *
     * @return bool
     */
    function isCampaignTeam(int $teamId): bool
    {
        $purchasedPlan = $this->getByTeamId($teamId, ['id']);
        if (!empty($purchasedPlan)) {
            return true;
        }
        return false;
    }
}
