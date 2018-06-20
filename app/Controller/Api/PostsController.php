<?php
App::import('Service', 'POstService');
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

        $post = $this->getRequestJsonBody();

        $circleId = $post['circle_id'];
        unset($post['circle_id']);

        try {
            $res = $PostService->addCirclePost($post, $circleId, $this->getUserId(), $this->getTeamId());
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_INTERNAL_SERVER_ERROR))->withException($e)->getResponse();
        }

        //If post saving failed, $res will be false
        if (is_bool($res) && !$res) {
            return (new ApiResponse(ApiResponse::RESPONSE_INTERNAL_SERVER_ERROR))->withMessage(__("Failed to post."))
                                                                                 ->getResponse();
        }

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->getResponse();
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

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $newBody = Hash::get($this->getRequestJsonBody(), 'body');

        try {
            $Post->editPost($newBody, $postId);
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_INTERNAL_SERVER_ERROR))->withException($e)->getResponse();
        }

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->getResponse();
    }

    /**
     * @return CakeResponse|null
     */
    private function validatePost()
    {
        $error = $this->allowMethod('POST');

        if (!empty($error)) {
            return $error;
        }

        $body = $this->getRequestJsonBody();

        try {

            PostRequestValidator::createDefaultPostValidator()->validate($body);

            switch ($body['type']) {
                case Post::TYPE_NORMAL:
                    PostRequestValidator::createCirclePostValidator()->validate($body);
                    break;
            }
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
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

        //Check whether user is the owner of the post
        if (!$Post->isPostOwned($postId, $this->getUserId())) {
            return (new ApiResponse(ApiResponse::RESPONSE_UNAUTHORIZED))->getResponse();
        }

        $body = $this->getRequestJsonBody();

        try {

            PostRequestValidator::createPostEditValidator()->validate($body);

        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
                                                                       ->getResponse();
        }

        return null;
    }
}