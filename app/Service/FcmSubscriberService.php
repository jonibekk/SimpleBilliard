<?php
App::import('Service', 'AppService');
App::uses('User', 'Model');
App::uses("FcmSubscriber", 'Model');

use Goalous\Enum as Enum;
/**
 * Class UserService
 */
class FcmSubscriberService extends AppService
{
    /**
     * get users' fcm subscriber
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function add($user_id, $subscriber, $version = -1, $browser_type = 99): array
    {
        /** @var User $User */
        $FcmSubscirber = ClassRegistry::init('FcmSubscriber');
        return $FcmSubscirber->getSubscriberByUserId($user_id, $subscriber, $version, $browser_type);
    }

    /**
     * get users' fcm subscriber
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function get($user_id): array
    {
        /** @var User $User */
        $FcmSubscirber = ClassRegistry::init('FcmSubscriber');
        return $FcmSubscirber->getSubscriberByUserId($user_id);
    }

    /**
     * delete users' fcm subscriber
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function delete($user_id): array
    {
        /** @var User $User */
        $FcmSubscirber = ClassRegistry::init('FcmSubscriber');
        return $FcmSubscirber->deleteSubscirberByUserId($user_id);
    }
}
