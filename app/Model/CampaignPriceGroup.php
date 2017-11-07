<?php
App::uses('AppModel', 'Model');

/**
 * Class CampaignPriceGroup
 */
class CampaignPriceGroup extends AppModel
{
    public $useTable = 'mst_price_plan_groups';

    function getCurrency(int $groupId)
    {
        $res = $this->getById($groupId);
        if (!$res) {
            return null;
        }
        return $res['currency'];
    }
}
