<?php

use Goalous\Enum\Model\AttachedFile\AttachedModelType;

App::uses('BasePagingController', 'Controller/Api');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Service/Paging', 'NotificationPagingService');
App::import('Service/Paging', 'RecentCircleListPagingService');
App::import('Service/Paging', 'CirclePostUnreadPagingService');
App::import('Service/Paging', 'JoinedCirclePostPagingService');
App::import('Service/Paging', 'UnreadCircleListPagingService');
App::import('Service/Paging', 'FeedPostPagingService');
App::import('Service/Request/Resource', 'UserResourceRequest');
App::import('Service/Request/Resource', 'TeamResourceRequest');
App::import('Service', 'UnreadCirclePostService');
App::import('Service', 'UserService');
App::import('Service', 'NotifyService');
App::import('Service', 'UserSettingsService');
App::import('Service', 'AuthenticationSessionDataService');
App::import('Service', 'GoalService');
App::import('Service', 'TermService');
App::import('Service', 'KeyResultService');
App::import('Service', 'KrProgressService');
App::import('Lib/Paging', 'PagingRequest');

App::import('Model/Dto/UserSettings', 'UserAccount');
App::import('Model/Dto/UserSettings', 'UserProfile');

App::uses('GlRedis', 'Model');
App::uses('TeamMember', 'Model');
App::uses('CircleMember', 'Model');
App::uses('CheckedCircle', 'Model');
App::uses('Term', 'Model');
App::import('Controller/Traits', 'AuthTrait');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsClient');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsKey');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/29
 * Time: 11:47
 * @property NotificationComponent $Notification
 * @property FlashComponent $Flash
 * @property LangComponent  $Lang
 * @property NotificationComponent $Notification
 * @property GlEmailComponent      $GlEmail
 * @property SessionComponent $Session
 * @property GlRedis          $GlRedis
 */
class MeController extends BasePagingController
{
    use AuthTrait;

    public $uses = [
        "GlRedis",
    ];

    public $components = [
        'Session',
        'Flash',
        'Lang',
        'Notification',
        'GlEmail',
    ];

