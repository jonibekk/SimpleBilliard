<?php
App::uses('AuthService', 'Service');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/30
 * Time: 11:35
 */
class AuthController extends ApiV2Controller
{
    public function beforeFilter()
    {
        return parent::beforeFilter();
    }

    /**
     * Login endpoint for user. Ignore restriction and authentication
     *
     * @ignoreRestriction
     * @skipAuthentication
     */
    public function post_login()
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

        if (empty($jwt)) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withMessage(__("Error. Try to login again."))
                                                                       ->getResponse();
        }

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