<?php
App::import('Service', 'AppService');
App::import('Service', 'UserService');
App::uses('Circle', 'Model');
App::uses('CirclePin', 'Model');
App::uses('CircleMember', 'Model');
App::uses('AppUtil', 'Util');

/**
 * Class CircleMemberService
 */
class CirclePinService extends AppService
{
    /**
     * 自分が所属しているサークルを抜けるときに更新処理を行うメソッド
     * @return bool
     */
    public function deleteCircleOrder(int $userId, int $teamId, int $circleId): bool
    {
        try{
            ClassRegistry::init('CirclePin')->deleteId($userId, $teamId, $orders);
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        return true;
    }

    /**
     * 自分が所属しているサークルのソート順をDBへセットする
     * @return bool
     */
    public function setCircleOrders(int $userId, int $teamId, string $orders): bool
    {
        try{
            ClassRegistry::init('CirclePin')->insertUpdate($userId, $teamId, $orders);
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        return true;
    }
}
