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
     * Logout endpoint for user. Ignore restriction
     *
     * @ignoreRestriction
     */
    public function post_logout()
    {
        switch ($this->getApiVersion()) {
            case 2:
                return $this->post_logout_v2();
                break;
            default:
                return $this->post_logout_v2();
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


        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init("AuthService");


        $requestData = $this->getRequestJsonBody();

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
     * API V2 logout endpoint for user. Ignore restriction
     */
    private function post_logout_v2()
    {

        $return = $this->validateLogout();

        if (!empty($return)) {
            return $return;
        }

        $Auth = new AuthService();

        try {
            $Auth->invalidateUser($this->getUserToken());
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_INTERNAL_SERVER_ERROR))->withMessage($e->getMessage())
                                                                                 ->withExceptionTrace($e->getTrace())
                                                                                 ->getResponse();
        }

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withMessage('User logged out')->getResponse();
    }

    /**
     * Validate all parameters before being manipulated by respective endpoint
     *
     * @return CakeResponse Return a response if validation failed
     */
    private function validateLogin()
    {
        // TODO: Better create method of allowMethodPost, allowMethodGet, ...
        $res = $this->allowMethod('POST');

        if (!empty($res)) {
            return $res;
        }

        $validator = AuthRequestValidator::createLoginValidator();

        try {
            $validator->validate($this->getRequestJsonBody());
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)
                                                                       ->getResponse();
        }
    }

    /**
     * Validate all parameters before passed to endpoint
     *
     * @return CakeResponse|null Return a response if validation failed
     */
    private function validateLogout()
    {
        if (!$this->request->is('post')) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withMessage("Unsupported HTTP method")
                                                                       ->getResponse();
        }

        return null;
    }

}