    /**
     * Get list of circles that an user is joined in
     *
     * @param int $userId
     *
     * @return CakeResponse
     */
    public function get_circles()
    {

        $res = $this->validateCircles();

        if (!empty($res)) {
            return $res;
        }
        try {
            $pagingRequest = $this->getPagingParameters();
            $pagingRequest->addCondition(['public_only' => false]);
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        // TODO: stop to get all pinned circles
        // Related issue: GL-7944
        // In fact, infinite loading pinned circles is not working well, I guess other infinite loading latest update circles below it is a cause,
        // that's not better, but we prioritize fixing other critical bugs
        $circleData = $CircleListPagingService->getDataWithPaging(
            $pagingRequest,
            $this->getPagingLimit(1000), // To get all
            $this->getExtensionOptions() ?: $this->getDefaultCircleExtension());

        return ApiResponse::ok()->withBody($circleData)->getResponse();
    }

    /**
     * Get list of notifications for user authorized
     */
    public function get_notifications()
    {
        $error = $this->validateNotifications();

        if (!empty($error)) {
            return $error;
        }

        try {
            $pagingRequest = $this->getPagingParameters();
            $pagingRequest->addCondition(['from_timestamp' => 0]);
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var NotificationPagingService $NotificationPagingService */
        $NotificationPagingService = ClassRegistry::init('NotificationPagingService');

        $notifications = $NotificationPagingService->getDataWithPaging($pagingRequest, $this->getPagingLimit(),
            [NotificationExtender::EXTEND_ALL]);

        return ApiResponse::ok()
                          ->withBody($notifications)->getResponse();
    }

    /**
     * Get unread notifications count
     */
    public function get_new_notification_count()
    {
        $error = $this->validateNotifications();

        if (!empty($error)) {
            return $error;
        }

        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');
        // unread_count doesn't deal as `count` return value of paging service because it is paging `total` count
        $unreadCount = $GlRedis->getCountOfNewNotification($this->getTeamId(), $this->getUserId());

        $data = ['new_notification_count' => $unreadCount];

        return ApiResponse::ok()
                          ->withBody(compact('data'))->getResponse();
    }

    /**
     * Get user detail
     *
     * @ignoreRestriction
     */
    public function get_detail()
    {
        $req = new UserResourceRequest($this->getUserId(), $this->getTeamId(), true);

        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');

        try {
            $data = $UserService->get($req, [MeExtender::EXTEND_ALL]);
        } catch (Exception $e) {
            GoalousLog::error('Failed to get personal data', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $this->getUserId(),
                'team_id' => $this->getTeamId()
            ]);
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        $flashMessage = $this->Notification->readFlash();
        $data['flash'] = [];
        if (!empty($flashMessage)) {
            switch ($flashMessage['params']['type'] ?? 'error') {
                case 'success':
                    $data['flash']['success'] = $flashMessage['message'];
                    break;
                case 'error':
                    $data['flash']['error'] = $flashMessage['message'];
                    break;
            }
        }

        return ApiResponse::ok()->withBody([
            'data' => $data,
        ])->getResponse();
    }

    /**
     * Get timezones
     */
    public function get_timezones()
    {
        $data = AppUtil::getTimezoneList();

        if (empty($data)) {
            GoalousLog::error('Failed to get timezones data');
            return ErrorResponse::internalServerError()->withMessage(__('System error has occurred.'))->getResponse();
        }

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    /**
     * Get available languages
     */
    public function get_languages()
    {
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        try {
            $translationLanguages = $TeamTranslationLanguageService->getAllLanguages($this->getTeamId());
        } catch (Exception $e) {
            GoalousLog::error('Failed to get team translation languages data');
            $translationLanguages = [];
        }

        $data = [
            'languages' => $this->Lang->getAvailLangList(),
            'translation_languages' => $translationLanguages
        ];

        if (empty($data)) {
            GoalousLog::error('Failed to get languages and team translation languages data');
            return ErrorResponse::internalServerError()->withMessage(__('System error has occurred.'))->getResponse();
        }

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    /**
     * Get QR Code
     */
    public function get_qrcode()
    {
        /** @var TwoFAService $TwoFAService */
        $TwoFAService = ClassRegistry::init("TwoFAService");

        if ($google_2fa_secret_key = $TwoFAService->generateSecretKey()) {
            $this->Session->write('2fa_secret_key', $google_2fa_secret_key);
            $url2fa = $TwoFAService->getQRCodeInline(
                SERVICE_NAME,
                $this->Session->read('Auth.User.PrimaryEmail.email'),
                $google_2fa_secret_key
            );
        }

        GoalousLog::error('EMAIL: ', ['email' => $this->Session->read('Auth.User.PrimaryEmail.email')]);

        if (empty($url2fa)) {
            GoalousLog::error('Failed to get QR data for 2fa', ['2fa_secret_key' => $this->Session->read('2fa_secret_key')]);
            return ErrorResponse::internalServerError()->withMessage(__('System error has occurred.'))->getResponse();
        }

        return ApiResponse::ok()->withData(['url' => $url2fa])->getResponse();
    }

    /**
     * Put account data
     */
    public function put_account()
    {
        /** @var UserSettingsService $UserSettingsService */
        $UserSettingsService = ClassRegistry::init("UserSettingsService");
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init("TeamMemberService");
        
        $data = $this->getRequestJsonBody();

        $user = $UserSettingsService->getUserData($this->getUserId());
        $team = $UserSettingsService->getTeamMemberData($this->getUserId(), $this->getTeamId());

        if (empty($user)) {
            GoalousLog::error('Failed to save account user settings data.', ['User' => $this->getUserId()]);
            return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
        }

        $accountInfo = new UserAccount();
        $accountInfo->userId = $this->getUserId();
        $accountInfo->email = $data['User']['email'];
        $accountInfo->defTeamId = $data['User']['default_team_id'];
        $accountInfo->language = $data['User']['language'];
        $accountInfo->timezone = $data['User']['timezone'];
        $accountInfo->updateEmailFlag = $data['User']['update_email_flg'];

        if (!empty($team) && $team['TeamMember']['default_translation_language'] !== $data['TeamMember']['default_translation_language']) {
            $translationLanguage = $data['TeamMember']['default_translation_language'];
            $team['TeamMember']['default_translation_language'] = $translationLanguage;
            $teamSuccess = $TeamMemberService->putTeamData($this->getTeamId(), $team);
            if (!$teamSuccess) {
                GoalousLog::error('Failed to save account user settings data.', ['Request payload', $data]);
                return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
            }
        }

        $userSuccess = $UserSettingsService->updateAccountSettingsData($this->getUserId(), $accountInfo);
        if (!$userSuccess) {
            GoalousLog::error('Failed to save account user settings data.', ['Request payload', $data]);
            return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
        }
        Cache::delete($UserSettingsService->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');

        return ApiResponse::ok()->withData(__('Saved user setting.'))->getResponse();
    }

    /**
     * Put profile data
     */
    public function put_profile()
    {
        /** @var UserSettingsService $UserSettingsService */
        $UserSettingsService = ClassRegistry::init("UserSettingsService");

        $data = $this->getRequestJsonBody();

        $user = $UserSettingsService->getUserData($this->getUserId());
        $teamMember = $UserSettingsService->getTeamMemberData($this->getUserId(), $this->getTeamId());

        if (empty($user) || empty($teamMember)) {
            GoalousLog::error('Failed to save profile user settings data.', ['User' => $this->getUserId()]);
            return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
        }

        $profileInfo = new UserProfile();
        $profileInfo->userId = $this->getUserId();
        $profileInfo->teamId = $this->getTeamId();
        $profileInfo->firstName = $data['User']['first_name'];
        $profileInfo->lastName = $data['User']['last_name'];
        $profileInfo->genderType = $data['User']['gender_type'];
        $profileInfo->birthday = $data['User']['birth_day'];
        $profileInfo->hideBirthdayFlag = $data['User']['hide_year_flg'];
        $profileInfo->homewotn = $data['User']['hometown'];
        $profileInfo->comment = $data['TeamMember']['comment'];

        if (isset($data['User']['photo']) || isset($data['User']['cover_photo'])) {
            $imageSuccess = $UserSettingsService->updateProfileAndCoverPhoto($this->getUserId(), $this->getTeamId(), $data['User']['photo'], $data['User']['cover_photo']);
            if (!$imageSuccess) {
                return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
            }
            $profileInfo->profilePhotoName = $UserSettingsService->getProfilePhotoName();
            $profileInfo->coverPhotoName = $UserSettingsService->getCoverPhotoName();
        }

        $success = $UserSettingsService->updateProfileSettingsData($this->getUserId(), $profileInfo);
        if (!$success) {
            GoalousLog::error('Failed to save profile user settings data.', ['User' => $this->getUserId()]);
            return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
        }
        Cache::delete($UserSettingsService->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');

        return ApiResponse::ok()->withData(__("Saved user setting."))->getResponse();
    }

    /**
     * Put Notifications data
     */
    public function put_notifications()
    {
        /** @var UserSettingsService $UserSettingsService */
        $UserSettingsService = ClassRegistry::init("UserSettingsService");

        $data = $this->getRequestJsonBody();

        $notifyInfo = new UserNotify();
        $notifyInfo->userId = $this->getUserId();
        $notifyInfo->id = $data['NotifySetting']['id'];
        $notifyInfo->emailStatus = $data['NotifySetting']['email_status'];
        $notifyInfo->mobileStatus = $data['NotifySetting']['mobile_status'];
        $notifyInfo->desktopStatus = $data['NotifySetting']['desktop_status'];

        $success = $UserSettingsService->updateNotifySettingsData($this->getUserId(), $notifyInfo);
        if (!$success) {
            GoalousLog::error('Failed to save notifications user settings data.', ['User' => $this->getUserId()]);
            return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
        }
        Cache::delete($UserSettingsService->getCacheKey(CACHE_KEY_MY_NOTIFY_SETTING, true, null, false), 'user_data');

        return ApiResponse::ok()->withData(__("Saved user setting."))->getResponse();
    }

    /**
     * Put change email
     */
    public function put_change_email()
    {
        /** @var UserSettingsService $UserSettingsService */
        $UserSettingsService = ClassRegistry::init("UserSettingsService");

        $data = $this->getRequestJsonBody();

        $emailInfo = new UserChangeEmail();
        $emailInfo->userId = $this->getUserId();
        $emailInfo->email = $data['User']['email'];
        $emailInfo->password = $data['User']['password_request2'];

        try {
            if (!$UserSettingsService->validatePassword($data)) {
                GoalousLog::error('Password validation failed.', ['data' => $data]);
                return ErrorResponse::internalServerError()->withMessage(__('Invalid Data'))->getResponse();
            } else {
                $emailData = $UserSettingsService->updateEmailAddress($emailInfo);
            }
        } catch (RuntimeException $e) {
            GoalousLog::error('Failed to change email address.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $this->getUserId(),
                'team_id' => $this->getTeamId()
            ]);
            return ErrorResponse::internalServerError()->withMessage(__('System error has occurred.'))->getResponse();
        }

        $this->GlEmail->sendMailChangeEmailVerify(
            $this->getUserId(),
            $emailData['Email']['email'],
            $emailData['Email']['email_token']
        );

        return ApiResponse::ok()->withData(__('Confirmation has been sent to your email address.'))->getResponse();
    }

    /**
     * Put change password
     */
    public function put_change_password()
    {
        /** @var UserSettingsService $UserSettingsService */
        $UserSettingsService = ClassRegistry::init("UserSettingsService");
        
        $data = $this->getRequestJsonBody();

        $passInfo = new UserChangePassword();
        $passInfo->userId = $this->getUserId();
        $passInfo->oldPassword = $data['User']['old_password'];
        $passInfo->password = $data['User']['password'];
        $passInfo->confirmPassword = $data['User']['password_confirm'];

        try {
            if (!$UserSettingsService->validatePassword($data)) {
                GoalousLog::error('Password validation failed.', ['User' => $data]);
                return ErrorResponse::badRequest()->withMessage(__('Failed to save password change.'))->getResponse();
            } else {
                $UserSettingsService->updatePassword($passInfo);
            }
        } catch (RuntimeException $e) {
            GoalousLog::error('Failed to change password.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $this->getUserId(),
                'team_id' => $this->getTeamId()
            ]);
            return ErrorResponse::internalServerError()->withMessage(__('Failed to save password change.'))->getResponse();
        }

        return ApiResponse::ok()->withData(__('Changed password.'))->getResponse();
    }

    /**
     * Post Enable 2FA
     */
    public function post_enable_2fa()
    {
        /** @var TwoFAService $TwoFAService */
        $TwoFAService = ClassRegistry::init("TwoFAService");
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init("UserService");

        $tmp = $this->getRequestJsonBody();

        $data = [
            'User' => [
                '2fa_code' => $tmp['code']
            ]
        ];

        try {
            if (!$secret_key = $this->Session->read('2fa_secret_key')) {
                GoalousLog::error('Enable 2fa failed.', ['Session' => $this->Session->read('2fa_secret_key')]);
                return ErrorResponse::badRequest()->withMessage(__("An error has occurred."))->getResponse();
            }
            if (!$TwoFAService->verifyKey($secret_key, $data['User']['2fa_code'])) {
                GoalousLog::error('Enable 2fa failed.', ['2fa code' => $data['User']['2fa_code']]);
                return ErrorResponse::internalServerError()->withMessage(__("The code is incorrect."))->getResponse();
            }

            if (!$UserService->saveField($this->getUserId(), '2fa_secret', $secret_key)) {
                GoalousLog::error('Enable 2fa failed.', ['2fa_secret' => $secret_key]);
                return ErrorResponse::badRequest()->withMessage(__("An error has occurred."))->getResponse();
            }
        } catch (RuntimeException $e) {
            GoalousLog::error('Enable 2fa failed.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $this->getUserId(),
                'team_id' => $this->getTeamId()
            ]);
            return ErrorResponse::internalServerError()->withMessage($e->getMessage())->getResponse();
        }

        $this->Session->delete('2fa_secret_key');

        return ApiResponse::ok()->withData(__('Succeeded to save 2-Step Verification.'))->getResponse();
    }

    /**
     * Post Disable 2FA
     */
    function post_disable_2fa()
    {
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init("UserService");

        if ($UserService->saveField($this->getUserId(), '2fa_secret', null)) {
            $UserService->invalidateTwoFa($this->getUserId());
        } else {
            GoalousLog::error('Failed to disable 2fa.', ['User' => $this->getUserId()]);
            return ErrorResponse::badRequest()->withMessage(__("An error has occurred."))->getResponse();
        }

        if (!empty($this->getTeamId()) && !empty($this->getUserId())) {
            $this->GlRedis->deleteDeviceHash($this->getTeamId(), $this->getUserId());
        }

        return ApiResponse::ok()->withData(__("Succeeded to cancel 2-Step Verification."))->getResponse();
    }

    /**
     * Get recovery Code
     */
    function get_recovery_code()
    {
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init("UserService");

        try {
            if (!$UserService->generateRecoveryCodes($this->getUserId())) {
                GoalousLog::error('Failed to generate recovery codes.', ['User' => $this->getUserId()]);
                return ErrorResponse::badRequest()->withMessage(__("An error has occurred."))->getResponse();
            } else {
                $recoveryCodes = $UserService->getRecoveryCodes($this->getUserId());
            }
        } catch (Exception $e) {
            GoalousLog::error('Failed to generate recovery codes.', ['User' => $this->getUserId()]);
            return ErrorResponse::badRequest()->withMessage(__("An error has occurred."))->getResponse();
        }


        return ApiResponse::ok()->withData($recoveryCodes)->getResponse();
    }

    /**
     * Get regenerated Code
     */
    function get_regenerated_code()
    {
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init("UserService");

        if (!$UserService->generateRecoveryCodes($this->getUserId())) {
            GoalousLog::error('Failed to regenerate recovery codes.');
            return ErrorResponse::internalServerError()->withMessage(__("An error has occurred."))->getResponse();
        }
        $recoveryCodes = $UserService->getRecoveryCodes($this->getUserId());

        return ApiResponse::ok()->withData($recoveryCodes)->getResponse();
    }

    public function get_goal_status()
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        $canActionGoals = !empty($GoalService->findActionables($this->getUserId()));
        if ($canActionGoals) {
            return ApiResponse::ok()
                ->withBody([
                    'data' => [
                        'can_action' => true,
                    ]
                ])->getResponse();
        }

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        /** @var AuthenticationSessionDataService $AuthenticationSessionDataService */
        $AuthenticationSessionDataService = ClassRegistry::init("AuthenticationSessionDataService");
        $sessionData = $AuthenticationSessionDataService->read($this->getUserId(), $this->getTeamId(), $this->getJwtAuth()->getJwtId());
        if (empty($sessionData)) {
            $sessionData = new AccessTokenData();
        }

        $countCurrentTermGoalUnachieved = $Goal->countSearch([
            'term'     => 'present',
            'progress' => 'unachieved',
        ], $this->getTeamId());
        return ApiResponse::ok()
            ->withBody([
                'data' => [
                    'can_action' => $canActionGoals,
                    'show_goal_create_guidance' => !$sessionData->isHideGoalCreateGuidance(),
                    'count_current_term_goals' => $countCurrentTermGoalUnachieved
                ]
            ])->getResponse();
    }

    public function get_kr_actionable()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var GoalExtension $UserExtension */
        $GoalExtension = ClassRegistry::init('GoalExtension');

        // Find KeyResult ordered by actioned in recent
        $findKrsRequest = new FindForKeyResultListRequest( 
            $this->getUserId(), 
            $this->getTeamId(),
            ['onlyIncomplete' => true]
        );
        $findKrsRequest =FindForKeyResultListRequest::initializePeriod($findKrsRequest);
        $keyResults = $KeyResultService->findForKeyResultList($findKrsRequest);

        foreach ($keyResults as $index => $keyResult) {
            $keyResults[$index]['KeyResult'] = $GoalExtension->extend($keyResults[$index]['KeyResult'], 'goal_id');
        }
        return ApiResponse::ok()
            ->withBody([
                'data' => [
                    'krs' => Hash::extract($keyResults, '{n}.KeyResult'),
                ],
            ])->getResponse();
    }

    public function get_latest_actioned_kr()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        $result = $KeyResultService->getLatestActionedKrIdByUser($this->getUserId());

        $keyResults[0]['KeyResult'] = $result['KeyResult'];
        return ApiResponse::ok()
            ->withBody([
                'data' => [
                    'krs' => Hash::extract($keyResults, '{n}.KeyResult'),
                ],
            ])->getResponse();
    }

