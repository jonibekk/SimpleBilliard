<?php
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');
App::uses('LangComponent', 'Controller/Component');
App::uses('ErrorResponse', 'Lib/Network/Response');
App::import('Lib/Status', 'TeamStatus');
App::import('Lib/Auth', 'AccessAuthenticator');

/**
 * Parent controller for API v2
 * Created by PhpStorm.
 * User: raharjas
 * Date: 13/04/2018
 * Time: 16:47
 *
 * @property .\Model\User  $User
 * @property LangComponent $LangComponent
 */

use Goalous\Enum\ApiVersion\ApiVersion as ApiVer;
use Goalous\Exception as GlException;

abstract class BaseApiController extends Controller
{
    /** @var string */
    private $_jwtToken;

    /** @var int */
    private $_currentUserId;

    /** @var int */
    private $_currentTeamId;

    /** @var TeamStatus */
    private $_teamStatus;

    /** @var bool */
    private $_stopInvokeFlag = false;

    /** @var JwtAuthentication */
    private $_jwtAuth;

    /**
     * ApiV2Controller constructor.
     *
     * @param CakeRequest|null  $request
     * @param CakeResponse|null $response
     */
    public function __construct(CakeRequest $request = null, CakeResponse $response = null)
    {
        parent::__construct($request, $response);
        $this->_fetchJwtToken($request);

        $components = new ComponentCollection();
        $this->LangComponent = new LangComponent($components);
        $this->LangComponent->initialize();
    }

    /**
     * Get JWT token from request
     *
     * @param CakeRequest $request
     */
    private function _fetchJwtToken(CakeRequest $request)
    {
        $authHeader = $request->header('Authorization');

        if (empty($authHeader)) {
            return;
        }

        $jwt = sscanf($authHeader, 'Bearer %s');

        $this->_jwtToken = $jwt[0] ?? '';
    }

