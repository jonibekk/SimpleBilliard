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

use Goalous\Exception as GlException;

class PostsController extends BasePagingController
{
    use TranslationNotificationTrait;

    public $components = [
        'NotifyBiz',
        'GlEmail',
        'Mention'
    ];

    /**
     * Endpoint for saving both circle posts and action posts
     *
     * @return CakeResponse
     */
    public function post()
    {
        $error = $this->validatePost();

        if (!empty($error)) {
            return $error;
        }

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
        /** @var VideoStreamService $VideoStreamService */
        $VideoStreamService = ClassRegistry::init("VideoStreamService");

        $requestData = $this->getRequestJsonBody();
        $post['body'] = Hash::get($requestData, 'body');
        $post['type'] = (int)Hash::get($requestData, 'type');
        $post['site_info'] = Hash::get($requestData, 'site_info');

        $circleId = (int)Hash::get($requestData, 'circle_id');
        $files = Hash::get($requestData, 'resources', []);

        try {
            // Checking if needs to create a draft post
            $videoStreamIds = [];
            foreach ($files as $file) {
                if (isset($file['is_video']) && $file['is_video']) {
                    $videoStreamIds[] = $file['video_stream_id'];
                }
            }
            if (1 < count($videoStreamIds)) {
                return ErrorResponse::badRequest()->withMessage(__('You can only post one video file.'))->getResponse();
            }
            if (1 === count($videoStreamIds)) {
                if (!$VideoStreamService->isAllCompletedTrancode($videoStreamIds)) {
                    // Transcode not completed, creating draft post
                    $postDraft = $this->createDraftPost($post, $circleId, $this->getUserId(), $this->getTeamId(), $files);
                    $draftData = json_decode($postDraft['draft_data'], true);
                    $data = am($postDraft, [
                        'is_draft' => true,
                        'body'     => $draftData['body'],
                    ]);
                    unset($data['draft_data']);
                    return ApiResponse::ok()->withData($data)->getResponse();
                }
            }

            $res = $PostService->addCirclePost($post, $circleId, $this->getUserId(), $this->getTeamId(), $files);
            $mentionedUserIds = $this->Mention->getUserList($post['body'], $this->getTeamId(), $this->getUserId());

            $this->notifyNewPost($res, $circleId, $mentionedUserIds);

            /** @var TeamTranslationLanguage $TeamTranslationLanguage */
            $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
            if ($TeamTranslationLanguage->hasLanguage($this->getTeamId())) {
                $this->sendTranslationUsageNotification($this->getTeamId());
            }

        } catch (InvalidArgumentException $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->withMessage(__("Failed to post."))
                ->getResponse();
        }
        return ApiResponse::ok()->withData($res->toArray())->getResponse();
    }

    private function createDraftPost(array $postBody, $circleId, $userId, $teamId, array $files)
    {
        /** @var PostDraftService $PostDraftService */
        $PostDraftService = ClassRegistry::init("PostDraftService");
        $postDraft = $PostDraftService->createPostDraftWithResources(
            am($postBody, [
                'is_api_v2' => true,
                'circle_id' => $circleId,
                'files'     => $files,
                'share'     => 'circle_' . $circleId,
            ]),
            $userId,
            $teamId,
            $files
        );
        return $postDraft;
    }

    /**
     * Notify new post to other members
     *
     * @param PostEntity $newPost
     * @param int        $circleId
     */
    private function notifyNewPost(PostEntity $newPost, int $circleId, array $mentionedUserIds = [])
    {
        // Notify to other members
        $postedPostId = $newPost['id'];
        $notifyType = NotifySetting::TYPE_FEED_POST;

        /** @var NotifyBizComponent $NotifyBiz */
        $this->NotifyBiz->execSendNotify($notifyType, $postedPostId, null, null, $newPost['team_id'], $newPost['user_id']);
        
        $this->NotifyBiz->execSendNotify(
                    NotifySetting::TYPE_FEED_MENTIONED_IN_POST,
                    $postedPostId,
                    null,
                    $mentionedUserIds,
                    $newPost['team_id'],
                    $$newPost['user_id']
                );
         

        /** @var PostPusherService $PostPusherService */
        $PostPusherService = ClassRegistry::init('PostPusherService');
        $PostPusherService->setSocketId($this->getSocketId());
        $PostPusherService->sendFeedNotification($circleId, $newPost);
    }


