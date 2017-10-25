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

    /**
     * get price plan with currency
     *
     * @param int $pricePlanId
     */
    function getWithCurrency(int $pricePlanId)
    {
        $options = [
            'conditions' => [
                'CampaignPricePlan.id' => $pricePlanId,
                'CampaignPricePlan.del_flg' => false,
            ],
            'fields'     => [
                'CampaignPricePlan.id',
                'CampaignPricePlan.price',
                'CampaignPricePlan.max_members',
                'CampaignPriceGroup.currency',
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'mst_price_plan_groups',
                    'alias'      => 'CampaignPriceGroup',
                    'conditions' => [
                        'CampaignPriceGroup.id = CampaignPricePlan.group_id',
                        'CampaignPriceGroup.del_flg' => false,
                    ],
                ],
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }
}
