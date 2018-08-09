<?php
App::uses('BaseApiController', 'Controller/Api');
App::import('Service', 'AuthService');
App::import('Service', 'ImageStorageService');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');
App::uses('User', 'Model');
App::uses('LangUtil', 'Util');

use Goalous\Exception as GlException;

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/30
 * Time: 11:35
 */
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
        $return = $this->validateLogin();

        if (!empty($return)) {
            return $return;
        }

        $requestData = $this->getRequestJsonBody();

        // TODO: do the translation

        try {
            /** @var AuthService $AuthService */
            $AuthService = ClassRegistry::init("AuthService");
            $jwt = $AuthService->authenticateUser($requestData['email'], $requestData['password']);
        } catch (GlException\Auth\AuthMismatchException $e) {
            return ErrorResponse::badRequest()
                                ->withError(new ErrorTypeGlobal(__('password and email did not match')))
                                ->getResponse();
        } catch (\Throwable $e) {
            GoalousLog::emergency('user failed to login', [
                'message' => $e->getMessage(),
            ]);
            return ErrorResponse::internalServerError()
                                ->getResponse();
        }

        $data = $this->_getAuthUserInfo($jwt);

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    /**
     * Get auth user info for Login API response
     *
     * @param JwtAuthentication $jwt
     *
     * @return array
     */
    private function _getAuthUserInfo(JwtAuthentication $jwt): array
    {
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $data = $User->getUserForLoginResponse($jwt->getUserId())->toArray();
        $data['token'] = $jwt->token();
        $data['profile_img_url'] = $ImageStorageService->getImgUrlEachSize($data, 'User');
        $data['cover_img_url'] = $ImageStorageService->getImgUrlEachSize($data, 'User', 'cover_photo');
        $data['language'] = LangUtil::convertISOFrom3to2($data['language']);
        return $data;
    }

    /**
     * Logout endpoint for user. Ignore restriction
     *
     * @ignoreRestriction
     */
    public function post_logout()
    {
        /** @var AuthService $AuthService */
        $AuthService = new AuthService();

        try {
            $res = $AuthService->invalidateUser($this->getJwtAuth());
        } catch (Exception $e) {
            GoalousLog::error('failed to logout', [
                'user.id' => $this->getUserId(),
                'team.id' => $this->getTeamId(),
                'jwt_id'  => $this->getJwtAuth()->getJwtId(),
            ]);
            return ErrorResponse::internalServerError()
                                ->getResponse();
        }

        if (!$res) {
            return ErrorResponse::internalServerError()
                                ->getResponse();
        }

        return ApiResponse::ok()->withMessage(__('Logged out'))->getResponse();
    }

    /**
     * Validate all parameters before being manipulated by respective endpoint
     *
     * @return CakeResponse Return a response if validation failed
     */
    private function validateLogin()
    {
        $requestedBody = $this->getRequestJsonBody();
        $validator = AuthRequestValidator::createLoginValidator();

        // This process is almost same as BaseApiController::generateResponseIfValidationFailed()
        // But not logging $requestedBody because its containing credential value
        try {
            $validator->validate($requestedBody);
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

}
