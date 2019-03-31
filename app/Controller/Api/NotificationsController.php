<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('GlRedis', 'Model');

class NotificationsController extends BaseApiController
{
    public $components = [
        'NotifyBiz',
    ];

    /**
     * Read all notifications
     * @return ApiResponse|BaseApiResponse
     */
    public function put_read_all()
    {
        // FIXME: this processing is as same as Controller/NotificationsController.ajax_mark_all_read method
        // But this is not effective, should fix after notification flow renewed.
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');
        $notify_items = $GlRedis->getNotifyIds(
            $this->getTeamId(),
            $this->getUserId()
        );

        foreach ($notify_items as $notify_id => $val) {
             $GlRedis->changeReadStatusOfNotification(
                $this->getTeamId(),
                $this->getUserId(),
                $notify_id
            );
        }
        return ApiResponse::ok()->getResponse();
    }

    /**
     * Reset new notification count
     * @return ApiResponse|BaseApiResponse
     */
    public function put_reset_new_count()
    {
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');

        $GlRedis->deleteCountOfNewNotification(
            $this->getTeamId(),
            $this->getUserId()
        );

        return ApiResponse::ok()->getResponse();
    }
}
