<?php
App::uses('AppModel', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * CampaignTeam Model
 */
class CampaignTeam extends AppModel
{
    /**
     * is campaign team
     * TODO: Implement. This is mock.
     *
     * @param int $teamId
     *
     * @return bool
     */
    public function isCampaignTeam(int $teamId): bool
    {
        return true;
    }
}
