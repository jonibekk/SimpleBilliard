<?php
App::import('Service', 'CommentService');
App::import('Service', 'CommentLikeService');
App::import('Service', 'CommentReadService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('BasePagingController', 'Controller/Api');
App::uses('BaseApiController', 'Controller/Api');
App::import('Service/Paging', 'CommentReaderPagingService');
App::import('Service/Paging', 'CommentLikesPagingService');
App::uses('CommentRequestValidator', 'Validator/Request/Api/V2');
App::uses('Comment', 'Model');
App::uses('TeamMember', 'Model');
App::import('Lib/DataExtender', 'CommentExtender');
App::import('Service/Request/Form', 'CommentUpdateRequest');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 14:21
 */

use Goalous\Exception as GlException;

class CommentsController extends BasePagingController
{
    public $components = [
        'NotifyBiz',
        'GlEmail',
        'Mention'
    ];

    /**
     * Endpoint for adding a like to a post
     *
     * @param int $commentId
     *
     * @return CakeResponse|null
     */
    public function post_likes(int $commentId)
    {
        $error = $this->validateAccessToComment($commentId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CommentLikeService $CommentLikeService */
        $CommentLikeService = ClassRegistry::init('CommentLikeService');

        try {
            $commentLike = $CommentLikeService->add($commentId, $this->getUserId(), $this->getTeamId());
        } catch (GlException\GoalousConflictException $exception) {
            return ErrorResponse::resourceConflict()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData($commentLike->toArray())->getResponse();
    }

    /**
     * Get list of the comment readers
     *
     * @param int $commentId
     *
     * @return BaseApiResponse
     */
    public function get_reads(int $commentId)
    {
        $error = $this->validateAccessToComment($commentId);
        if (!empty($error)) {
            return $error;
        }

        /** @var CommentReaderPagingService $CommentReaderPagingService */
        $CommentReaderPagingService = ClassRegistry::init("CommentReaderPagingService");

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        try {
            $result = $CommentReaderPagingService->getDataWithPaging(
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
     * Add readers of the comment
     *
     * @param int $commentId
     *
     * @return BaseApiResponse
     */
    public function post_reads()
    {

        $error = $this->validateCommentRead();
        if (!empty($error)) {
            return $error;
        }

        $commentsIds = Hash::get($this->getRequestJsonBody(), 'comment_ids', []);

        /** @var CommentReadService $CommentReadService */
        $CommentReadService = ClassRegistry::init('CommentReadService');

        try {
            $res = $CommentReadService->multipleAdd($commentsIds, $this->getUserId(), $this->getTeamId());
        } catch (InvalidArgumentException $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->withMessage(__("Failed to read comment."))
                                ->getResponse();
        }

        return ApiResponse::ok()->withData(["comment_ids" => $res])->getResponse();
    }

    /**
     * Endpoint for removing a like from a post
     *
     * @param int $commentId
     *
     * @return CakeResponse|null
     */
    public function delete_likes(int $commentId)
    {
        $error = $this->validateAccessToComment($commentId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CommentLikeService $CommentLikeService */
        $CommentLikeService = ClassRegistry::init('CommentLikeService');

        try {
            $newCount = $CommentLikeService->delete($commentId, $this->getUserId(), $this->getTeamId());
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData(['like_count' => $newCount])->getResponse();
    }

    /**
     * Endpoint for removing a comment
     *
     * @param int $commentId
     *
     * @return CakeResponse|null
     */
    public function delete(int $commentId)
    {
        $error = $this->validateDelete($commentId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        try {
            $CommentService->delete($commentId);
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData(["comment_id" => $commentId])->getResponse();
    }

    /**
     * Endpoint for editing a comment
     *
     * @param int $commentId
     *
     * @return CakeResponse
     */
    public function put(int $commentId): CakeResponse
    {
        $error = $this->validatePut($commentId);
        if (!empty($error)) {
            return $error;
        }

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $siteInfo = Hash::get($this->getRequestJsonBody(), 'site_info');
        $body = Hash::get($this->getRequestJsonBody(), 'body');
        $resources = Hash::get($this->getRequestJsonBody(), 'resources');
        try {
            $userId = $this->getUserId();
            $teamId = $this->getTeamId();
            $commentUpdateRequest = new CommentUpdateRequest($commentId, $userId, $teamId, $body, $siteInfo, $resources);
            /** @var CommentEntity $updatedComment */
            $updatedComment = $CommentService->edit($commentUpdateRequest);
            $mentionedUserIds = $this->Mention->getUserList($updatedComment['body'], $teamId, $userId);
            $this->notifyUpdateComment($updatedComment['id'], $updatedComment['post_id'],$userId, $teamId, $mentionedUserIds);
        } catch (GlException\GoalousNotFoundException $exception) {
            return ErrorResponse::notFound()->withException($exception)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }
        /** @var CommentExtender $CommentExtender */
        $CommentExtender = ClassRegistry::init('CommentExtender');
        $res = $CommentExtender->extend($updatedComment->toArray(), $userId, $teamId, [$CommentExtender::EXTEND_ALL]);
        return ApiResponse::ok()->withData($res)->getResponse();
    }


    /**
     * Send notification about updated comment on a post.
     * Will notify only if mention
     *
     * @param int $commentId Comment ID of the new comment
     * @param int $postId Post ID where the comment belongs to
     * @param int $userId User ID of the author of the updated comment
     * @param int $teamId
     * @param int[] $mentionedUserIds List of user IDs of mentioned users
     */
    private function notifyUpdateComment(int $commentId, int $postId, int $userId, int $teamId, array $mentionedUserIds = [])
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $type = $Post->getPostType($postId);

        switch ($type) {
            case Post::TYPE_NORMAL:
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
                $this->NotifyBiz->execSendNotify(
                    NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT,
                    $postId,
                    $commentId,
                    $mentionedUserIds,
                    $teamId,
                    $userId
                );
                break;
        }
    }


    /**
     * Get list of the user who likes the comment
     *
     * @param int $commentId
     *
     * @return BaseApiResponse
     */
    public function get_likes(int $commentId)
    {
        $error = $this->validateAccessToComment($commentId);
        if (!empty($error)) {
            return $error;
        }

        /** @var CommentLikesPagingService $CommentLikesPagingService */
        $CommentLikesPagingService = ClassRegistry::init("CommentLikesPagingService");

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        try {
            $result = $CommentLikesPagingService->getDataWithPaging(
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
     * @param int $commentId
     *
     * @return ErrorResponse | null
     */
    private function validateAccessToComment(int $commentId)
    {
        if (empty($commentId) || !is_int($commentId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');
        try {
            $access = $CommentService->checkUserAccessToComment($this->getUserId(), $commentId);
        } catch (GlException\GoalousNotFoundException $notFoundException) {
            return ErrorResponse::notFound()->withException($notFoundException)->getResponse();
        }
        if (!$access) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this comment"))
                ->getResponse();
        }

        return null;
    }

    /**
     * Validate whether user is the owner of the comment
     *
     * @param int $commentId
     *
     * @return ErrorResponse | null
     */
    private function validateDelete(int $commentId)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        if (!$Comment->exists($commentId)) {
            return ErrorResponse::notFound()->withMessage(__("This comment doesn't exist."))->getResponse();
        }

        if (!$Comment->isCommentOwned($commentId, $this->getUserId()) && !$TeamMember->isActiveAdmin($this->getUserId(),
                $this->getTeamId())) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this comment"))
                ->getResponse();
        }
        return null;
    }

    /**
     * @param $commentId
     *
     * @return CakeResponse| null
     */
    private function validatePut(int $commentId)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        if (!$Comment->exists($commentId)) {
            return ErrorResponse::notFound()->withMessage(__("This comment doesn't exist."))->getResponse();
        }
        //Check whether user is the owner of the comment
        if (!$Comment->isCommentOwned($commentId, $this->getUserId())) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this comment"))
                ->getResponse();
        }
        $body = $this->getRequestJsonBody();
        try {
            CommentRequestValidator::createCommentEditValidator()->validate($body);
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
     * Default extension options for getting user that readers of the comment
     *
     * @return array
     */
    private function getDefaultReaderExtension()
    {
        return [
            CommentReadExtender::EXTEND_USER
        ];
    }

    /**
     * Default extension options for getting user that likes the comment
     *
     * @return array
     */
    private function getDefaultLikesUserExtension()
    {
        return [
            CommentLikeExtender::EXTEND_USER
        ];
    }

    /**
     *
     * @return CakeResponse|null
     */
    private function validateCommentRead()
    {
        $requestBody = $this->getRequestJsonBody();

        $commentsIds = Hash::get($requestBody, 'comment_ids', []);

        try {
            CommentRequestValidator::createCommentReadValidator()->validate($requestBody);
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

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        try {
            $CommentService->checkUserAccessToMultipleComment($this->getUserId(), $commentsIds);
        } catch (GlException\GoalousNotFoundException $notFoundException) {
            return ErrorResponse::notFound()->withException($notFoundException)->getResponse();
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        return null;
    }
}
