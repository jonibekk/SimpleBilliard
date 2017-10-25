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
    }
}

