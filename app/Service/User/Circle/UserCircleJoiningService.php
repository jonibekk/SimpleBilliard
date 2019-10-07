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

        $circle_id = Hash::get($data, 'circle_id');
        $this->getCircleMemberEntity()->updateCounterCache(['circle_id' => $circle_id]);
        $this->getGlRedisEntity()->deleteMultiCircleMemberCount([$circle_id]);

        return $result;
    }

    /**
     * @param int $circle_id
     * @param int $user_id
     * @return bool
     */
    public function isJoined(int $circle_id, int $user_id): bool
    {
        return $this->getCircleMemberEntity()->isJoined($circle_id, $user_id);
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