    public function get_comments(int $postId)
    {
        $error = $this->validatePostAccess($postId);
        if (!empty($error)) {
            return $error;
        }

        /** @var CommentPagingService $CommentPagingService */
        $CommentPagingService = ClassRegistry::init("CommentPagingService");

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        $result = $CommentPagingService->getDataWithPaging($pagingRequest, $this->getPagingLimit(),
            $this->getExtensionOptions() ?: $this->getDefaultCommentsExtension());

        return ApiResponse::ok()->withBody($result)->getResponse();
    }

    /**
     * Default extension options for getting comments
     *
     * @return array
     */
    private function getDefaultCommentsExtension()
    {
        return [
            CommentExtender::EXTEND_ALL
        ];
    }

    /**
     * Get list of the post readers
     *
     * @param int $postId
     *
     * @return BaseApiResponse
     */
    public function get_reads(int $postId)
    {
        $error = $this->validatePostAccess($postId);

        if (!empty($error)) {
            return $error;
        }

        /** @var PostReaderPagingService $PostReaderPagingService */
        $PostReaderPagingService = ClassRegistry::init("PostReaderPagingService");

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        try {
            $result = $PostReaderPagingService->getDataWithPaging(
                $pagingRequest,
                $this->getPagingLimit(),
                $this->getExtensionOptions() ?: $this->getDefaultReaderExtension());
        } catch (Exception $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withBody($result)->getResponse();
    }

    /**
     * Add readers of the post
     *
     * @param int $postId
     *
     * @return BaseApiResponse
     */
    public function post_reads()
    {

        $error = $this->validatePostRead();
        if (!empty($error)) {
            return $error;
        }

        $postsIds = Hash::get($this->getRequestJsonBody(), 'posts_ids', []);
        $postsIds = array_unique($postsIds);

        /** @var PostReadService $PostReadService */
        $PostReadService = ClassRegistry::init('PostReadService');

        try {
            $res = $PostReadService->multipleAdd($postsIds, $this->getUserId(), $this->getTeamId());
        } catch (InvalidArgumentException $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->withMessage(__("Failed to read post."))
                ->getResponse();
        }

        return ApiResponse::ok()->withData(["posts_ids" => $res])->getResponse();
    }

    /**
     * Default extension options for getting user that readers of the post
     *
     * @return array
     */
    private function getDefaultReaderExtension()
    {
        return [
            PostReadExtender::EXTEND_USER
        ];
    }

    /**
     * Default extension options for getting user that likes the post
     *
     * @return array
     */
    private function getDefaultLikesUserExtension()
    {
        return [
            PostLikeExtender::EXTEND_USER
        ];
    }

    /**
     * Endpoint for editing a post
     *
     * @param int $postId
     *
     * @return CakeResponse
     */
    public function put(int $postId): CakeResponse
    {
        $error = $this->validatePut($postId);

        if (!empty($error)) {
            return $error;
        }

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $newBody['body'] = Hash::get($this->getRequestJsonBody(), 'body');
        $newBody['site_info'] = Hash::get($this->getRequestJsonBody(), 'site_info');
        $resources = Hash::get($this->getRequestJsonBody(), 'resources');

        try {
            /** @var PostEntity $newPost */
            $newPost = $PostService->editPost($newBody, $postId, $this->getUserId(), $this->getTeamId(), $resources);
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }
        /** @var PostExtender $PostExtender */
        $PostExtender = ClassRegistry::init('PostExtender');

        $newPost = $PostExtender->extend($newPost->toArray(), $this->getUserId(), $this->getTeamId(), [PostExtender::EXTEND_ALL]);

        return ApiResponse::ok()->withData($newPost)->getResponse();
    }

    public function get_detail(int $postId): CakeResponse
    {
        $error = $this->validatePostAccess($postId);
        if (!empty($error)) {
            return $error;
        }

        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        $post = $Post->useType()->getById($postId);

        /** @var PostExtender $PostExtender */
        $PostExtender = ClassRegistry::init('PostExtender');

        $post = $PostExtender->extend($post, $this->getUserId(), $this->getTeamId(), [
            PostExtender::EXTEND_ALL,
            PostExtender::EXTEND_COMMENTS_ALL,
            PostExtender::EXTEND_TRANSLATION_LANGUAGE
        ]);

        // Make user read this post
        // Decreasing unread count if this post haven't read yet.
        if (!$post['is_read']) {
            /** @var PostReadService $PostReadService */
            $PostReadService = ClassRegistry::init('PostReadService');
            $PostReadService->multipleAdd([$postId], $this->getUserId(), $this->getTeamId());
            /** @var CircleMemberService $CircleMemberService */
            $CircleMemberService = ClassRegistry::init('CircleMemberService');
            $firstSharedCircle = reset($post['shared_circles']);
            $CircleMemberService->decreaseCircleUnreadCount($firstSharedCircle['id'], $this->getUserId(), $this->getTeamId(), 1);
        }


        return ApiResponse::ok()->withData($post)->getResponse();
    }

    public function post_likes(int $postId): CakeResponse
    {
        $res = $this->validatePostAccess($postId);

        if (!empty($res)) {
            return $res;
        }

        /** @var PostLikeService $PostLikeService */
        $PostLikeService = ClassRegistry::init('PostLikeService');

        try {
            $result = $PostLikeService->add($postId, $this->getUserId(), $this->getTeamId());
        } catch (GlException\GoalousConflictException $exception) {
            return ErrorResponse::resourceConflict()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData((empty($result)) ? [] : $result->toArray())->getResponse();
    }

    public function delete(int $postId)
    {
        $error = $this->validateDelete($postId);

        if (!empty($error)) {
            return $error;
        }

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        try {
            $PostService->softDelete($postId);
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData(["post_id" => $postId])->getResponse();
    }

    /**
     * @param int $postId
     *
     * @return CakeResponse
     */
    public function delete_likes(int $postId): CakeResponse
    {
        $res = $this->validatePostAccess($postId);

        if (!empty($res)) {
            return $res;
        }

        /** @var PostLikeService $PostLikeService */
        $PostLikeService = ClassRegistry::init('PostLikeService');

        try {
            $count = $PostLikeService->delete($postId, $this->getUserId());
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }
        return ApiResponse::ok()->withData(["like_count" => $count])->getResponse();
    }

    /**
     * Get list of the user who likes the post
     *
     * @param int $postId
     *
     * @return BaseApiResponse
     */
    public function get_likes(int $postId)
    {
        $error = $this->validatePostAccess($postId);
        if (!empty($error)) {
            return $error;
        }

        /** @var PostLikesPagingService $PostLikesPagingService */
        $PostLikesPagingService = ClassRegistry::init("PostLikesPagingService");

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        try {
            $result = $PostLikesPagingService->getDataWithPaging(
                $pagingRequest,
                $this->getPagingLimit(),
                $this->getExtensionOptions() ?: $this->getDefaultLikesUserExtension());
        } catch (Exception $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withBody($result)->getResponse();
    }

    /**
     * Post save method
     *
     * @param int $postId
     *
     * @return BaseApiResponse
     */
    public function post_saves(int $postId): CakeResponse
    {
        $res = $this->validatePostAccess($postId);

        if (!empty($res)) {
            return $res;
        }

        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        try {
            $result = $SavedPostService->add($postId, $this->getUserId(), $this->getTeamId());
        } catch (GlException\GoalousConflictException $ConflictException) {
            return ErrorResponse::resourceConflict()->withException($ConflictException)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData($result->toArray())->getResponse();
    }

    /**
     * @param int $postId
     *
     * @return CakeResponse
     */
    public function delete_saves(int $postId): CakeResponse
    {
        $res = $this->validatePostAccess($postId);

        if (!empty($res)) {
            return $res;
        }

        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        try {
            $SavedPostService->delete($postId, $this->getUserId());
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }
        return ApiResponse::ok()->withData(["post_id" => $postId])->getResponse();
    }

    /**
     * Endpoint for saving a new comment
     *
     * @param int $postId Id of the post to comment to
     *
     * @return CakeResponse
     */
    public function post_comments(int $postId)
    {
        /* Validate user access to this post */
        $error = $this->validatePostComments($postId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $requestBody = $this->getRequestJsonBody();
        $commentData['body'] = Hash::get($requestBody, 'body');
        $commentData['site_info'] = Hash::get($requestBody, 'site_info');
        $resources = Hash::get($requestBody, 'resources', []);
        $fileIDs = Hash::extract($resources, '{n}.file_uuid') ?? [];
        $userId = $this->getUserId();
        $teamId = $this->getTeamId();
        try {
            $res = $CommentService->add($commentData, $postId, $userId, $teamId, $fileIDs);
            $mentionedUserIds = $this->Mention->getUserList($commentData['body'], $this->getTeamId(), $this->getUserId());
            $this->notifyNewComment($res['id'], $postId, $this->getUserId(), $this->getTeamId(), $mentionedUserIds);

            /** @var TeamTranslationLanguage $TeamTranslationLanguage */
            $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
            if ($TeamTranslationLanguage->hasLanguage($this->getTeamId())) {
                $this->sendTranslationUsageNotification($teamId);
            }
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (InvalidArgumentException $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->withMessage(__("Failed to comment."))
                ->getResponse();
        }

        /** @var CommentExtender $CommentExtender */
        $CommentExtender = ClassRegistry::init('CommentExtender');
        $comment = $res->toArray();
        $comment = $CommentExtender->extend($comment, $userId, $teamId, [CommentExtender::EXTEND_ALL]);

        return ApiResponse::ok()->withData($comment)->getResponse();
    }

    /**
     * @return CakeResponse|null
     */
    private function validatePost()
    {
        $requestBody = $this->getRequestJsonBody();

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $circleId = (int)Hash::get($requestBody, 'circle_id');

        if (!empty($circleId) && !$CircleMember->isJoined($circleId, $this->getUserId())) {
            return ErrorResponse::forbidden()->withMessage(__("The circle doesn't exist or you don't have permission."))
                ->getResponse();
        }
        try {
            PostRequestValidator::createDefaultPostValidator()->validate($requestBody);
            PostRequestValidator::createFileUploadValidator()->validate($requestBody);
            switch ($requestBody['type']) {
                case Post::TYPE_NORMAL:
                    PostRequestValidator::createCirclePostValidator()->validate($requestBody);
                    break;
            }
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

        return null;
    }

    /**
     * Validate access to post
     *
     * @param int  $postId
     * @param bool $mustBelong Whether user must belong to the circle where post is made
     *
     * @return CakeResponse|null
     */
    private function validatePostAccess(int $postId, bool $mustBelong = false)
    {
        if (empty($postId) || !is_int($postId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        try {
            $access = $PostService->checkUserAccessToCirclePost($this->getUserId(), $postId);
        } catch (GlException\GoalousNotFoundException $notFoundException) {
            return ErrorResponse::notFound()->withException($notFoundException)->getResponse();
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        //Check if user belongs to a circle where the post is shared to
        if (!$access) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this post"))
                ->getResponse();
        }

        return null;
    }

    /**
     * Validate deleting post endpoint
     *
     * @param int $postId
     *
     * @return ErrorResponse|null
     */
    private function validateDelete(int $postId)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        if (!$Post->exists($postId)) {
            return ErrorResponse::notFound()->withMessage(__("This post doesn't exist."))->getResponse();
        }

        if (!$Post->isPostOwned($postId, $this->getUserId()) && !$TeamMember->isActiveAdmin($this->getUserId(),
                $this->getTeamId())) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this post"))
                ->getResponse();
        }
        return null;
    }

    /**
     * @param $postId
     *
     * @return CakeResponse| null
     */
    private function validatePut(int $postId)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        if (!$Post->exists($postId)) {
            return ErrorResponse::notFound()->withMessage(__("This post doesn't exist."))->getResponse();
        }
        //Check whether user is the owner of the post
        if (!$Post->isPostOwned($postId, $this->getUserId())) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this post"))
                ->getResponse();
        }

        $body = $this->getRequestJsonBody();

        try {

            PostRequestValidator::createPostEditValidator()->validate($body);
            /**
             * FixMe For now, post edit doesn't allow new videos.
             * JIRA task: GL-7826
             */
            PostRequestValidator::createPostEditFileValidator()->validate($body);
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

        return null;
    }

    /**
     * @return CakeResponse|null
     */
    private function validatePostRead()
    {
        $requestBody = $this->getRequestJsonBody();

        $postsIds = Hash::get($requestBody, 'posts_ids', []);

        try {
            PostRequestValidator::createPostReadValidator()->validate($requestBody);
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

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        try {
            $PostService->checkUserAccessToMultiplePost($this->getUserId(), $postsIds);
        } catch (GlException\GoalousNotFoundException $notFoundException) {
            return ErrorResponse::notFound()->withException($notFoundException)->getResponse();
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        return null;
    }

    private function validatePostComments(int $postId)
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $requestBody = $this->getRequestJsonBody();

        try {
            PostRequestValidator::createPostCommentValidator()->validate($requestBody);
            PostRequestValidator::createFileUploadValidator()->validate($requestBody);
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

        try {
            $access = $PostService->checkUserAccessToCirclePost($this->getUserId(), $postId, true);
        } catch (GlException\GoalousNotFoundException $notFoundException) {
            return ErrorResponse::notFound()->withException($notFoundException)->getResponse();
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        //Check if user belongs to a circle where the post is shared to
        if (!$access) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this post"))
                ->getResponse();
        }
        return null;
    }

    /**
     * Send notification about new comment on a post.
     * Will notify post's author & other users who've commented on the post
     *
     * @param int   $commentId        Comment ID of the new comment
     * @param int   $postId           Post ID where the comment belongs to
     * @param int   $userId           User ID of the author of the new comment
     * @param int   $teamId
     * @param int[] $mentionedUserIds List of user IDs of mentioned users
     */
    private function notifyNewComment(int $commentId, int $postId, int $userId, int $teamId, array $mentionedUserIds = [])
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $type = $Post->getPostType($postId);

        switch ($type) {
            case Post::TYPE_NORMAL:
                // This notification must not be sent to those who mentioned
                // because we exlude them in NotifyBiz#execSendNotify.
                $this->NotifyBiz->execSendNotify(
                    NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST,
                    $postId,
                    $commentId,
                    null,
                    $teamId,
                    $userId
                );
                $this->NotifyBiz->execSendNotify(
                    NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST,
                    $postId,
                    $commentId,
                    null,
                    $teamId,
                    $userId
                );
                $this->NotifyBiz->execSendNotify(
                    NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT,
                    $postId,
                    $commentId,
                    $mentionedUserIds,
                    $teamId,
                    $userId
                );
                break;
            case Post::TYPE_ACTION:
                // This notification must not be sent to those who mentioned
                // because we exlude them in NotifyBiz#execSendNotify.
                $this->NotifyBiz->execSendNotify(
                    NotifySetting::TYPE_FEED_COMMENTED_ON_MY_ACTION,
                    $postId,
                    $commentId,
                    null,
                    $teamId,
                    $userId
                );
                $this->NotifyBiz->execSendNotify(
                    NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION,
                    $postId,
                    $commentId,
                    null,
                    $teamId,
                    $userId
                );
                $this->NotifyBiz->execSendNotify(
                    NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT,
                    $postId,
                    $commentId,
                    $mentionedUserIds,
                    $teamId,
                    $userId
                );
                break;
            case Post::TYPE_CREATE_GOAL:
                $this->notifyUserOfGoalComment($userId, $postId);
                break;
        }

        /** @var PusherService $PusherService */
        $PusherService = ClassRegistry::init("PusherService");
        /** @var NewCommentNotifiable $NewCommentNotifiable */
        $socketId = $this->getSocketId();
        $NewCommentNotifiable = ClassRegistry::init("NewCommentNotifiable");
        $NewCommentNotifiable->build($commentId, $postId, $teamId);
        $PusherService->notify($socketId, $NewCommentNotifiable);
    }

    /**
     * Send notification if a Goal post is commented
     *
     * @param int $commentAuthorUserId ID of user who made the comment
     * @param int $postId              Post ID where the comment belongs to
     */
    private function notifyUserOfGoalComment(int $commentAuthorUserId, int $postId)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $postData = $Post->getEntity($postId);

        $postId = $postData['id'];
        $postOwnerUserId = $postData['user_id'];

        //If commenter is not post owner, send notification to owner
        if ($commentAuthorUserId !== $postOwnerUserId) {
            $this->NotifyBiz->sendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_GOAL, null, null,
                [$postOwnerUserId], $commentAuthorUserId, $postData['team_id'], $postId);
        }
        $excludedUserList = array($postOwnerUserId, $commentAuthorUserId);

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        $notificationReceiverUserList = $Comment->getCommentedUniqueUsersList($postId, false, $excludedUserList);

        if (!empty($notificationReceiverUserList)) {
            $this->NotifyBiz->sendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_COMMENTED_GOAL, null, null,
                $notificationReceiverUserList, $commentAuthorUserId, $postData['team_id'], $postId);
        }
    }
}
