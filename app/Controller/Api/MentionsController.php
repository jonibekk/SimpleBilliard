<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Service', 'PostService');
App::import('Service/Paging', 'MentionPagingService');
App::uses('PagingRequest', 'Lib/Paging');

use Goalous\Exception as GlException;

class MentionsController extends BasePagingController
{
    public function get_search()
    {
        $resourceId = $this->request->query('resource_id');
        $resourceType = $this->request->query('resource_type');
        switch ($resourceType) {
            case 1:
                $postId = $resourceId;
                $error = $this->validatePostAccess($postId);
                break;
            case 2:
                $circleId = $resourceId;
                $error = $this->validateCircleAccess($circleId);
                break;
            default:
                return null; 
                break;

        }
        // $error = $this->validatePostAccess($postId);

        if (!empty($error)) {
            return $error;
        }

        /** @var MentionPagingService $MentionPagingService */
        $MentionPagingService = ClassRegistry::init('MentionPagingService');

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        try {
            $data = $MentionPagingService->getAllData(
                $pagingRequest,
                $this->getPagingLimit());
        } catch (Exception $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withBody($data)->getResponse();
    }


    /**
     * Validate access to post
     *
     * @param int  $postId
     * @return CakeResponse|null
     */
    private function validatePostAccess($postId)
    {
        if (empty($postId)) {
            return null;
        }
        if (!AppUtil::isInt($postId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        try {
            $access = $PostService->checkUserAccessToCirclePost($this->getUserId(), (int)$postId);
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
     * Validate access to post
     *
     * @param int  $postId
     * @return CakeResponse|null
     */
    private function validateCircleAccess($circleId)
    {
        if (empty($circleId)) {
            return null;
        }
        if (!AppUtil::isInt($circleId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var PostService $PostService */
        $CircleService = ClassRegistry::init('CircleService');

        GoalousLog::error('enter');
        try {
            $access = $CircleService->checkUserAccessToCircle($this->getUserId(), (int)$circleId);
        } catch (GlException\GoalousNotFoundException $notFoundException) {
            return ErrorResponse::notFound()->withException($notFoundException)->getResponse();
        } catch (Exception $exception) {
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        //Check if user belongs to a circle where the post is shared to
        if (!$access) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to access this circle"))
                ->getResponse();
        }

        return null;
    }

}
