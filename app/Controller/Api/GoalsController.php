<?php
App::import('Service', 'CommentService');
App::import('Service', 'PostService');
App::import('Service', 'PostLikeService');
App::import('Service', 'PostReadService');
App::import('Service', 'SavedPostService');
App::import('Service', 'PostDraftService');
App::import('Service', 'PusherService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service/Paging', 'CommentPagingService');
App::import('Service/Paging', 'PostLikesPagingService');
App::import('Service/Paging', 'PostReaderPagingService');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');
App::uses('BasePagingController', 'Controller/Api');
App::uses('PostShareCircle', 'Model');
App::uses('PostRequestValidator', 'Validator/Request/Api/V2');
App::uses('TeamMember', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::import('Lib/DataExtender', 'CommentExtender');
App::import('Lib/DataExtender', 'PostExtender');
App::import('Lib/Pusher', 'NewCommentNotifiable');
App::import('Service/Pusher', 'PostPusherService');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');
App::import('Service', 'GoalService');
App::import('Service', 'FollowService');

use Goalous\Exception as GlException;

class GoalsController extends BasePagingController
{
    use TranslationNotificationTrait;

    public $components = [
        'NotifyBiz',
        'GlEmail',
        'Mention'
    ];

    public function get_collab_details(int $goalId)
    {
        /** @var GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $approvalData = $GoalApprovalService->genRequestApprovalData(
            $this->getUserId(),
            $this->getTeamId(),
            $goalId
        );
        $approvalData['goal_id'] = $goalId;

        return ApiResponse::ok()->withData($approvalData)->getResponse();
    }

    public function post_follow(int $goalId)
    {
        /** @var FollowService $FollowService */
        $FollowService = ClassRegistry::init("FollowService");
        try {
            $FollowService->validateToFollow(
                $this->getTeamId(),
                $goalId,
                $this->getUserId()
            );
        } catch (GlException\Follow\ValidationToFollowException $e) {
            return ErrorResponse::badRequest()
                ->withMessage(__("Failed to follow"))
                ->getResponse();
        }

        // フォロー
        $newId = $FollowService->add($goalId, $this->getUserId(), $this->getTeamId());
        if (!$newId) {
            return ErrorResponse::internalServerError()
                ->withMessage(__("Failed to follow"))
                ->getResponse();
        }

        // トラッキング
        // Commented out, Mixpanel component depends on global user variable
        // $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_FOLLOW_GOAL, $goalId);

        // 通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_FOLLOW, $goalId);

        return ApiResponse::ok()->withData([])->getResponse();
    }

    public function delete_follow(int $goalId)
    {
        /** @var FollowService $FollowService */
        $FollowService = ClassRegistry::init("FollowService");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        // ゴール存在チェック
        if (!$Goal->isBelongCurrentTeam($goalId, $this->Session->read('current_team_id'))) {
            return ErrorResponse::badRequest()->getResponse();
        }
        // 解除対象のフォロー存在チェック
        $following = $FollowService->getUnique($goalId, $this->getUserId());
        if (empty($following)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        // フォロー解除
        if (!$FollowService->delete($goalId, $this->getUserId())) {
            return ErrorResponse::internalServerError()->getResponse();
        }

        // トラッキング
        // Commented out, Mixpanel component depends on global user variable
        // $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_FOLLOW_GOAL, $goalId);

        return ApiResponse::ok()->withData([])->getResponse();
    }

    public function post_collaborate(int $goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $body = $this->getRequestJsonBody();
        $request = new CollaborateStartRequest(
            $this->getTeamId(),
            $goalId,
            $this->getUserId(),
            \Goalous\Enum\Model\GoalMember\Type::COLLABORATOR(),
            Hash::get($body, 'role'),
            Hash::get($body, 'description'),
            Hash::get($body, 'priority')
        );
        $isWishApproval = Hash::get($body, 'requestGoalApproval');
        $request->setIsWishApproval($isWishApproval);
        $request->setNotifyBiz($this->NotifyBiz);

        try {
            $GoalService->collaborateStart($request);
        } catch (\Throwable $exception) {
            GoalousLog::info('Failed to collaborate', [
                'message' => $exception->getMessage(),
                'goals.id' => $goalId,
                'users.id' => $this->getUserId()
            ]);
            return ErrorResponse::internalServerError()
                ->withMessage(__("Failed to collaborate"))
                ->getResponse();
        }

        return ApiResponse::ok()->withData([])->getResponse();
    }
}
