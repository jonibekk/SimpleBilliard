<?php

App::uses('ApiResponse', '/Lib/Network');
App::uses('User', 'Model');
App::uses('TeamMember', 'Model');

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
class ApiV2Controller extends Controller
{
    private $_jwtToken;

    private $_currentUser;

    private $_currentTeamId;

    public function __construct(CakeRequest $request = null, CakeResponse $response = null)
    {
        parent::__construct($request, $response);

        //TODO get token
    }

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_disablePHPCache();

        if ($this->_authenticateUser()) {
            $this->_initializeTeamStatus();
            $this->_setAppLanguage();
        } else {
            return $this->returnResponseBadRequest(__('You should be logged in.'));
        }

        return true;
    }

    /**
     * Set HTTP header to disable PHP caching function
     */
    private function _disablePHPCache()
    {
        $this->setHeader("Cache-Control: no-store, no-cache, max-age=0");
        $this->setHeader("Pragma: no-cache");
    }

    /**
     * Set HTTP header for response
     *
     * @param string $value
     */
    public function setHeader($value)
    {
        header($value);
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
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($this->_currentTeamId);
    }

    private function _setAppLanguage()
    {
        $user = $this->_currentUser;

        if (isset($user) && isset($user['language']) && !boolval($user['auto_language_flg'])) {
            Configure::write('Config.language', $user['language']);
            $this
                ->set('is_not_use_local_name', $this->User->isNotUseLocalName($user['language']));
        } else {
            $lang = $this->LangComponent->getLanguage();
            $this->set('is_not_use_local_name', $this->User->isNotUseLocalName($lang));
        }

    }

    /**
     * Create CakeResponse object to be returned to the client
     *
     * @param integer     $httpStatusCode HTTP status code of the response
     * @param array|null  $data           Data do be returned to the client
     * @param string|null $message        Additional message to be sent
     * @param string|null $exceptionTrace Any trace if an exception occurs
     *
     * @return CakeResponse
     */
    private function _getResponse(
        int $httpStatusCode,
        $data = null,
        string $message = null,
        string $exceptionTrace = null
    ) {
        $ret = [];
        if ($data !== null) {
            $ret['data'] = $data;
        }
        if ($message !== null) {
            $ret['message'] = $message;
        }
        if ($exceptionTrace !== null) {
            $ret['exception_trace'] = $exceptionTrace;
        }
        $this->response->type('json');
        $this->response->body(json_encode($ret));
        $this->response->statusCode($httpStatusCode);

        return $this->response;
    }

    /**
     * Return HTTP CODE 200: Success
     *
     * @param array|null  $data           Data do be returned to the client
     * @param string|null $message        Additional message to be sent
     * @param string|null $exceptionTrace Any trace if an exception occurs
     *
     * @return CakeResponse
     */
    protected function returnResponseSuccess(
        array $data = [],
        string $message = null,
        string $exceptionTrace = null
    ) {
        return $this->_getResponse(200, $data, $message, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 400: Bad request
     *
     * @param array|null  $data           Data do be returned to the client
     * @param string|null $message        Additional message to be sent
     * @param string|null $exceptionTrace Any trace if an exception occurs
     *
     * @return CakeResponse
     */
    protected function returnResponseBadRequest(
        array $data = [],
        string $message = null,
        string $exceptionTrace = null
    ) {
        return $this->_getResponse(400, $data, $message, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 403: Forbidden
     *
     * @param array|null  $data           Data do be returned to the client
     * @param string|null $message        Additional message to be sent
     * @param string|null $exceptionTrace Any trace if an exception occurs
     *
     * @return CakeResponse
     */
    protected function returnResponseForbidden(
        array $data = [],
        string $message = null,
        string $exceptionTrace = null
    ) {
        return $this->_getResponse(403, $data, $message, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 404: Not found
     *
     * @param array|null  $data           Data do be returned to the client
     * @param string|null $message        Additional message to be sent
     * @param string|null $exceptionTrace Any trace if an exception occurs
     *
     * @return CakeResponse
     */
    protected function returnResponseNotFound(
        array $data = [],
        string $message = null,
        string $exceptionTrace = null
    ) {
        return $this->_getResponse(404, $data, $message, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 409: Resource conflict
     *
     * @param array|null  $data           Data do be returned to the client
     * @param string|null $message        Additional message to be sent
     * @param string|null $exceptionTrace Any trace if an exception occurs
     *
     * @return CakeResponse
     */
    protected function returnResponseResourceConflict(
        array $data = [],
        string $message = null,
        string $exceptionTrace = null
    ) {
        return $this->_getResponse(409, $data, $message, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 500: Internal server error
     *
     * @param array|null  $data           Data do be returned to the client
     * @param string|null $message        Additional message to be sent
     * @param string|null $exceptionTrace Any trace if an exception occurs
     *
     * @return CakeResponse
     */
    protected function returnResponseInternalServerError(
        array $data = [],
        string $message = null,
        string $exceptionTrace = null
    ) {
        return $this->_getResponse(500, $data, $message, $exceptionTrace);
    }
}