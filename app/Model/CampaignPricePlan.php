<?php
App::uses('AppModel', 'Model');

/**
 * Class CampaignPricePlan
 */
class CampaignPricePlan extends AppModel
{
    public $useTable = 'mst_price_plans';

    /**
     * Return CampaignPricePlan containing
     * currency information from CampaignPriceGroup
     *
     * @param int $planId
     *
     * @return array|null
     */
    public function getWithCurrencyInfo(int $planId)
    {
        $options = [
            'fields'     => [
                'CampaignPricePlan.id',
                'CampaignPricePlan.group_id',
                'CampaignPricePlan.code',
                'CampaignPricePlan.price',
                'CampaignPricePlan.max_members',
                'CampaignPriceGroup.currency',
            ],
            'conditions' => [
                'CampaignPricePlan.id' => $planId,
                'CampaignPricePlan.del_flg'  => false
            ],
            'joins'      => [
                [
                    'table'      => 'mst_price_plan_groups',
                    'alias'      => 'CampaignPriceGroup',
                    'type'       => 'INNER',
                    'conditions' => [
                        'CampaignPricePlan.group_id = CampaignPriceGroup.id',
                        'CampaignPriceGroup.del_flg' => false
                    ]
                ]
            ],
        ];
        $res = $this->find('first', $options);
        $res = am($res['CampaignPricePlan'], $res['CampaignPriceGroup']);
        return $res;
    }

}
