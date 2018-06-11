<?php
App::uses('BaseApiController', 'Controller/Api');
App::import('Service', 'AuthService');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/30
 * Time: 11:35
 */

use Goalous\Enum\ApiVersion\ApiVersion as ApiVer;

class AuthController extends BaseApiController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * Login endpoint for user. Ignore restriction and authentication
     *
     * @ignoreRestriction
     * @skipAuthentication
     */
    public function post_login()
    {
        switch ($this->getApiVersion()) {
            case ApiVer::VER_2:
                return $this->post_login_v2();
                break;
            default:
                return $this->post_login_v2();
                break;
        }
    }

    /**
     * API v2 login endpoint for user
     *
     * @return CakeResponse
     */
    private function post_login_v2()
    {

        $return = $this->validateLogin();

        if (!empty($return)) {
            return $return;
        }

        $requestData = $this->request->data;

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init("AuthService");

        try {
            $jwt = $AuthService->authenticateUser($requestData['username'], $requestData['password']);
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_INTERNAL_SERVER_ERROR))->withException($e)
                                                                                 ->getResponse();
        }

        //If no matching username / password is found
        if (empty($jwt)) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withMessage(__("Error. Try to login again."))
                                                                       ->getResponse();
        }

        //On successful login, return the JWT token to the user
        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withBody(['jwt' => $jwt->token()])->getResponse();
    }

    /**
     * Validate all parameters before being manipulated by respective endpoint
     *
     * @return CakeResponse Return a response if validation failed
     */
    private function validateLogin()
    {
        $res = $this->allowMethod('post');

        if (!empty($res)) {
            return $res;
        }

        $validator = AuthRequestValidator::createLoginValidator();

        try {
            $validator->validate($this->request->data);
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
                                                                       ->getResponse();
        }
    }

}