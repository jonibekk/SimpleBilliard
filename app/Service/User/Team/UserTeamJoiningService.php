<?php

App::uses('TeamMember', 'Model');

class UserTeamJoiningService
{
    /** @var TeamMember */
    private $TeamMember;

    public function __construct()
    {
        $this->TeamMember = ClassRegistry::init('TeamMember');
    }

    /**
     * @param int $userId
     * @param int $teamId
     * @param bool $adminFlg
     * @return array|mixed
     * @throws Exception
     */
    public function addMember(int $userId, int $teamId, bool $adminFlg)
    {
        $this->getTeamMemberEntity()->create();
        return $this->getTeamMemberEntity()->save([
            'user_id' => $userId,
            'team_id' => $teamId,
            'admin_flg' => $adminFlg,
            'status'  => TeamMember::USER_STATUS_ACTIVE
        ]);
    }

    /**
     * @param int $userId
     * @param int $teamId
     * @return bool
     */
    public function isJoined(int $userId, int $teamId): bool
    {
        $options = [
            'conditions' => [
                'user_id' => $userId,
                'team_id' => $teamId
            ]
        ];

        return !!$this->getTeamMemberEntity()->find('first', $options);
    }

    /**
     * @return TeamMember
     */
    protected function getTeamMemberEntity(): TeamMember
    {
        return $this->TeamMember;
    }
}