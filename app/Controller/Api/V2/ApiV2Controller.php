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
 * @property ApiResponse $ApiResponse
 */
class ApiV2Controller extends Controller
{

    public $currentUser;

    public $teamId;

    private $_jwtToken;

    public function __construct(CakeRequest $request = null, CakeResponse $response = null)
    {
        //TODO get token
        parent::__construct($request, $response);
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
    }

    /**
     * Initialize current team's status based on current user's team ID
     */
    private function _initializeTeamStatus()
    {
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($this->teamId);
    }

    private function _setAppLanguage()
    {


    }

    /**
     * Set HTTP header to disable PHP caching function
     */
    private function _disablePHPCache()
    {
        $this->ApiResponse->addHeaderEntry("Cache-Control: no-store, no-cache, max-age=0");
        $this->ApiResponse->addHeaderEntry("Pragma: no-cache");
    }

    /**
     * Check whether user is an admin in current team
     */
    protected function isAdmin(): bool
    {
        if (empty($this->currentUser['id']) || empty ($this->teamId)) {
            return false;
        }
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        return $TeamMember->isActiveAdmin($this->currentUser['id'], $this->teamId);
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
     * Return HTTP CODE 200: Success
     *
     * @param array|null  $data    Data to be returned to client
     * @param string|null $message Response message
     * @param string|null $exception
     * @param string|null $exceptionTrace
     */
    protected function returnResponseSuccess(
        array $data = [],
        string $message = null,
        string $exception = null,
        string $exceptionTrace = null
    ) {
        $this->ApiResponse->_createResponse(200, $message, $exception, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 400: Bad request
     *
     * @param array|null  $data    Data to be returned to client
     * @param string|null $message Response message
     * @param string|null $exception
     * @param string|null $exceptionTrace
     */
    protected function returnResponseBadRequest(
        array $data = [],
        string $message = null,
        string $exception = null,
        string $exceptionTrace = null
    ) {
        $this->ApiResponse->_createResponse(400, $message, $exception, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 403: Forbidden
     *
     * @param array|null  $data    Data to be returned to client
     * @param string|null $message Response message
     * @param string|null $exception
     * @param string|null $exceptionTrace
     */
    protected function returnResponseForbidden(
        array $data = [],
        string $message = null,
        string $exception = null,
        string $exceptionTrace = null
    ) {
        $this->ApiResponse->_createResponse(403, $message, $exception, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 404: Forbidden
     *
     * @param array|null  $data    Data to be returned to client
     * @param string|null $message Response message
     * @param string|null $exception
     * @param string|null $exceptionTrace
     */
    protected function returnResponseNotFound(
        array $data = [],
        string $message = null,
        string $exception = null,
        string $exceptionTrace = null
    ) {
        $this->ApiResponse->_createResponse(404, $message, $exception, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 409: Forbidden
     *
     * @param array|null  $data    Data to be returned to client
     * @param string|null $message Response message
     * @param string|null $exception
     * @param string|null $exceptionTrace
     */
    protected function returnResponseResourceConflict(
        array $data = [],
        string $message = null,
        string $exception = null,
        string $exceptionTrace = null
    ) {
        $this->ApiResponse->_createResponse(409, $message, $exception, $exceptionTrace);
    }

    /**
     * Return HTTP CODE 500: Forbidden
     *
     * @param array|null  $data    Data to be returned to client
     * @param string|null $message Response message
     * @param string|null $exception
     * @param string|null $exceptionTrace
     */
    protected function returnResponseInternalServerError(
        array $data = [],
        string $message = null,
        string $exception = null,
        string $exceptionTrace = null
    ) {
        $this->ApiResponse->_createResponse(500, $message, $exception, $exceptionTrace);
    }
}