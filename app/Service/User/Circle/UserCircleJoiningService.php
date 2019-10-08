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
     * @param array $data
     * @return array|mixed
     * @throws Exception
     */
    public function addMember(array $data)
    {
        $this->getCircleMemberEntity()->create();
        $result = $this->getCircleMemberEntity()->save($data);

        $circleId = Hash::get($data, 'circle_id');
        $this->getCircleMemberEntity()->updateCounterCache(['circle_id' => $circleId]);
        $this->getGlRedisEntity()->deleteMultiCircleMemberCount([$circleId]);

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
    protected function getGlRedisEntity(): GlRedis
    {
        return $this->GlRedis;
    }
}