    public function get_kr_progress()
    {
        /** @var KrProgressService */
        $KrProgressService = ClassRegistry::init('KrProgressService');

        $opts = [
            'goalId' => $this->request->query('goal_id'),
            'limit' => intval($this->request->query('limit'))
        ];

        $findKrsRequest = new FindForKeyResultListRequest(
            $this->getUserId(),
            $this->getTeamId(),
            $opts
        );
        $findKrsRequest =FindForKeyResultListRequest::initializePeriod($findKrsRequest);
        $response = $KrProgressService->getWithGraph($findKrsRequest);

        return ApiResponse::ok()->withBody($response)->getResponse();
    }

    /**
     * @return ApiResponse|BaseApiResponse
     */
    public function put_hide_goal_create_guidance()
    {
        /** @var AuthenticationSessionDataService $AuthenticationSessionDataService */
        $AuthenticationSessionDataService = ClassRegistry::init("AuthenticationSessionDataService");
        $sessionData = $AuthenticationSessionDataService->read($this->getUserId(), $this->getTeamId(), $this->getJwtAuth()->getJwtId());
        $sessionData->withHideGoalCreateGuidance(true);
        $AuthenticationSessionDataService->write($this->getUserId(), $this->getTeamId(), $this->getJwtAuth()->getJwtId(), $sessionData);

        return ApiResponse::ok()->withBody([])->getResponse();
    }

