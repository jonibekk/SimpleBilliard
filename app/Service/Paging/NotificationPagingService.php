<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Lib/DataExtender', 'NotificationExtender');
App::uses('GlRedis', 'Model');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 2019/01/29
 * Time: 16:23
 */
class NotificationPagingService extends BasePagingService
{
    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');
        $conditions = $pagingRequest->getConditions();
        $fromTsPointer = $pagingRequest->getPointer('from_timestamp');
        $fromTimestamp = !empty($fromTsPointer[2]) ? $fromTsPointer[2] :
            $conditions['from_timestamp'];

        $notifications = $GlRedis->getNotifications(
            $pagingRequest->getCurrentTeamId(),
            $pagingRequest->getCurrentUserId(),
            $limit, $fromTimestamp
        );
        if (empty($notifications)) {
            return $notifications;
        }

        $notifications = $this->processResponse($notifications);
        return $notifications;
    }

    private function processResponse(array $notifications)
    {
        foreach($notifications as &$noti) {
            $noti['type'] = (int)$noti['type'];
            $noti['to_user_count'] = (int)$noti['to_user_count'];
            $noti['created'] = (int)floor($noti['created']);
            $body = json_decode($noti['body'], true);
            $noti['body'] = is_array($body) ? reset($body): $body;
            $noti['is_read'] = !$noti['unread_flg'];
            $noti['options'] = json_decode($noti['options'], true) ?? [];
            $noti['old_gl'] = !isset($noti['old_gl']) || $noti['old_gl'] ;
            unset($noti['unread_flg']);
        }

        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var NotifySetting $NotifySetting */
        $NotifySetting = ClassRegistry::init('NotifySetting');

        //fetch User
        $userIds = Hash::extract($notifications, '{n}.user_id');
        $userIds = array_merge($userIds, Hash::extract($notifications, '{n}.options.post_user_id'));
        $users = Hash::combine($User->getUsersProf($userIds), '{n}.User.id', '{n}');

        foreach($notifications as &$noti) {
            $userId = null;
            $userName = null;

            if (isset($users[$noti['user_id']])) {
                $userId = $noti['user_id'];
                $user = $users[$userId]['User'];
                $userName = $user['display_username'];
            }
            //get title
            $title = $NotifySetting->getTitle($noti['type'],
                $userName, 1,
                $noti['body'],
                array_merge($noti['options'],
                    ['from_user_id' => $userId]));
            $noti['html_title'] = $title;
        }

        return $notifications;
    }

    protected function countData(PagingRequest $request): int
    {
        return 0;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree
    {
        return new PointerTree(['from_timestamp', "<", $lastElement['score']]);
    }

    protected function beforeRead(PagingRequest $pagingRequest)
    {
        $pagingRequest->addQueriesToCondition(['from_timestamp']);
        return $pagingRequest;
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var NotificationExtender $NotificationExtender */
        $NotificationExtender = ClassRegistry::init('NotificationExtender');
        $data = $NotificationExtender->extendMulti($data, $userId, $teamId, $options);
    }


    /**
     * Attach additional values to PagingRequest before usage
     *
     * @param PagingRequest $pagingRequest
     *
     * @return PagingRequest
     */
    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        return $pagingRequest;
    }
}
