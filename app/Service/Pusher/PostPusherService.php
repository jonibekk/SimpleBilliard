<?php
App::import('Lib/Pusher', 'NewPostNotifiable');
App::import('Lib/Pusher', 'NavCircleBadgeNotifiable');
App::import('Service/Pusher', 'BasePusherService');
App::uses('CircleMember', 'Model');

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/03/20
 * Time: 10:45
 */
class PostPusherService extends BasePusherService
{
    public function sendFeedNotification(int $circleId, PostEntity $newPost)
    {
        /** @var PusherService $PusherService */
        $PusherService = ClassRegistry::init("PusherService");
        /** @var NewPostNotifiable $NewPostNotifiable */
        $NewPostNotifiable = ClassRegistry::init("NewPostNotifiable");
        $NewPostNotifiable->build($newPost, $circleId);
        $PusherService->notify($this->socketId, $NewPostNotifiable);
    }

    public function sendCircleBadgeNotification(int $circleId, int $userId, PostEntity $newPost)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        if ($CircleMember->getNotificationFlg($circleId, $userId)) {
            /** @var PusherService $PusherService */
            $PusherService = ClassRegistry::init("PusherService");
            /** @var NavCircleBadgeNotifiable $NavCircleBadgeNotifiable */
            $NavCircleBadgeNotifiable = ClassRegistry::init("NavCircleBadgeNotifiable");
            $NavCircleBadgeNotifiable->build($newPost, $circleId);
            $PusherService->notify($this->socketId, $NavCircleBadgeNotifiable);
        }
    }

}