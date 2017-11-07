<?php
App::uses('AppModel', 'Model');
/**
 * Class CampaignPricePlan
 */
class CampaignPricePlan extends AppModel
{
    public $useTable = 'mst_price_plans';
    /**
     * Get max member count of campaign price plan
     *
     * @param int $pricePlanId
     * @return void
     */
    function getMaxMemberCount(int $pricePlanId)
    {
        $res = $this->getById($pricePlanId, ['max_members']);
        if (!$res) {
            return null;
        }
        return $res['max_members'];
    }
}
