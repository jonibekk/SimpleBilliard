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
        $campaignTeam = $this->getByTeamId($teamId, ['id']);
        if (!empty($campaignTeam)) {
            return true;
        }
        return false;
    }

    /**
     * find price plans belongs team campaign group
     *
     * @param int $teamId
     * @return array
     */
    function findPricePlans(int $teamId): array
    {
        $options = [
            'fields'     => [
                'ViewCampaignPricePlan.id',
                'ViewCampaignPricePlan.code',
                'ViewCampaignPricePlan.price',
                'ViewCampaignPricePlan.max_members',
                'ViewCampaignPricePlan.currency',
            ],
            'order'      => [
                'ViewCampaignPricePlan.max_members ASC'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'view_price_plans',
                    'alias'      => 'ViewCampaignPricePlan',
                    'conditions' => [
                        'ViewCampaignPricePlan.group_id = CampaignTeam.price_plan_group_id',
                        'CampaignTeam.team_id' => $teamId,
                        'CampaignTeam.del_flg' => false,
                    ],
                ],
            ]
        ];
        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::extract($res, '{n}.ViewCampaignPricePlan');
    }

    /**
     * Check is allowed price plan as team campaign groups
     *
     * @param int $teamId
     * @param int $pricePlanId
     * @return bool
     */
    function isTeamPricePlan(int $teamId, int $pricePlanId): bool
    {
        $options = [
            'fields'     => [
                'ViewCampaignPricePlan.id'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'view_price_plans',
                    'alias'      => 'ViewCampaignPricePlan',
                    'conditions' => [
                        'ViewCampaignPricePlan.group_id = CampaignTeam.price_plan_group_id',
                        'CampaignTeam.team_id' => $teamId,
                        'CampaignTeam.del_flg' => false,
                        'ViewCampaignPricePlan.id' => $pricePlanId
                    ],
                ],
            ]
        ];
        return (bool)$this->find('first', $options);
    }

}

