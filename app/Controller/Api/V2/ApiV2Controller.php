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
class ApiV2Controller extends Controller
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
     * This list for excluding from prohibited request
     * If only controller name is specified, including all actions
     * If you would like to specify several action, refer to the following:
     * [
     * 'controller' => 'users', 'action'     => 'settings',
     * ],
     * [
     * 'controller' => 'users', 'action'     => 'view_goals',
     * ],
     *
     * @var array
     */
    private $_excludedRequestArray = [
        [
            'controller' => 'payments',
        ],
        [
            'controller' => 'teams',
        ],
        [
            'controller' => 'users',
            'action'     => 'logout',
        ],
        [
            'controller' => 'users',
            'action'     => 'accept_invite',
        ],
        [
            'controller' => 'users',
            'action'     => 'settings',
        ],
        [
            'controller' => 'terms',
        ],
        [
            'controller' => 'pages',
            'action'     => 'display',
            'pagename'   => 'privacy_policy',
        ],
        [
            'controller' => 'pages',
            'action'     => 'display',
            'pagename'   => 'terms',
        ],
    ];

    public function __construct(CakeRequest $request = null, CakeResponse $response = null)
    {
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

    public function beforeFilter()
    {
        parent::beforeFilter();

        if (empty($this->_jwtToken) || !$this->_authenticateUser()) {
            return (new ApiResponse(ApiResponse::RESPONSE_UNAUTHORIZED))
                ->setMessage(__('You should be logged in.'))->getResponse();
        }
        $this->_initializeTeamStatus();

        if ($this->_isRestrictedFromUsingService()) {
            $this->_stopInvokeFlag = true;
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))
                ->setMessage(__("You cannot use service on the team."))->getResponse();
        }
        if ($this->_isRestrictedToReadOnly()) {
            $this->_stopInvokeFlag = true;
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))
                ->setMessage(__("You may only read your team’s pages."))->getResponse();
        }

        $this->_setAppLanguage();
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
        if ($this->_isRequestExcludedFromRestriction()) {
            return false;
        }

        return $this->_teamStatus->getServiceUseStatus() == Team::SERVICE_USE_STATUS_CANNOT_USE;
    }

    /**
     * Check whether current request is excluded from any restriction
     *
     * @return bool True if request is in list of requests ignoring restriction
     */
    private function _isRequestExcludedFromRestriction(): bool
    {
        foreach ($this->_excludedRequestArray as $ignoreParam) {
            // filter requested param with $ignoreParam
            $intersectedParams = array_intersect_key($this->request->params, $ignoreParam);
            if ($intersectedParams == $ignoreParam) {
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
        if ($this->_isRequestExcludedFromRestriction()) {
            return false;
        }
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
}