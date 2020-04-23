<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Service/Paging', 'NotificationPagingService');
App::import('Service/Paging', 'RecentCircleListPagingService');
App::import('Service/Paging', 'CirclePostUnreadPagingService');
App::import('Service/Paging', 'UnreadCircleListPagingService');
App::import('Service/Paging', 'FeedPostPagingService');
App::import('Service/Request/Resource', 'UserResourceRequest');
App::import('Service/Request/Resource', 'TeamResourceRequest');
App::import('Service', 'UnreadCirclePostService');
App::import('Service', 'UserService');
App::import('Service', 'AuthenticationSessionDataService');
App::import('Service', 'GoalService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('GlRedis', 'Model');
App::uses('TeamMember', 'Model');
App::uses('CircleMember', 'Model');
App::uses('CheckedCircle', 'Model');
App::import('Controller/Traits', 'AuthTrait');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsClient');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsKey');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/29
 * Time: 11:47
 * @property NotificationComponent $Notification
 * @property FlashComponent $Flash
 */
class MeController extends BasePagingController
{
    use AuthTrait;

    public $components = [
        'Flash',
        'Notification',
    ];

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

        $notifications = $NotificationPagingService->getDataWithPaging($pagingRequest, $this->getPagingLimit(),
            [NotificationExtender::EXTEND_ALL]);

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
     *
     * @ignoreRestriction
     */
    public function get_detail()
    {
        $req = new UserResourceRequest($this->getUserId(), $this->getTeamId(), true);

        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');

        try {
            $data = $UserService->get($req, [MeExtender::EXTEND_ALL]);
        } catch (Exception $e) {
            GoalousLog::error('Failed to get personal data', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $this->getUserId(),
                'team_id' => $this->getTeamId()
            ]);
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        $flashMessage = $this->Notification->readFlash();
        $data['flash'] = [];
        if (!empty($flashMessage)) {
            switch ($flashMessage['params']['type'] ?? 'error') {
                case 'success':
                    $data['flash']['success'] = $flashMessage['message'];
                    break;
                case 'error':
                    $data['flash']['error'] = $flashMessage['message'];
                    break;
            }
        }

        return ApiResponse::ok()->withBody([
            'data' => $data,
        ])->getResponse();
    }

    public function get_goal_status()
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        $canActionGoals = !empty($GoalService->findActionables($this->getUserId()));
        if ($canActionGoals) {
            return ApiResponse::ok()
                ->withBody([
                    'data' => [
                        'can_action' => true,
                    ]
                ])->getResponse();
        }

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        /** @var AuthenticationSessionDataService $AuthenticationSessionDataService */
        $AuthenticationSessionDataService = ClassRegistry::init("AuthenticationSessionDataService");
        $sessionData = $AuthenticationSessionDataService->read($this->getUserId(), $this->getTeamId(), $this->getJwtAuth()->getJwtId());
        if (empty($sessionData)) {
            $sessionData = new AccessTokenData();
        }

        $countCurrentTermGoalUnachieved = $Goal->countSearch([
            'term'     => 'present',
            'progress' => 'unachieved',
        ], $this->getTeamId());
        return ApiResponse::ok()
            ->withBody([
                'data' => [
                    'can_action' => $canActionGoals,
                    'show_goal_create_guidance' => !$sessionData->isHideGoalCreateGuidance(),
                    'count_current_term_goals' => $countCurrentTermGoalUnachieved
                ]
            ])->getResponse();
    }

    /**
     * @return ApiResponse|BaseApiResponse
     */
    public function put_hide_goal_create_guidance()
    {
        /** @var AuthenticationSessionDataService $AuthenticationSessionDataService */
        $AuthenticationSessionDataService = ClassRegistry::init("AuthenticationSessionDataService");
        $sessionData = $AuthenticationSessionDataService->read($this->getUserId(), $this->getTeamId(), $this->getJwtAuth()->getJwtId());
        $sessionData->withHideGoalCreateGuidance(true);
        $AuthenticationSessionDataService->write($this->getUserId(), $this->getTeamId(), $this->getJwtAuth()->getJwtId(), $sessionData);

        return ApiResponse::ok()->withBody([])->getResponse();
    }

    /**
     * Get unread posts summary for this user in this team
     */
    public function get_all_unread_posts()
    {
        /** @var UnreadCirclePostService $UnreadCirclePostService */
        $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');
        $data = $UnreadCirclePostService->getGrouped($this->getTeamId(), $this->getUserId());

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    public function get_recent_circles()
    {
        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var RecentCircleListPagingService $RecentCircleListPagingService */
        $RecentCircleListPagingService = ClassRegistry::init('RecentCircleListPagingService');

        $data = $RecentCircleListPagingService->getDataWithPaging(
            $pagingRequest,
            $this->getPagingLimit(15),
            [CircleExtender::EXTEND_MEMBER_INFO]);

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    public function get_unread_posts()
    {
        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var CirclePostUnreadPagingService $CirclePostUnreadPagingService */
        $CirclePostUnreadPagingService = ClassRegistry::init('CirclePostUnreadPagingService');
        $data = $CirclePostUnreadPagingService->getDataWithPaging($pagingRequest);

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    /**
     * Delete all unread posts summary for this user in this team
     */
    public function delete_all_unread_posts()
    {
        /** @var UnreadCirclePostService $UnreadCirclePostService */
        $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');

        $UnreadCirclePostService->deleteUserCacheInTeam($this->getTeamId(), $this->getUserId());

        return ApiResponse::ok()->getResponse();
    }

    /**
     * Get action & goal feed for homepage
     */
    public function get_feed()
    {
        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var FeedPostPagingService $FeedPostPagingService */
        $FeedPostPagingService = ClassRegistry::init('FeedPostPagingService');

        $data = $FeedPostPagingService->getDataWithPaging(
            $pagingRequest,
            $this->getPagingLimit(),
            [FeedPostExtender::EXTEND_ALL]);

        return ApiResponse::ok()->withBody($data)->getResponse();
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
     *
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
            return ErrorResponse::forbidden()->withMessage(__("You don't have access right to this team."))
                                ->getResponse();
        }

        try {
            $jwt = $this->resetAuth($userId, $teamId, $this->getJwtAuth());
        } catch (Exception $e) {
            GoalousLog::error('failed to switch team', [
                'message'        => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
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

    /**
     * Get checked circle ids
     *
     * @return ApiResponse|BaseApiResponse
     */
    public function get_checkedCircleIds()
    {
        $userId = $this->getUserId();
        $teamId = $this->getTeamId();

        /** @var CheckedCircle $CheckedCircle */
        $CheckedCircle = ClassRegistry::init('CheckedCircle');

        try {
            $CheckedCircleIds = $CheckedCircle->getCheckedCircleIds($userId, $teamId);
        }
        catch (Exception $e) {
            GoalousLog::error("Faild to get checked circle ids.", [
                'message'   => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
                'team_id'   => $teamId,
                'circle_id' => $circleId
            ]);
            throw $e;
        }

        return ApiResponse::ok()->withData($CheckedCircleIds)->getResponse();
    }
}
