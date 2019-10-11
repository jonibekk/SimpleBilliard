<?php

App::uses('CircleMember', 'Model');
App::uses('GlRedis', 'Model');

class UserCircleJoiningService
{
    /** @var CircleMember */
    private $CircleMember;
    /** @var GlRedis */
    private $GlRedis;

    public function __construct()
    {
        $this->CircleMember = ClassRegistry::init('CircleMember');
        $this->GlRedis = ClassRegistry::init('GlRedis');
    }

    /**
     * @param int $userId
     * @param int $teamId
     * @param int $circleId
     * @return array|mixed
     * @throws Exception
     */
    public function addMember(int $userId, int $teamId, int $circleId)
    {
        $this->getCircleMemberEntity()->create();
        $result = $this->getCircleMemberEntity()->save([
            'circle_id' => $circleId,
            'team_id' => $teamId,
            'user_id' => $userId
        ]);

        $this->getCircleMemberEntity()->updateCounterCache(['circle_id' => $circleId]);
        $this->getGlRedis()->deleteMultiCircleMemberCount([$circleId]);

        return $result;
    }

    /**
     * @param int $circleId
     * @param int $userId
     * @return bool
     */
    public function isJoined(int $circleId, int $userId): bool
    {
        return $this->getCircleMemberEntity()->isJoined($circleId, $userId);
    }

    /**
     * @return CircleMember
     */
    protected function getCircleMemberEntity(): CircleMember
    {
        return $this->CircleMember;
    }

    /**
     * @return GlRedis
     */
    protected function getGlRedis(): GlRedis
    {
        return $this->GlRedis;
    }
}