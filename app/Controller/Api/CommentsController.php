<?php
App::import('Service', 'CommentService');
App::import('Service', 'CommentLikeService');
App::uses('BaseApiController', 'Controller/Api');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 14:21
 */
class CommentsController extends BaseApiController
{
    /**
     * Endpoint for adding a like to a post
     *
     * @param int $commentId
     *
     * @return CakeResponse|null
     */
    public function post_like(int $commentId)
    {
        $error = $this->validatePostCommentLike($commentId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CommentLikeService $CommentLikeService */
        $CommentLikeService = ClassRegistry::init('CommentLikeService');

        try {
            $commentLike = $CommentLikeService->addCommentLike($commentId, $this->getUserId(), $this->getTeamId());
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData($commentLike->toArray())->getResponse();
    }

    /**
     * Endpoint for removing a like from a post
     *
     * @param int $commentId
     *
     * @return CakeResponse|null
     */
    public function delete_like(int $commentId)
    {
        $error = $this->validateDeleteCommentLike($commentId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CommentLikeService $CommentLikeService */
        $CommentLikeService = ClassRegistry::init('CommentLikeService');

        try {
            $newCount = $CommentLikeService->removeCommentLike($commentId, $this->getUserId(), $this->getTeamId());
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData(['like_count' => $newCount])->getResponse();

    }

    /**
     * @param int $commentId
     *
     * @return ErrorResponse | null
     */
    private function validatePostCommentLike(int $commentId)
    {
        if (empty($commentId) || !is_int($commentId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');
        try {
            $access = $CommentService->checkUserHasAccessToPost($this->getUserId(), $commentId);
        } catch (InvalidArgumentException $invalidArgumentException) {
            return ErrorResponse::badRequest()->withException($invalidArgumentException)->getResponse();
        }
        if (!$access) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this post"))
                                ->getResponse();
        }

        return null;
    }

    /**
     * @param int $commentId
     *
     * @return ErrorResponse | null
     */
    private function validateDeleteCommentLike(int $commentId)
    {
        if (empty($commentId) || !is_int($commentId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        return null;
    }
}