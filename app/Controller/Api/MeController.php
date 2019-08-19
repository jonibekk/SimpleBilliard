<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Service/Paging', 'NotificationPagingService');
App::import('Service/Request/Resource', 'UserResourceRequest');
App::import('Service/Request/Resource', 'TeamResourceRequest');
App::import('Service', 'UserService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('GlRedis', 'Model');
App::uses('TeamMember', 'Model');
App::uses('CircleMember', 'Model');
App::import('Controller/Traits', 'AuthTrait');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsClient');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsKey');
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/29
 * Time: 11:47
 */
class MeController extends BasePagingController
{
    use AuthTrait;

    /**
     * Get list of circles that an user is joined in
     *
     * @param int $userId
     *
     * @return CakeResponse
     */
    public function get_circles()
    {

        $res = $this->validateCircles();

        if (!empty($res)) {
            return $res;
        }
        try {
            $pagingRequest = $this->getPagingParameters();
            $pagingRequest->addCondition(['public_only' => false]);
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        // TODO: stop to get all pinned circles
        // Related issue: GL-7944
        // In fact, infinite loading pinned circles is not working well, I guess other infinite loading latest update circles below it is a cause,
        // that's not better, but we prioritize fixing other critical bugs
        $circleData = $CircleListPagingService->getDataWithPaging(
            $pagingRequest,
            $this->getPagingLimit(1000), // To get all
            $this->getExtensionOptions() ?: $this->getDefaultCircleExtension());

        return ApiResponse::ok()->withBody($circleData)->getResponse();
    }

    /**
     * Get list of notifications for user authorized
     */
    public function get_notifications()
    {
        $error = $this->validateNotifications();

        if (!empty($error)) {
            return $error;
        }

        try {
            $pagingRequest = $this->getPagingParameters();
            $pagingRequest->addCondition(['from_timestamp' => 0]);
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var NotificationPagingService $NotificationPagingService */
        $NotificationPagingService = ClassRegistry::init('NotificationPagingService');

        $notifications = $NotificationPagingService->getDataWithPaging($pagingRequest, $this->getPagingLimit(), [NotificationExtender::EXTEND_ALL]);

        return ApiResponse::ok()
            ->withBody($notifications)->getResponse();
    }

    /**
     * Get unread notifications count
     */
    public function get_new_notification_count()
    {
        $error = $this->validateNotifications();

        if (!empty($error)) {
            return $error;
        }

        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');
        // unread_count doesn't deal as `count` return value of paging service because it is paging `total` count
        $unreadCount = $GlRedis->getCountOfNewNotification($this->getTeamId(), $this->getUserId());

        $data = ['new_notification_count' => $unreadCount];

        return ApiResponse::ok()
            ->withBody(compact('data'))->getResponse();
    }

    /**
     * Get user detail
     */
    public function get_detail()
    {
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');
        $req = new UserResourceRequest($this->getUserId(), $this->getTeamId(), true);
        $data = $UserService->get($req, [MeExtender::EXTEND_ALL]);

        return ApiResponse::ok()
            ->withBody(compact('data'))->getResponse();
    }

    /**
     * Get unread posts summary for this user in this team
     */
    public function get_all_unread_posts()
    {
        $unreadPostsKey = new UnreadPostsKey($this->getUserId(), $this->getTeamId());
        $unreadPostsClient = new UnreadPostsClient();

        $data = $unreadPostsClient->read($unreadPostsKey)->get(true);

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    /**
     * Delete all unread posts summary for this user in this team
     */
    public function delete_all_unread_posts()
    {
        $UnreadPostsKey = new UnreadPostsKey($this->getUserId(), $this->getTeamId());
        $UnreadPostsClient = new UnreadPostsClient();

        $UnreadPostsClient->del($UnreadPostsKey);

        return ApiResponse::ok()->getResponse();
    }

    /**
     * Validate parameters for getting notifications
     *
     * @return ErrorResponse | null
     */
    private function validateNotifications()
    {
        $fromTimestamp = $this->request->query('from_timestamp');

        if (!empty($fromTimestamp) && !AppUtil::isInt($fromTimestamp)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        return null;
    }

    /**
     * Parameter validation for circles()
     *
     * @return CakeResponse | null
     */
    private function validateCircles()
    {
        return null;
    }

    /**
     * Default extension option for getting circle list
     *
     * @return array
     */
    private function getDefaultCircleExtension()
    {
        return [
            CircleExtender::EXTEND_ALL
        ];
    }

    /**
     * Switch team
     * @ignoreRestriction
     * @return ApiResponse|BaseApiResponse
     */
    public function put_switch_team()
    {
        $teamId = Hash::get($this->getRequestJsonBody(), 'team_id');
        $userId = $this->getUserId();
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        // Check permission whether access team
        $myTeams = $TeamMember->getActiveTeamList($userId);
        if (!array_key_exists($teamId, $myTeams)) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have access right to this team."))->getResponse();
        }

        try {
            $jwt = $this->resetAuth($userId, $teamId, $this->getJwtAuth());
        } catch (Exception $e) {
            GoalousLog::error('failed to switch team', [
                'user_id'        => $userId,
                'switch_team_id' => $teamId,
            ]);
            return ErrorResponse::internalServerError()
                ->getResponse();
        }

        $data = [
            'token' => $jwt->token(),
        ];

        return ApiResponse::ok()->withData($data)->getResponse();
    }
}
