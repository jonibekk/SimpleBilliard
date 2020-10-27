<?php

App::uses('BaseApiController', 'Controller/Api');
App::import('Service', 'AuthService');
App::import('Service', 'ImageStorageService');
App::import('Service', 'InvitationService');
App::import('Service/Request/Resource', 'UserResourceRequest');
App::import('Service', 'UserService');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');
App::uses('GlRedis', 'Model');
App::uses('User', 'Model');
App::uses('LangUtil', 'Util');
App::uses('GlRedis', 'Model');

use Goalous\Exception as GlException;
use Goalous\Enum as Enum;

/**
 * @property AuthComponent    Auth
 * @property SessionComponent Session
 *
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

        $this->Auth->allow();
    }

    public $components = [
        'Auth',
        'Session',
        'NotifyBiz',
    ];

    /**
     * Endpoint for checking for session
     *
     * @ignoreRestriction
     * @skipAuthentication
     */
    public function get_has_session()
    {
        return ApiResponse::ok()
            ->withMessage(Enum\Auth\Status::OK)
            ->withData(['has_session' => !!$this->Auth->user()])
            ->getResponse();
    }

    /**
     * Endpoint for step 1 of login. Returns login method information for the user
     *
     * @ignoreRestriction
     * @skipAuthentication
     */
    public function post_request_login()
    {
        $return = $this->validateRequestLogin();

        if (!empty($return)) {
            return $return;
        }

        $requestData = $this->getRequestJsonBody();

        try {
            /** @var AuthService $AuthService */
            $AuthService = ClassRegistry::init('AuthService');
            $loginRequestData = $AuthService->createLoginRequest($requestData['email']);
        } catch (GlException\Auth\AuthUserNotFoundException $e) {
            return ErrorResponse::badRequest()->withMessage(Enum\Auth\Status::AUTH_MISMATCH)->getResponse();
        } catch (GlException\Auth\AuthFailedException $e) {
            return ErrorResponse::badRequest()->withMessage(Enum\Auth\Status::AUTH_ERROR)->getResponse();
        } catch (\Throwable $e) {
            GoalousLog::emergency(
                'user failed to request login',
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ]
            );
            return ErrorResponse::internalServerError()
                ->getResponse();
        }

        return ApiResponse::ok()->withMessage(Enum\Auth\Status::OK)->withData($loginRequestData)->getResponse();
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

        try {
            /** @var AuthService $AuthService */
            $AuthService = ClassRegistry::init("AuthService");
            $response = $AuthService->authenticateWithPassword($requestData['email'], $requestData['password']);
            if ($response['message'] === Enum\Auth\Status::OK) {
                $response = $this->processInvite($response);
                $response = $this->appendCookieInfo($response);
            }
        } catch (GlException\GoalousNotFoundException $e) {
            return ErrorResponse::badRequest()
                ->withMessage(Enum\Auth\Status::USER_NOT_FOUND)
                ->getResponse();
        } catch (GlException\Auth\AuthMismatchException $e) {
            return ErrorResponse::badRequest()
                ->withMessage(Enum\Auth\Status::AUTH_MISMATCH)
                ->getResponse();
        } catch (\Throwable $e) {
            GoalousLog::emergency(
                'user failed to login',
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ]
            );
            return ErrorResponse::internalServerError()
                ->getResponse();
        }

        return ApiResponse::ok()->withBody($response)->getResponse();
    }

    /**
     * Login endpoint for user using 2fa. Ignore restriction and authentication
     *
     * @ignoreRestriction
     * @skipAuthentication
     */
    public function post_login_2fa()
    {
        $return = $this->validate2FALogin();

        if (!empty($return)) {
            return $return;
        }

        $requestData = $this->getRequestJsonBody();

        try {
            /** @var AuthService $AuthService */
            $AuthService = ClassRegistry::init("AuthService");
            $response = $AuthService->authenticateWith2FA($requestData['auth_hash'], $requestData['2fa_token']);
            $response = $this->processInvite($response);
            $response = $this->appendCookieInfo($response);
        } catch (GlException\GoalousNotFoundException $e) {
            return ErrorResponse::badRequest()
                ->withMessage(Enum\Auth\Status::USER_NOT_FOUND)
                ->getResponse();
        } catch (GlException\Auth\Auth2FAMismatchException $e) {
            return ErrorResponse::badRequest()
                ->withMessage(Enum\Auth\Status::AUTH_MISMATCH)
                ->getResponse();
        } catch (\Throwable $e) {
            GoalousLog::emergency(
                'user failed to login',
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ]
            );
            return ErrorResponse::internalServerError()
                ->getResponse();
        }

        return ApiResponse::ok()->withBody($response)->getResponse();
    }

    /**
     * Login endpoint for user using 2fa recovery codes. Ignore restriction and authentication
     *
     * @ignoreRestriction
     * @skipAuthentication
     */
    public function post_login_2fa_backup()
    {
        $return = $this->validate2FALogin();

        if (!empty($return)) {
            return $return;
        }

        $requestData = $this->getRequestJsonBody();

        try {
            /** @var AuthService $AuthService */
            $AuthService = ClassRegistry::init("AuthService");
            $response = $AuthService->authenticateWith2FA(
                $requestData['auth_hash'],
                $requestData['2fa_token'],
                true
            );
            $response = $this->processInvite($response);
            $response = $this->appendCookieInfo($response);
        } catch (GlException\GoalousNotFoundException $e) {
            return ErrorResponse::badRequest()
                ->withMessage(Enum\Auth\Status::USER_NOT_FOUND)
                ->getResponse();
        } catch (GlException\Auth\Auth2FAInvalidBackupCodeException $e) {
            return ErrorResponse::badRequest()
                ->withMessage(Enum\Auth\Status::AUTH_MISMATCH)
                ->getResponse();
        } catch (\Throwable $e) {
            GoalousLog::emergency(
                'user failed to login',
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ]
            );
            return ErrorResponse::internalServerError()
                ->getResponse();
        }

        return ApiResponse::ok()->withBody($response)->getResponse();
    }

    public function post_login_sso()
    {
        //TODO
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
            GoalousLog::error(
                'failed to logout',
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'user.id' => $this->getUserId(),
                    'team.id' => $this->getTeamId(),
                    'jwt_id'  => $this->getJwtAuth()->getJwtId(),
                ]
            );
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
     * Validate parameters
     *
     * @return CakeResponse
     */
    private function validateRequestLogin()
    {
        $requestedBody = $this->getRequestJsonBody();
        $validator = AuthRequestValidator::createRequestLoginValidator();

        // This process is almost same as BaseApiController::generateResponseIfValidationFailed()
        // But not logging $requestedBody because its containing credential value
        try {
            $validator->validate($requestedBody);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                ->addErrorsFromValidationException($e)
                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error(
                'Unexpected validation exception',
                [
                    'class'   => get_class($e),
                    'message' => $e,
                ]
            );
            return ErrorResponse::internalServerError()->getResponse();
        }

        return null;
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
            GoalousLog::error(
                'Unexpected validation exception',
                [
                    'class'   => get_class($e),
                    'message' => $e,
                ]
            );
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
        $token = $this->getTokenForRecovery($user, $teamId);

        /** @var AuthService $AuthService */
        $AuthService = new AuthService();

        $data = [
            'me'    => $AuthService->getUserInfo($user['id'], $teamId),
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
        try {
            $jwtAuth = AccessAuthenticator::verify($token);
            if (empty($jwtAuth->getUserId()) || empty ($jwtAuth->getTeamId())) {
                throw new GlException\Auth\AuthFailedException('Jwt data is incorrect');
            }
        } catch (Exception $e) {
            GoalousLog::error("ERROR " . $e->getMessage(), $e->getTrace());
            // Regenerate token
            $jwt = $GlRedis->saveMapSesAndJwt($teamId, $user['id'], $sesId);
            $token = $jwt->token();
            return $token;
        }

        return $token;
    }

    private function validate2FALogin()
    {
        $requestedBody = $this->getRequestJsonBody();
        $validator = AuthRequestValidator::create2FALoginValidator();

        // This process is almost same as BaseApiController::generateResponseIfValidationFailed()
        // But not logging $requestedBody because its containing credential value
        try {
            $validator->validate($requestedBody);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                ->addErrorsFromValidationException($e)
                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error(
                'Unexpected validation exception',
                [
                    'class'   => get_class($e),
                    'message' => $e,
                ]
            );
            return ErrorResponse::internalServerError()->getResponse();
        }

        return null;
    }

    private function processInvite(array $responseData): array
    {
        $newResponseData = $responseData;

        // If it's not invitation, return
        if (!key_exists('invitation_token', $this->getRequestJsonBody())) {
            return $newResponseData;
        }

        $userId = $newResponseData['data']['me']['id'];
        $invitationToken = $this->getRequestJsonBody()['invitation_token'];

        try {
            /** @var InvitationService $InvitationService */
            $InvitationService = ClassRegistry::init('InvitationService');
            $message = $InvitationService->validateToken($userId, $invitationToken);

            // If token is invalid, return with message
            if ($message !== Enum\Invite\ResponseMessage::SUCCESS) {
                $newResponseData['message'] = $message;
                return $newResponseData;
            }

            $newTeamId = $InvitationService->consumeToken($userId, $invitationToken);

            if (empty($newTeamId) || $newTeamId < 0) {
                $newResponseData['message'] = Enum\Invite\ResponseMessage::FAILED;
                return $newResponseData;
            }

            $oldJwt = JwtAuthentication::decode($newResponseData['data']['token']);

            /** @var AuthService $AuthService */
            $AuthService = ClassRegistry::init('AuthService');
            $newResponseData['data']['me'] = $AuthService->getUserInfo($userId, $newTeamId);
            $newResponseData['data']['token'] = $AuthService->recreateJwt($oldJwt, $newTeamId)->token();

            $newResponseData['message'] = Enum\Invite\ResponseMessage::SUCCESS;

            /** @var NotifyBizComponent $NotifyBiz */
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_USER_JOINED_TO_INVITED_TEAM, $newTeamId);
        } catch (Exception $e) {
            GoalousLog::error("Failed to process invitation",
                [
                    'class'   => get_class($e),
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'user.id' => $this->getUserId(),
                ]
            );
            $responseData['message'] = Enum\Invite\ResponseMessage::FAILED;
            return $responseData;
        }

        return $newResponseData;
    }

    /**
     * @param array $responseData
     *
     * @return array
     */
    private function appendCookieInfo(array $responseData): array
    {
        if (!array_key_exists('me', $responseData['data'])) {
            return $responseData;
        }
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');
        /** @var User $User */
        $User = ClassRegistry::init('User');

        //Filter out extended data
        $userData = array_filter(
            $responseData['data']['me'],
            function ($input) use ($User) {
                return in_array($input, $User->loginUserFields);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (!$this->Auth->login($userData)) {
            throw new GlException\Auth\AuthFailedException("Unable to manually log user.");
        }

        $userId = $responseData['data']['me']['id'];
        $currentTeamId = $responseData['data']['me']['current_team_id'];

        $sessionId = $this->Session->id();

        $this->Session->write('current_team_id', $currentTeamId);
        $this->Session->write('default_team_id ', $responseData['data']['me']['default_team_id']);

        $cookieLifetime = Configure::read('Session')["ini"]["session.cookie_lifetime"];
        $cookieExpiryTime = GoalousDateTime::now()->addSeconds($cookieLifetime);

        $cookieData = [
            'name'       => getSid(),
            'session_id' => $sessionId,
            'max_age'    => $cookieLifetime,
            'expires'    => $cookieExpiryTime->format("D, d-M-Y H:i:s e")
        ];

        $responseData['data']['cookie'] = $cookieData;

        $GlRedis->saveMapSesAndJwtWithToken($currentTeamId, $userId, $responseData['data']['token'], $sessionId);

        return $responseData;
    }
}
