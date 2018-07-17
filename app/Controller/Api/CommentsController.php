<?php
App::import('Service', 'CommentService');
App::uses('CommentLike', 'Model');
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
        $error = $this->validateLike($commentId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        try {
            $CommentLike->addCommentLike($commentId, $this->getUserId(), $this->getTeamId());
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->getResponse();
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
        $error = $this->validateLike($commentId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        try {
            $CommentLike->removeCommentLike($commentId, $this->getUserId(), $this->getTeamId());
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->getResponse();

    }

    /**
     * @param int $commentId
     *
     * @return ErrorResponse | null
     */
    private function validateLike(int $commentId)
    {
        if (empty($commentId) && !is_int($commentId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        if (!$CommentService->checkUserHasAccessToPost($this->getUserId(), $commentId)) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this post"))
                                ->getResponse();
        }

        return null;
    }
}