<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Service', 'CircleService');
App::import('Service', 'CircleMemberService');
App::import('Service/Paging', 'CirclePostPagingService');
App::import('Service/Paging', 'CircleMemberPagingService');
App::import('Service/Paging', 'PostDraftPagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('CircleMember', 'Model');
App::uses('Circle', 'Model');
App::import('Service', 'PostDraftService');
App::import('Service/Request/Resource', 'CircleResourceRequest');
App::import('Service/Redis', 'UnreadPostsRedisService');
App::import('Validator/Request/Api/V2', 'CircleRequestValidator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 9:41
 */

use Goalous\Exception as GlException;

class CirclesController extends BasePagingController
{
    public $components = [
        'NotifyBiz',
        'GlEmail',
    ];

    public function get_posts(int $circleId)
    {
        $error = $this->validateCircleAccess($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = ClassRegistry::init('CirclePostPagingService');

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        try {
            $data = $CirclePostPagingService->getDataWithPaging(
                $pagingRequest,
                $this->getPagingLimit(),
                $this->getExtensionOptions() ?: $this->getDefaultPostExtension());
        } catch (Exception $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    public function get_members(int $circleId)
    {
        $error = $this->validateCircleAccess($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CircleMemberPagingService $CircleMemberPagingService */
        $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        try {
            $data = $CircleMemberPagingService->getDataWithPaging(
                $pagingRequest,
                $this->getPagingLimit(10),
                $this->getExtensionOptions() ?: $this->getDefaultMemberExtension());
        } catch (Exception $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    public function post_joins(int $circleId)
    {
        $error = $this->validatePostJoins($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');
        try {
            $return = $CircleMemberService->add($this->getUserId(), $this->getTeamId(), $circleId);
            $this->notifyMember(NotifySetting::TYPE_CIRCLE_USER_JOIN, $circleId, $this->getUserId(),
                $this->getTeamId());
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (GlException\GoalousConflictException $exception) {
            return ErrorResponse::resourceConflict()->withException($exception)
                ->withMessage(__("You already joined to this circle."))->getResponse();
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        return ApiResponse::ok()->withData($return->toArray())->getResponse();
    }

    public function post_leaves(int $circleId)
    {
        $error = $this->validatePostLeaves($circleId);

        if (!empty($error)) {
            return $error;
        }

        try {
            /** @var CircleMemberService $CircleMemberService */
            $CircleMemberService = ClassRegistry::init('CircleMemberService');

            $CircleMemberService->delete($this->getUserId(), $this->getTeamId(), $circleId);
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        return ApiResponse::ok()->withData(['circle_id' => $circleId, 'user_id' => $this->getUserId()])->getResponse();
    }

    /**
     * Invite a member into a circle
     *
     * @param int $circleId
     *
     * @return BaseApiResponse
     */
    public function post_invite_members(int $circleId)
    {
        $error = $this->validatePostMembers($circleId);

        if (!empty($error)) {
            return $error;
        }
        $newMemberIds = Hash::extract($this->getRequestJsonBody(), 'user_id');

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        try {
            $return = $CircleMemberService->multipleAdd($newMemberIds, $this->getTeamId(), $circleId);
            if(!empty($return['newMemberIds'])){
                $this->notifyMembers(NotifySetting::TYPE_CIRCLE_ADD_USER,$circleId, $this->getUserId(),$this->getTeamId(),$return['newMemberIds']);
            }
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        return ApiResponse::ok()->withData($return)->getResponse();
    }

    /**
     * Endpoint for enabling notification setting for an user in a circle
     *
     * @param int $circleId
     *
     * @return BaseApiResponse
     */
    public function post_enable_notification(int $circleId)
    {
        $error = $this->validateCircleJoined($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        try {
            $CircleMemberService->setNotificationSetting($circleId, $this->getUserId(), true);
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $data = $CircleMember->getCircleMember($circleId, $this->getUserId());

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    /**
     * Endpoint for disabling notification setting for an user in a circle
     *
     * @param int $circleId
     *
     * @return BaseApiResponse
     */
    public function post_disable_notification(int $circleId)
    {
        $error = $this->validateCircleJoined($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        try {
            $CircleMemberService->setNotificationSetting($circleId, $this->getUserId(), false);
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $data = $CircleMember->getCircleMember($circleId, $this->getUserId());

        return ApiResponse::ok()->withBody($data)->getResponse();
    }


    /**
     * Endpoint for resetting current user's unread count for target circle
     *
     * @param int $circleId
     *
     * @return BaseApiResponse
     */
    public function put_reset_unread_count(int $circleId)
    {
        $error = $this->validateCircleJoined($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        try {
            $CircleMember->updateUnreadCount($circleId, 0, $this->getUserId(), $this->getTeamId());
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        return ApiResponse::ok()->getResponse();
    }

    public function put_reset_unread_posts(int $circleId)
    {
        /** @var UnreadPostsRedisService $UnreadPostsRedisService */
        $UnreadPostsRedisService = ClassRegistry::init('UnreadPostsRedisService');
        $UnreadPostsRedisService->removeManyByCircleId($this->getUserId(), $this->getTeamId(), $circleId);
        return ApiResponse::ok()->getResponse();
    }


    /**
     * Get list of post drafts in a circle
     *
     * @param int $circleId
     *
     * @return BaseApiResponse;
     */
    public function get_post_drafts(int $circleId)
    {
        $error = $this->validateCircleAccess($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var PostDraftService $PostDraftService */
        $PostDraftService = ClassRegistry::init('PostDraftService');

        try {
            $postDrafts = $PostDraftService->getPostDraftsFilterByCircleId(
                $this->getUserId(),
                $this->getTeamId(),
                $circleId
            );

            $postDrafts = array_map(function ($v) {
                $postDraft = $v->toArray();
                $draftData = json_decode($postDraft['draft_data'], true);
                $postDraft['body'] = $draftData['body'];
                unset($postDraft['draft_data']);

                return $postDraft;
            }, $postDrafts);

            /** @var PostDraftExtender $PostDraftExtender */
            $PostDraftExtender = ClassRegistry::init('PostDraftExtender');

            $postDrafts = $PostDraftExtender->extendMulti(
                $postDrafts,
                $this->getUserId(),
                $this->getTeamId(),
                [PostDraftExtender::EXTEND_ALL]
            );

        } catch (Exception $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withBody([
            'data'  => $postDrafts,
            'count' => count($postDrafts)
        ])->getResponse();
    }

    /**
     * Validation for endpoint get_posts. True if circle is in team and circle is public, or the user is joined to the
     * secret circle.
     *
     * @param int $circleId
     *
     * @return ErrorResponse | null
     */
    private function validateCircleAccess(int $circleId)
    {
        if (!is_int($circleId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        //Check if circle belongs to current team & user has access to the circle
        if (!$Circle->isBelongCurrentTeam($circleId, $this->getTeamId()) ||
            ($Circle->isSecret($circleId) && !$CircleMember->isBelong($circleId, $this->getUserId(),
                    $this->getTeamId()))) {
            return ErrorResponse::notFound()->withMessage(__("The circle doesn't exist or you don't have permission."))
                ->getResponse();
        }

        return null;
    }

    private function validateCircleJoined(int $circleId)
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        if (!$Circle->exists($circleId) || !$CircleMember->isBelong($circleId, $this->getUserId(),
                $this->getTeamId())) {
            return ErrorResponse::notFound()->withMessage(__("The circle doesn't exist or you don't have permission."))
                ->getResponse();
        }

        return null;

    }

    /**
     * Validate delete member endpoint
     *
     * @param int $circleId
     *
     * @return ErrorResponse | null
     */
    private function validatePostLeaves(int $circleId)
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        if (!$Circle->exists($circleId)) {
            return ErrorResponse::notFound()->withMessage(__("This circle does not exist."))->getResponse();
        }

        return null;
    }

    /**
     * Validate post_joins endpoint
     *
     * @param int $circleId
     *
     * @return ErrorResponse | null
     */
    public function validatePostJoins(int $circleId)
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        $condition = [
            'conditions' => [
                'Circle.id'         => $circleId,
                'Circle.public_flg' => true,
                'del_flg'           => false
            ]
        ];
        $circle = $Circle->find('first', $condition);

        if (!$Circle->isBelongCurrentTeam($circleId,
                $this->getTeamId()) || empty($circle) || $Circle->isSecret($circleId)) {
            return ErrorResponse::notFound()->withMessage(__("This circle does not exist."))->getResponse();
        }

        return null;
    }

    /**
     * Validate post_members endpoint.
     * Only admin is allowed to invite a member to secret circle
     *
     * @param int $circleId
     *
     * @return ErrorResponse | null
     */
    private function validatePostMembers(int $circleId)
    {
        try {
            CircleRequestValidator::createPostMembersValidator()->validate($this->getRequestJsonBody());
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                ->addErrorsFromValidationException($e)
                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error('Unexpected validation exception', [
                'class'   => get_class($e),
                'message' => $e,
            ]);
            return ErrorResponse::internalServerError()->getResponse();
        }

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $condition = [
            'conditions' => [
                'id'      => $circleId,
                'del_flg' => false
            ],
            'fields'     => [
                'id',
                'public_flg',
            ]
        ];

        $circle = $Circle->useType()->useEntity()->find('first', $condition);

        if (empty($circle)) {
            return ErrorResponse::notFound()->withMessage(__("This circle does not exist."))->getResponse();
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        if (!$CircleMember->isBelong($circleId, $this->getUserId(), $this->getTeamId())) {
            return ErrorResponse::forbidden()->withMessage(__("You can not invite."))->getResponse();
        }

        if (!$circle['properties']['public_flg']) {
            if (!$CircleMember->isAdmin($this->getUserId(), $circleId)) {
                return ErrorResponse::forbidden()->withMessage(__("You can not invite."))->getResponse();
            }
        }

        return null;
    }

    /**
     * Default extension options for getting circle post
     *
     * @return array
     */
    private function getDefaultPostExtension()
    {
        return [
            CirclePostExtender::EXTEND_CIRCLE,
            CirclePostExtender::EXTEND_LIKE,
            CirclePostExtender::EXTEND_SAVED,
            CirclePostExtender::EXTEND_READ,
            CirclePostExtender::EXTEND_USER,
            CirclePostExtender::EXTEND_COMMENTS,
            CirclePostExtender::EXTEND_RESOURCES,
            CirclePostExtender::EXTEND_TRANSLATION_LANGUAGE
        ];
    }

    /**
     * Default extension options for getting circle members
     *
     * @return array
     */
    private function getDefaultMemberExtension()
    {
        return [
            CircleMemberExtender::EXTEND_USER
        ];
    }

    /**
     * Get circle by Id
     *
     * @param int $circleId
     *
     * @return BaseApiResponse
     */
    public function get_detail(int $circleId)
    {
        $error = $this->validateCircleAccess($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init("CircleService");

        $circle = $CircleService->get(new CircleResourceRequest($circleId, $this->getUserId(), $this->getTeamId()));

        return ApiResponse::ok()->withData($circle)->getResponse();
    }


    /**
     * Send notification to all members in a circle
     *
     * @param int $notificationType
     * @param int $circleId
     * @param int $userId User who sent the notification
     * @param int $teamId
     */
    private function notifyMember(int $notificationType, int $circleId, int $userId, int $teamId)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $memberList = $CircleMember->getMemberList($circleId, true, false, [$userId]);

        // Notify to circle member
        $this->NotifyBiz->execSendNotify($notificationType, $circleId, null, $memberList, $teamId, $userId);
    }

    /**
     * Send notification to each members who invited
     *
     * @param int $notificationType
     * @param int $circleId
     * @param int userId
     * @param int teamId
     * @param int $memberIds User who got the notification
     */
    private function notifyMembers(int $notificationType, int $circleId, int $userId, int $teamId, array $memberIds)
    {
        // Notify to circle member
        $this->NotifyBiz->execSendNotify($notificationType, $circleId, null, $memberIds, $teamId, $userId);
    }

    private function getDefaultPostDraftExtension()
    {
        return [
            PostDraftExtender::EXTEND_USER
        ];
    }

}
