<?php
App::uses('AppModel', 'Model');

/**
 * Class CampaignTeam
 *
 * Teams applicable to campaigns
 * キャンペーン適用チーム
 */
class CampaignTeam extends AppModel
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
        // TODO: Delete
        return true;

        $campaignTeam = $this->getByTeamId($teamId, ['id']);
        if (!empty($campaignTeam)) {
            return true;
        }
        return false;
    }

    function findPricePlans(int $teamId): array
    {
        $options = [
            'conditions' => [
                'CampaignTeam.team_id' => $teamId,
                'CampaignTeam.del_flg' => false,
            ],
            'fields'     => [
                'CampaignPricePlan.id',
                'CampaignPricePlan.price',
                'CampaignPricePlan.max_members',
                'CampaignPriceGroup.currency',
            ],
            'order'      => [
                'CampaignPricePlan.price DESC'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'mst_price_plan_groups',
                    'alias'      => 'CampaignPriceGroup',
                    'conditions' => [
                        'CampaignPriceGroup.id = CampaignTeam.price_plan_group_id',
                        'CampaignPriceGroup.del_flg' => false,
                    ],
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'mst_price_plans',
                    'alias'      => 'CampaignPricePlan',
                    'conditions' => [
                        'CampaignPricePlan.group_id = CampaignPriceGroup.id',
                        'CampaignPricePlan.del_flg' => false
                    ],
                ],
            ]
        ];
        return $this->find('all', $options);

        // return [
        //     [
        //         'id' => 5,
        //         'sub_total_charge' => '¥250,000',
        //         'tax' => '¥20000',
        //         'total_charge' => '¥270,000',
        //         'member_count' => 500,
        //         'can_select' => true
        //     ],
        //     [
        //         'id' => 4,
        //         'sub_total_charge' => '¥200,000',
        //         'tax' => '¥16,000',
        //         'total_charge' => '¥216,000',
        //         'member_count' => 400,
        //         'can_select' => true
        //     ],
        //     [
        //         'id' => 3,
        //         'sub_total_charge' => '¥150,000',
        //         'tax' => '¥12,000',
        //         'total_charge' => '¥162,000',
        //         'member_count' => 300,
        //         'can_select' => true
        //     ],
        //     [
        //         'id' => 2,
        //         'sub_total_charge' => '¥100,000',
        //         'tax' => '¥8000',
        //         'total_charge' => '¥108,000',
        //         'member_count' => 200,
        //         'can_select' => true
        //     ],
        //     [
        //         'id' => 1,
        //         'sub_total_charge' => '¥50,000',
        //         'tax' => '¥4000',
        //         'total_charge' => '¥54,000',
        //         'member_count' => 50,
        //         'can_select' => false
        //     ],
        // ];
    }
}