    /**
     * Get unread posts summary for this user in this team
     */
    public function get_all_unread_posts()
    {
        /** @var UnreadCirclePostService $UnreadCirclePostService */
        $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');
        $data = $UnreadCirclePostService->getGrouped($this->getTeamId(), $this->getUserId());

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    public function get_recent_circles()
    {
        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var RecentCircleListPagingService $RecentCircleListPagingService */
        $RecentCircleListPagingService = ClassRegistry::init('RecentCircleListPagingService');

        $data = $RecentCircleListPagingService->getDataWithPaging(
            $pagingRequest,
            $this->getPagingLimit(15),
            [CircleExtender::EXTEND_MEMBER_INFO]);

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    public function get_unread_posts()
    {
        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var CirclePostUnreadPagingService $CirclePostUnreadPagingService */
        $CirclePostUnreadPagingService = ClassRegistry::init('CirclePostUnreadPagingService');
        $data = $CirclePostUnreadPagingService->getDataWithPaging($pagingRequest);

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    public function get_posts()
    {
        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var JoinedCirclePostPagingService $JoinedCirclePostPagingService */
        $JoinedCirclePostPagingService = ClassRegistry::init('JoinedCirclePostPagingService');
        $data = $JoinedCirclePostPagingService->getDataWithPaging($pagingRequest, 15, [CirclePostExtender::EXTEND_ALL]);

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    /**
     * Delete all unread posts summary for this user in this team
     */
    public function delete_all_unread_posts()
    {
        /** @var UnreadCirclePostService $UnreadCirclePostService */
        $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');

        $UnreadCirclePostService->deleteUserCacheInTeam($this->getTeamId(), $this->getUserId());

        return ApiResponse::ok()->getResponse();
    }

    /**
     * Get action & goal feed for homepage
     */
    public function get_feed()
    {
        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var FeedPostPagingService $FeedPostPagingService */
        $FeedPostPagingService = ClassRegistry::init('FeedPostPagingService');

        try {
            $data = $FeedPostPagingService->getDataWithPaging(
                $pagingRequest,
                $this->getPagingLimit(),
                [FeedPostExtender::EXTEND_ALL]
            );
        } catch (Exception $e) {
            GoalousLog::error(
                "Failed to fetch feed",
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ]
            );
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    /**
     * Validate parameters for getting notifications
     *
     * @return ErrorResponse | null
     */
    private function validateNotifications()
    {
        $fromTimestamp = $this->request->query('from_timestamp');

        if (!empty($fromTimestamp) && !AppUtil::isInt($fromTimestamp)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        return null;
    }

    /**
     * Parameter validation for circles()
     *
     * @return CakeResponse | null
     */
    private function validateCircles()
    {
        return null;
    }

    /**
     * Default extension option for getting circle list
     *
     * @return array
     */
    private function getDefaultCircleExtension()
    {
        return [
            CircleExtender::EXTEND_ALL
        ];
    }

    /**
     * Switch team
     *
     * @ignoreRestriction
     * @return ApiResponse|BaseApiResponse
     */
    public function put_switch_team()
    {
        $teamId = Hash::get($this->getRequestJsonBody(), 'team_id');
        $userId = $this->getUserId();
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        if (!empty($TeamMember->getSsoEnabledTeams($userId))) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have access right to this team."))
                ->getResponse();
        }

        // Check permission whether access team
        $myTeams = $TeamMember->getActiveTeamList($userId);
        if (!array_key_exists($teamId, $myTeams)) {
            return ErrorResponse::forbidden()->withMessage(__("You don't have access right to this team."))
                                ->getResponse();
        }

        try {
            $jwt = $this->resetAuth($userId, $teamId, $this->getJwtAuth());
        } catch (Exception $e) {
            GoalousLog::error('failed to switch team', [
                'message'        => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
                'user_id'        => $userId,
                'switch_team_id' => $teamId,
            ]);
            return ErrorResponse::internalServerError()
                                ->getResponse();
        }

        $data = [
            'token' => $jwt->token(),
        ];

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    /**
     * Get checked circle ids
     *
     * @return ApiResponse|BaseApiResponse
     */
    public function get_checkedCircleIds()
    {
        $userId = $this->getUserId();
        $teamId = $this->getTeamId();

        /** @var CheckedCircle $CheckedCircle */
        $CheckedCircle = ClassRegistry::init('CheckedCircle');

        try {
            $CheckedCircleIds = $CheckedCircle->getCheckedCircleIds($userId, $teamId);
        }
        catch (Exception $e) {
            GoalousLog::error("Faild to get checked circle ids.", [
                'message'   => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
                'team_id'   => $teamId,
                'circle_id' => $circleId
            ]);
            throw $e;
        }

        return ApiResponse::ok()->withData($CheckedCircleIds)->getResponse();
    }
}
