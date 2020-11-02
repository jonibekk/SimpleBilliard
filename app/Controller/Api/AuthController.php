<?php
App::uses('BaseApiController', 'Controller/Api');
App::import('Lib/DataExtender', 'MeExtender');
App::import('Service', 'AuthService');
App::import('Service', 'ImageStorageService');
App::import('Service/Request/Resource', 'UserResourceRequest');
App::import('Service', 'UserService');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');
App::uses('User', 'Model');
App::uses('LangUtil', 'Util');
App::uses('GlRedis', 'Model');

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
                ->withMessage(__('Email address or Password is incorrect.'))
                ->withError(new ErrorTypeGlobal(__('Email address or Password is incorrect.')))
                ->getResponse();
        } catch (\Throwable $e) {
            GoalousLog::emergency('user failed to login', [
                'message' => $e->getMessage(),
            ]);
            return ErrorResponse::internalServerError()
                ->getResponse();
        }

        $data = [
            'me'    => $this->_getAuthUserInfo($jwt->getUserId(), $jwt->getTeamId()),
            'token' => $jwt->token()
        ];

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    /**
     * Get auth user info for Login API response
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return array
     */
    private function _getAuthUserInfo(int $userId, int $teamId): array
    {
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');
        $req = new UserResourceRequest($userId, $teamId, true);
        return $UserService->get($req, [MeExtender::EXTEND_ALL]);
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
     * Get jwt from session id to enable to access new Goalous from old Goalous
     *
     * @ignoreRestriction
     * @skipAuthentication
     */
    public function get_recover_token()
    {
        $user = $this->Session->read('Auth.User');
        $teamId = $this->Session->read('current_team_id');
        if (empty($user) || empty($teamId)) {
            return ErrorResponse::badRequest()
                ->withMessage(__("Session doesn't exist"))
                ->getResponse();
        }
        $token = $this->getTokenForRecovery($user, $teamId); //

        $data = [
            'me'    => $this->_getAuthUserInfo($user['id'], $teamId),
            'token' => $token
        ];

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    /**
     * Get token from redis integrated session
     * If token is not verified, regenerate token
     *
     * @param array $user
     * @param int   $teamId
     *
     * @return string
     */
    private function getTokenForRecovery(array $user, int $teamId): string
    {
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');

        $sesId = $this->Session->id();
        $token = $GlRedis->getMapSesAndJwt($teamId, $user['id'], $sesId);
        if (strlen($token) > 0) {
            try {
                $jwtAuth = AccessAuthenticator::verify($token); //
                if (empty($jwtAuth->getUserId()) || empty ($jwtAuth->getTeamId())) {
                    throw new GlException\Auth\AuthFailedException('Jwt data is incorrect');
                }
                return $token;
            } catch (Exception $e) {
                GoalousLog::error("ERROR " . $e->getMessage(), $e->getTrace());
            }
        }
        // Regenerate token
        $jwt = $GlRedis->saveMapSesAndJwt($teamId, $user['id'], $sesId);
        $token = $jwt->token();
        return $token;
    }
}
