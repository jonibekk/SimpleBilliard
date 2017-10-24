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
}

