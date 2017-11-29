<?php
App::uses('AppModel', 'Model');

/**
 * Class ViewCampaignPricePlan
 */
class ViewCampaignPricePlan extends AppModel
{
    public $useTable = 'view_price_plans';

    function findAllPlansByGroupId(int $groupId)
    {
        $options = [
            'fields' => [
                'id', 'code', 'max_members', 'price', 'currency', 'group_id'
            ],
            'conditions' => [
                'group_id' => $groupId
            ],
            'order' => 'max_members ASC'
        ];
        $res= $this->find('all', $options);
        if (empty($res)) {
            CakeLog::emergency("Price plans don't exist. group_id:".$groupId);
            return [];
        }
        return Hash::extract($res, '{n}.ViewCampaignPricePlan');

    }
}
