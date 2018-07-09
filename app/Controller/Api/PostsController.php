<?php

App::import('Service', 'PostService');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');
App::uses('BasePagingController', 'Controller/Api');
App::uses('PostShareCircle', 'Model');
App::uses('PostRequestValidator', 'Validator/Request/Api/V2');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/18
 * Time: 15:00
 */
class PostsController extends BasePagingController
{

    /**
     * Endpoint for saving both circle posts and action posts
     *
     * @return CakeResponse|null
     */
    public function post()
    {
        $error = $this->validatePost();

        if (!empty($error)) {
            return $error;
        }

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $post['body'] = Hash::get($this->getRequestJsonBody(), 'body');
        $post['type'] = Hash::get($this->getRequestJsonBody(), 'type');

        $circleId = Hash::get($this->getRequestJsonBody(), 'circle_id');

        try {
            $res = $PostService->addCirclePost($post, $circleId, $this->getUserId(), $this->getTeamId());
        } catch (InvalidArgumentException $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        //If post saving failed, $res will be false
        if (empty($res)) {
            return ErrorResponse::internalServerError()->withMessage(__("Failed to post."))->getResponse();
        }

        return ApiResponse::ok()->getResponse();
    }

    public function get_comments(int $postId)
    {
        $error = $this->validateGetComments($postId);
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
            $this->getExtensionOptions());

        return ApiResponse::ok()->withBody($result)->getResponse();
    }

    /**
     * @return CakeResponse|null
     */
    private function validatePost()
    {
        $requestBody = $this->getRequestJsonBody();

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $circleId = Hash::get($requestBody, 'circle_id');

        if (!empty($circleId) && !$CircleMember->isJoined($circleId, $this->getUserId())) {
            return ErrorResponse::forbidden()->withMessage(__("The circle dosen't exist or you don't have permission."))
                                ->getResponse();
        }

        try {
            PostRequestValidator::createDefaultPostValidator()->validate($requestBody);

            switch ($requestBody['type']) {
                case Post::TYPE_NORMAL:
                    PostRequestValidator::createCirclePostValidator()->validate($requestBody);
                    break;
            }
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                                ->addErrorsFromValidationException($e)
                                ->withMessage(__('validation failed'))
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
     * Validate get comments endpoint
     *
     * @param int $postId
     *
     * @return BaseApiResponse|ErrorResponse|null
     */
    public function validateGetComments(int $postId)
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $hasAccess = $PostService->checkUserAccessToPost($this->getUserId(), $postId);

        if (!$hasAccess) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have permission to view this post"))
                                ->getResponse();
        }

        return null;
    }
}