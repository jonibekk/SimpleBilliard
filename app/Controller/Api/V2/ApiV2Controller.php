<?php

App::uses('ApiResponse', 'Lib/Network');
App::uses('TeamMember', 'Model');
App::uses('TeamStatus', 'Model');
App::uses('User', 'Model');

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
abstract class ApiV2Controller extends Controller
{
    /** @var string */
    private $_jwtToken;

    /** @var .\Model\User */
    private $_currentUser;

    /** @var int */
    private $_currentTeamId;

    /** @var TeamStatus */
    private $_teamStatus;

    /** @var bool */
    private $_stopInvokeFlag = false;

    /**
     * ApiV2Controller constructor.
     *
     * @param CakeRequest|null  $request
     * @param CakeResponse|null $response
     */
    public function __construct(
        CakeRequest $request = null,
        CakeResponse $response = null

    ) {
        parent::__construct($request, $response);
        $this->_fetchJwtToken($request);
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
        list($jwt) = sscanf($authHeader->toString(), 'Authorization: Bearer %s');

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
            if (empty($this->_jwtToken) || !$this->_authenticateUser()) {
                /** @noinspection PhpInconsistentReturnPointsInspection */
                return (new ApiResponse(ApiResponse::RESPONSE_UNAUTHORIZED))
                    ->withMessage(__('You should be logged in.'))->getResponse();
            }
            $this->_initializeTeamStatus();

            //Check if user is restricted from using the service. Always skipped if endpoint ignores restriction
            if ($this->_isRestrictedFromUsingService() && !$this->_checkIgnoreRestriction($this->request)) {
                $this->_stopInvokeFlag = true;
                /** @noinspection PhpInconsistentReturnPointsInspection */
                return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))
                    ->withMessage(__("You cannot use service on the team."))->getResponse();
            }
            //Check if user is restricted to read only. Always skipped if endpoint ignores restriction
            if ($this->_isRestrictedToReadOnly() && !$this->_checkIgnoreRestriction($this->request)) {
                $this->_stopInvokeFlag = true;
                /** @noinspection PhpInconsistentReturnPointsInspection */
                return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))
                    ->withMessage(__("You may only read your team’s pages."))->getResponse();
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
        $apiVersion = $request->params['apiVersion'] ?? '';

        if (empty($controllerName) || empty($actionName)) {
            return [];
        }

        //TODO Make it works with path. Currently only works with classname, which might cause mix up with classes of same name under different folder

        $classPath = '';
//        $classPath = '\\vagrant\\app\\Controller';

//        if (!empty($apiVersion)) {
//            $classPath .= '\\Api\\' . ucfirst($apiVersion) . '\\';
//        }
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
        //TODO set user & team ID
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
        return $this->_teamStatus->getServiceUseStatus() == Team::SERVICE_USE_STATUS_CANNOT_USE;
    }

    /**
     * Check whether the method ignore service usage restriction
     * To use: @ignoreRestriction
     *
     * @param CakeRequest $request
     *
     * @return bool
     */
    private function _checkIgnoreRestriction(CakeRequest $request)
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
     * Check whether user is restricted to read only
     *
     * @return bool True if user is restricted to read only
     */
    private function _isRestrictedToReadOnly(): bool
    {
        if (!$this->request->is(['post', 'put', 'delete', 'patch'])) {
            return false;
        }
        return $this->_teamStatus->getServiceUseStatus() == Team::SERVICE_USE_STATUS_READ_ONLY;
    }

    /**
     * Set the app language for current user
     */
    private function _setAppLanguage()
    {
        if (isset($this->_currentUser) && isset($this->_currentUser['language']) && !boolval($this->_currentUser['auto_language_flg'])) {
            Configure::write('Config.language', $this->_currentUser['language']);
            $this
                ->set('is_not_use_local_name',
                    (new User())->isNotUseLocalName($this->_currentUser['language']) ?? false);
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
    public function invokeAction(
        CakeRequest $request
    ) {
        if ($this->_stopInvokeFlag) {
            return false;
        }
        return parent::invokeAction($request);
    }

    /**
     * @return int Current user's current team ID
     */
    public function getTeamId(): int
    {
        return $this->_currentTeamId;
    }

    /**
     * @return mixed Current user's User object
     */
    public function getUser()
    {
        return $this->_currentUser;
    }
}