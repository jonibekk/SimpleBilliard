<?php

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
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)->getResponse();
        }

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->getResponse();
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
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)->getResponse();
        }

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->getResponse();

    }

    /**
     * @param int $commentId
     *
     * @return CakeResponse | null
     */
    private function validateLike(int $commentId)
    {
        if (!is_int($commentId)) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->getResponse();
        }

        return null;
    }
}