    /**
     * @return CakeResponse|void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        //Skip authentication if the endpoint set the option
        if (!$this->_checkSkipAuthentication($this->request)) {

            if (empty($this->_jwtToken)) {
                /** @noinspection PhpInconsistentReturnPointsInspection */
                return ErrorResponse::unauthorized()->withMessage(__('Missing token.'))->getResponse();
            }

            try {
                $userAuthentication = $this->_authenticateUser();
            } catch (Exception $e) {
                /** @noinspection PhpInconsistentReturnPointsInspection */
                return ErrorResponse::internalServerError()->withException($e)->getResponse();
            }

            if (!$userAuthentication) {
                /** @noinspection PhpInconsistentReturnPointsInspection */
                return ErrorResponse::unauthorized()->withMessage(__('You should be logged in.'))->getResponse();
            }

            $this->_initializeTeamStatus();

            //Check if user is restricted from using the service. Always skipped if endpoint ignores restriction
            if ($this->_isRestrictedFromUsingService() && !$this->_checkIgnoreRestriction($this->request)) {
                $this->_stopInvokeFlag = true;
                /** @noinspection PhpInconsistentReturnPointsInspection */
                return ErrorResponse::forbidden()->withMessage(__("You cannot use service on the team."))
                                    ->getResponse();
            }
            //Check if user is restricted to read only. Always skipped if endpoint ignores restriction
            if ($this->_isRestrictedToReadOnly() && !$this->_checkIgnoreRestriction($this->request)) {
                $this->_stopInvokeFlag = true;
                /** @noinspection PhpInconsistentReturnPointsInspection */
                return ErrorResponse::forbidden()->withMessage(__("You may only read your team’s pages."))
                                    ->getResponse();
            }
        }

        $this->_setAppLanguage();
    }

    /**
     * Only allow a given request method
     *
     * @param string $method Method name
     *
     * @return CakeResponse
     */
    protected function allowMethod(string $method)
    {
        if ($this->request->method() != $method) {
            return (new ApiResponse(ApiResponse::RESPONSE_UNAUTHORIZED))->getResponse();
        }
    }

    /**
     * Common use of most validation of API access.
     * This method will logging validating value if caught unexpected exception.
     * Do not use this method if validating value is containing credential value.
     *
     * @param BaseValidator $validator
     * @param array $validateValue
     *
     * @return null|BaseApiResponse
     */
    protected function generateResponseIfValidationFailed(BaseValidator $validator, array $validateValue)
    {
        try {
            $validator->validate($validateValue);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                ->addErrorsFromValidationException($e)
                ->withMessage(__('validation failed'))
                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error('Unexpected validation exception', [
                'class' => get_class($e),
                'message' => $e,
                'values' => $validateValue,
            ]);
            return ErrorResponse::internalServerError()->getResponse();
        }

        return null;
    }

    /**
     * Check whether the method skip authentication method
     * To use: @skipAuthentication
     *
     * @param CakeRequest $request
     *
     * @return bool
     */
    private function _checkSkipAuthentication(CakeRequest $request)
    {
        $commentArray = $this->_parseEndpointDocument($request);

        foreach ($commentArray as $commentLine) {
            if ('@skipAuthentication' == trim($commentLine)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Parse endpoint methods' documents, and retrieve special options
     *
     * @param CakeRequest $request
     *
     * @return array
     */
    private function _parseEndpointDocument(CakeRequest $request): array
    {
        $controllerName = $request->params['controller'] ?? '';
        $actionName = $request->params['action'] ?? '';

        if (empty($controllerName) && empty($actionName)) {
            return [];
        }

        $classPath = '';

        $classPath .= ucfirst($controllerName) . "Controller";

        try {
            $class = new ReflectionClass($classPath);

        } catch (ReflectionException $exception) {
            GoalousLog::info("Reflection exception :" . $exception->getMessage());
        }

        if (empty($class)) {
            return [];
        }

        $methodDocument = $class->getMethod($actionName)->getDocComment();

        if (empty($methodDocument)) {
            return [];
        }

        $commentArray = explode('*', substr($methodDocument, 3, -2));

        $resultArray = [];

        foreach ($commentArray as $value) {
            if (!empty($value) && substr(trim($value), 0, 1) == "@") {
                $resultArray[] = trim($value);
            }
        }

        return $resultArray;
    }

    /**
     * Perform user authentication using JWT Token method
     *
     * @return bool TRUE = user successfully authenticated
     */
    private function _authenticateUser(): bool
    {
        try {
            $jwtAuth = AccessAuthenticator::verify($this->_jwtToken);
        } catch (GlException\Auth\AuthFailedException $e) {
            GoalousLog::error("ERROR " . $e->getMessage(), $e->getTrace());
            return false;
        }

        if (empty($jwtAuth->getUserId() || empty ($jwtAuth->getTeamId()))) {
            return false;
        }

        $this->_currentUserId = $jwtAuth->getUserId();

        $this->_currentTeamId = $jwtAuth->getTeamId();

        $this->_jwtAuth = $jwtAuth->getJwtAuthentication();

        return true;
    }

    /**
     * Initialize current team's status based on current user's team ID
     */
    private function _initializeTeamStatus()
    {
        $this->_teamStatus = TeamStatus::getCurrentTeam();
        $this->_teamStatus->initializeByTeamId($this->_currentTeamId);
    }

    /**
     * Check if user is restricted from using service
     *
     * @return bool True if user is restricted from using the service
     */
    private function _isRestrictedFromUsingService(): bool
    {
        return $this->_teamStatus->getServiceUseStatus()->getValue() == Team::SERVICE_USE_STATUS_CANNOT_USE;
    }

    /**
     * Check whether the method ignore service usage restriction
     * To use: @ignoreRestriction
     *
     * @param CakeRequest $request
     *
     * @return bool
     */
    private function _checkIgnoreRestriction(
        CakeRequest $request
    ) {
        $commentArray = $this->_parseEndpointDocument($request);

        foreach ($commentArray as $commentLine) {
            if ('@ignoreRestriction' == trim($commentLine)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether user is restricted to read only
     *
     * @return bool True if user is restricted to read only
     */
    private function _isRestrictedToReadOnly(): bool
    {
        if (!$this->request->is(['post', 'put', 'delete', 'patch'])) {
            return false;
        }
        return $this->_teamStatus->getServiceUseStatus()->getValue() == Team::SERVICE_USE_STATUS_READ_ONLY;
    }

    /**
     * Set the app language for current user
     */
    private function _setAppLanguage()
    {
        /** @var .\Model\User $User */
        $User = ClassRegistry::init('User');
        if (isset($this->_currentUserId)) {
            $currentUser = $User->findById($this->_currentUserId)['User'];
        }
        if (isset($this->_currentUserId) && isset($currentUser['language']) && !boolval($currentUser['auto_language_flg'])) {
            Configure::write('Config.language', $currentUser['language']);
            $this->set('is_not_use_local_name', (new User())->isNotUseLocalName($currentUser['language']) ?? false);
        } else {
            $lang = $this->LangComponent->getLanguage();
            $this->set('is_not_use_local_name', (new User())->isNotUseLocalName($lang) ?? false);
        }
    }

    /** Override parent's method
     *
     * @param CakeRequest $request
     *
     * @return mixed
     */
    public function invokeAction(CakeRequest $request)
    {
        if ($this->_stopInvokeFlag) {
            return false;
        }
        try {
            return parent::invokeAction($request);
        } catch (\Throwable $throwable) {
            GoalousLog::error('Caught an throwable that could not catch anywhere.', [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTraceAsString(),
            ]);
            return ErrorResponse::internalServerError()
                ->withMessage('internal server error')
                ->withException($throwable)
                ->getResponse();
        }
    }

    /**
     * @return string Current user's JWT token
     */
    public function getUserToken()
    {
        return $this->_jwtToken;
    }

    /**
     * @return JwtAuthentication Current user's JWT Auth
     */
    public function getJwtAuth()
    {
        return $this->_jwtAuth;
    }

    /**
     * Get requested API Version. If not given / not valid, will return latest version
     *
     * @return int
     */
    protected function getApiVersion()
    {
        $requestedVersion = (int)$this->request::header('X-API-Version');

        return ApiVer::isAvailable($requestedVersion) ?
            $requestedVersion : ApiVer::getLatestApiVersion();
    }

    /**
     * Check whether JWT Auth has been set
     */
    protected function hasAuth()
    {
        return isset($this->_jwtAuth);
    }

    /**
     * @return int Current user's current team ID
     */
    protected function getTeamId(): int
    {
        return $this->_currentTeamId;
    }

    /**
     * @return int Current user's ID
     */
    protected function getUserId()
    {
        return $this->_currentUserId;
    }

    /**
     * Get requested Json Body value
     *
     * @return array
     */
    protected function getRequestJsonBody(): array
    {
        $body = $this->request->input();
        $decodedJson = json_decode($body, true);
        return is_array($decodedJson) ? $decodedJson : [];
    }
}