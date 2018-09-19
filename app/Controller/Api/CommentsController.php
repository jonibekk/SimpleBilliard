<?php
App::import('Service', 'CommentService');
App::import('Service', 'CommentLikeService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('BasePagingController', 'Controller/Api');
App::uses('BaseApiController', 'Controller/Api');
App::import('Service/Paging', 'CommentReaderPagingService');
App::import('Service/Paging', 'CommentLikesPagingService');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 14:21
 */

use Goalous\Exception as GlException;

class CommentsController extends BasePagingController
{
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
     * Default extension options for getting user that readers of the comment
     *
     * @return array
     */
    private function getDefaultReaderExtension()
    {
        return [
            CommentReaderPagingService::EXTEND_USER
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
            CommentLikesPagingService::EXTEND_USER
        ];
    }
}