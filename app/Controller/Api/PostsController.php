<?php

App::import('Service', 'POstService');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');
App::uses('BaseApiController', 'Controller/Api');
App::uses('PostShareCircle', 'Model');
App::uses('PostRequestValidator', 'Validator/Request/Api/V2');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/18
 * Time: 15:00
 */
class PostsController extends BaseApiController
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
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_INTERNAL_SERVER_ERROR))->withException($e)->getResponse();
        }

        //If post saving failed, $res will be false
        if ($res === false) {
            return (new ApiResponse(ApiResponse::RESPONSE_INTERNAL_SERVER_ERROR))->withMessage(__("Failed to post."))
                                                                                 ->getResponse();
        }

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->getResponse();
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

        if (!$CircleMember->isJoined($circleId, $this->getUserId())) {
            return (new ApiResponse(ApiResponse::RESPONSE_UNAUTHORIZED))->withMessage(__("The circle dosen't exist or you don't have permission."))
                                                                        ->getResponse();
        }

        try {
            PostRequestValidator::createDefaultPostValidator()->validate($requestBody);

            switch ($requestBody['type']) {
                case Post::TYPE_NORMAL:
                    PostRequestValidator::createCirclePostValidator()->validate($requestBody);
                    break;
            }
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
                                                                       ->getResponse();
        }

        return null;
    }
}