<?php
App::uses('AppModel', 'Model');

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
     * Returns true if the team have purchased a campaign plan
     *
     * @param int $teamId
     *
     * @return bool
     */
    function purchased(int $teamId): bool
    {
        $purchasedPlan = $this->getByTeamId($teamId, ['id']);
        if (!empty($purchasedPlan)) {
            return true;
        }
        return false;
    }
}
