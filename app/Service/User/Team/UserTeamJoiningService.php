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
     * @param array $data
     * @return array|mixed
     * @throws Exception
     */
    public function addMember(array $data)
    {
        $this->getTeamMemberEntity()->create();
        return $this->getTeamMemberEntity()->save(array_merge($data, [
            'status'  => TeamMember::USER_STATUS_ACTIVE
        ]));
    }

    /**
     * @param int $user_id
     * @param int $team_id
     * @return bool
     */
    public function isJoined(int $user_id, int $team_id): bool
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $team_id
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