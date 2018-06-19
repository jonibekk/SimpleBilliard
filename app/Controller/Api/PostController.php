<?php
App::import('Service', 'POstService');
App::uses('Post', 'Model');
App::uses('BaseApiController', 'Controller/Api');
App::uses('PostRequestValidator', 'Validator/Request/Api/V2');
App::uses('PostLike', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/18
 * Time: 15:00
 */
class PostController extends BaseApiController
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

        $post['Post'] = $this->getRequestJsonBody();

        try {
            $res = $PostService->addNormalWithTransaction($post, $this->getUserId(), $this->getTeamId());
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
     * @param int $postId
     *
     * @return CakeResponse
     */
    public function post_like(int $postId): CakeResponse
    {
        $res = $this->validatePostLike($postId);

        if (!empty($res)) {
            return $res;
        }

        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        try {
            $PostLike->addPostLike($postId, $this->getUserId(), $this->getTeamId());
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
                                                                       ->getResponse();
        }

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->getResponse();
    }

    /**
     * @param int $postId
     *
     * @return CakeResponse
     */
    public function delete_like(int $postId): CakeResponse
    {
        $res = $this->validateDeleteLike($postId);

        if (!empty($res)) {
            return $res;
        }

        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        try {
            $PostLike->deletePostLike($postId, $this->getUserId(), $this->getTeamId());
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
                                                                       ->getResponse();
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

    private function validatePostLike(int $postId)
    {
        $error = $this->allowMethod('POST');

        if (!empty($error)) {
            return $error;
        }

        try {
            PostRequestValidator::createPostLikeValidator()->validate(['post_id' => $postId]);
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
                                                                       ->getResponse();
        }

        return null;
    }

    private function validateDeleteLike(int $postId)
    {
        $error = $this->allowMethod('DELETE');

        if (!empty($error)) {
            return $error;
        }

        try {
            PostRequestValidator::createPostLikeValidator()->validate(['post_id' => $postId]);
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
                                                                       ->getResponse();
        }

        return null;
    }
}