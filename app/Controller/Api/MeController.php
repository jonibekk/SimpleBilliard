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
App::import('Service', 'AuthenticationSessionDataService');
App::import('Service', 'GoalService');
App::import('Service', 'KeyResultService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('GlRedis', 'Model');
App::uses('TeamMember', 'Model');
App::uses('CircleMember', 'Model');
App::uses('CheckedCircle', 'Model');
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
 * @property User  $User
 * @property NotificationComponent $Notification
 * @property GlEmailComponent      $GlEmail
 * @property TeamMember  $TeamMember
 * @property TwoFaComponent $TwoFa
 * @property SessionComponent $Session
 * @property GlRedis          $GlRedis
 * @property NotifySetting  $NotifySetting
 */
class MeController extends BasePagingController
{
    use AuthTrait;

    public $uses = [
        'User',
        'TeamMember',
        "GlRedis",
        'NotifySetting',
    ];

    public $components = [
        'Session',
        'Flash',
        'Lang',
        'TwoFa',
        'Notification',
        'GlEmail',
        'Notification',
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

        return ApiResponse::ok()->withBody([
            'data' => $data
        ])->getResponse();
    }

    /**
     * Get available languages
     */
    public function get_languages()
    {
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');
        $translation_languages = $TeamTranslationLanguageService->getAllLanguages($this->getTeamId());

        $data = [
            'languages' => $this->Lang->getAvailLangList(),
            'translation_languages' => $translation_languages
        ];

        if (empty($data)) {
            GoalousLog::error('Failed to get languages and translations data');
            return ErrorResponse::internalServerError()->withMessage(__('System error has occurred.'))->getResponse();
        }
        
        return ApiResponse::ok()->withBody([
            'data' => $data
        ])->getResponse();
    }

    /**
     * Get QR Code
     */
    public function get_qrcode()
    {
        if ($this->Session->read('2fa_secret_key')) {
            $google_2fa_secret_key = $this->Session->read('2fa_secret_key');
        } else {
            $google_2fa_secret_key = $this->TwoFa->generateSecretKey();
            $this->Session->write('2fa_secret_key', $google_2fa_secret_key);
        }

        $url_2fa = $this->TwoFa->getQRCodeInline(
            SERVICE_NAME,
            $this->Session->read('Auth.User.PrimaryEmail.email'),
            $google_2fa_secret_key
        );

        $data = [];
        if ($url_2fa) {
            $data = ['url' => $url_2fa];
        } else {
            return ErrorResponse::internalServerError()->withMessage(__('System error has occurred.'))->getResponse();
        }


        return ApiResponse::ok()->withBody([
            'data' => $data
        ])->getResponse();
    }

    /**
     * Put account data
     */
    public function put_account()
    {
        /** @var User $User */
        $User = ClassRegistry::init("User");
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');
        
        $data = $this->getRequestJsonBody();

        $user_options = [
            'conditions' => ['User.id' => $data['User']['id'],],
        ];
        $team_options = [
            'conditions' => ['Team.id' => $this->getTeamId(),],
        ];

        $user = $User->find('first', $user_options);
        $team = $Team->find('first', $team_options);
        if (!empty($user)) {

            if (!empty($team) && $team['Team']['default_translation_language'] !== $data['TeamMember']['default_translation_language']) {
                $translationLanguage = $data['TeamMember']['default_translation_language'];
                $team['Team']['default_translation_language'] = $translationLanguage;
                $Team->save($team, false);
            }

            $result = $User->saveAll($data['User']);
            if (!$result) {
                return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
            }
        }

        return ApiResponse::ok()->withBody(['data' => __('Saved user setting.')
        ])->getResponse();
    }

    /**
     * Put profile data
     */
    public function put_profile()
    {
        /** @var User $User */
        $User = ClassRegistry::init("User");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init("UploadService");
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init("AttachedFileService");
        Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');

        $data = $this->getRequestJsonBody();

        $user_options = [
            'conditions' => ['User.id' => $data['User']['id'],],
        ];
        $team_options = [
            'conditions' => ['TeamMember.team_id' => $data['TeamMember']['id'],],
        ];
        $user = $User->find('first', $user_options);
        $teamMember = $TeamMember->find('first', $team_options);

        if (!empty($user)) {
            $user['User']['first_name'] = $data['User']['first_name'];
            $user['User']['last_name'] = $data['User']['last_name'];
            $user['User']['gender_type'] = $data['User']['gender_type'];
            $user['User']['birth_day'] = $data['User']['birth_day'];
            $user['User']['hide_year_flg'] = $data['User']['hide_year_flg'];
            $user['User']['hometown'] = $data['User']['hometown'];
            $teamMember['TeamMember']['comment'] = $data['TeamMember']['comment'];

            try {
                if ($data['User']['photo']) {
                    /** @var UploadedFile $uploadedFile */
                    $uploadedFile = $UploadService->getBuffer($this->getUserId(), $this->getTeamId(), $data['User']['photo']);
                    /** @var AttachedFileEntity $attachedFile */
                    $AttachedFileService->add($this->getUserId(), $this->getTeamId(), $uploadedFile, AttachedModelType::TYPE_MODEL_ACTION_RESULT());

                    $user['User']['photo_file_name'] = $uploadedFile->getFileName();
                    $UploadService->saveWithProcessing("User", $this->getUserId(), 'photo', $uploadedFile);
                }
                if ($data['User']['cover_photo']) {
                    /** @var UploadedFile $uploadedFile */
                    $uploadedFile = $UploadService->getBuffer($this->getUserId(), $this->getTeamId(), $data['User']['cover_photo']);
                    /** @var AttachedFileEntity $attachedFile */
                    $AttachedFileService->add($this->getUserId(), $this->getTeamId(), $uploadedFile, AttachedModelType::TYPE_MODEL_ACTION_RESULT());

                    $user['User']['cover_photo_file_name'] = $uploadedFile->getFileName();
                    $UploadService->saveWithProcessing("User", $this->getUserId(), 'cover_photo', $uploadedFile);
                }
            } catch (Exception $e) {
                return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
            }
            
            $TeamMember->save($teamMember, false);
            $User->save($user, false);
        } else {
            return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
        }

        return ApiResponse::ok()->withBody(['data' => __("Saved user setting.")
        ])->getResponse();
    }

    /**
     * Put Notifications data
     */
    public function put_notifications()
    {
        /** @var NotifySetting $NotifySetting */
        $NotifySetting = ClassRegistry::init("NotifySetting");

        $data = $this->getRequestJsonBody();

        $options = [
            'conditions' => ['NotifySetting.user_id' => $data['User']['id'],],
        ];
        $notifyData = $NotifySetting->find('first', $options);

        if (!empty($notifyData)) {
            $notifyData['NotifySetting']['email_status'] = $data['NotifySetting']['email_status'];
            $notifyData['NotifySetting']['mobile_status'] = $data['NotifySetting']['mobile_status'];
            $notifyData['NotifySetting']['desktop_status'] = $data['NotifySetting']['desktop_status'];

            $NotifySetting->save($notifyData, false);

            Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_NOTIFY_SETTING, true, null, false), 'user_data');
        } else {
            return ErrorResponse::badRequest()->withMessage(__("Failed to save user setting."))->getResponse();
        }

        return ApiResponse::ok()->withBody(['data' => __("Saved user setting.")
        ])->getResponse();
    }

    /**
     * Put change email
     */
    public function put_change_email()
    {
        $data = $this->getRequestJsonBody();

        try {
            if (!$this->User->validatePassword($data)) {
                return ErrorResponse::internalServerError()->withMessage(__('Invalid Data'))->getResponse();
            } else {
                $email_data = $this->User->addEmail($data, $this->getUserId());
            }
        } catch (RuntimeException $e) {
            return ErrorResponse::internalServerError()->withMessage(__('System error has occurred.'))->getResponse();
        }

        $this->GlEmail->sendMailChangeEmailVerify(
            $this->getUserId(),
            $email_data['Email']['email'],
            $email_data['Email']['email_token']
        );

        return ApiResponse::ok()->withBody(['data' => __('Confirmation has been sent to your email address.')
        ])->getResponse();
    }

    /**
     * Put change password
     */
    public function put_change_password()
    {
        $data = $this->getRequestJsonBody();

        try {
            if (!$this->User->validatePassword($data)) {
                return ErrorResponse::badRequest()->withMessage(__('Failed to save password change.'))->getResponse();
            } else {
                $this->User->changePassword($data);
            }
        } catch (RuntimeException $e) {
            return ErrorResponse::internalServerError()->withMessage(__('Failed to save password change.'))->getResponse();
        }

        return ApiResponse::ok()->withBody(['data' => __('Changed password.')
        ])->getResponse();
    }

    /**
     * Post Enable 2FA
     */
    public function post_enable_2fa()
    {
        $tmp = $this->getRequestJsonBody();

        $data = [
            'User' => [
                '2fa_code' => $tmp['code']
            ]
        ];

        try {
            if (!$secret_key = $this->Session->read('2fa_secret_key')) {
                throw new RuntimeException(__("An error has occurred."));
            }
            if (!Hash::get($data, 'User.2fa_code')) {
                throw new RuntimeException(__("An error has occurred."));
            }
            if (!$this->TwoFa->verifyKey($secret_key, $data['User']['2fa_code'])) {
                return ErrorResponse::internalServerError()->withMessage(__("The code is incorrect."))->getResponse();
            }

            $this->User->id = $this->getUserId();
            $this->User->saveField('2fa_secret', $secret_key);
            //
        } catch (RuntimeException $e) {
            return ErrorResponse::internalServerError()->withMessage($e->getMessage())->getResponse();
        }

        $this->Session->delete('2fa_secret_key');
        //$this->Mixpanel->track2SV(MixpanelComponent::TRACK_2SV_ENABLE);

        return ApiResponse::ok()->withBody(['data' => __('Succeeded to save 2-Step Verification.')
        ])->getResponse();
    }

    /**
     * Post Disable 2FA
     */
    function post_desable_2fa()
    {
        $this->User->id = $this->getUserId();
        $this->User->saveField('2fa_secret', null);
        $this->User->RecoveryCode->invalidateAll($this->User->id);

        if (empty($this->getTeamId()) === false && empty($this->getUserId()) === false) {
            $this->GlRedis->deleteDeviceHash($this->getTeamId(), $this->getUserId());
        }
        //$this->Mixpanel->track2SV(MixpanelComponent::TRACK_2SV_DISABLE);

        return ApiResponse::ok()->withBody(['data' => __("Succeeded to cancel 2-Step Verification.")
        ])->getResponse();
    }

    /**
     * Get recovery Code
     */
    function get_recovery_code()
    {
        $recovery_codes = $this->User->RecoveryCode->getAll($this->getUserId());
        if (!$recovery_codes) {
            $success = $this->User->RecoveryCode->regenerate($this->getUserId());
            if (!$success) {
                throw new NotFoundException();
            }
            $recovery_codes = $this->User->RecoveryCode->getAll($this->getUserId());
        }

        return ApiResponse::ok()->withBody([
            'data' => $recovery_codes
        ])->getResponse();
    }

    /**
     * Get regenerated Code
     */
    function get_regenerated_code()
    {
        $success = $this->User->RecoveryCode->regenerate($this->getUserId());
        if (!$success) {
            return ErrorResponse::internalServerError()->withMessage(__("An error has occurred."))->getResponse();
        }
        $recovery_codes = $this->User->RecoveryCode->getAll($this->getUserId());

        return ApiResponse::ok()->withBody([
            'data' => $recovery_codes
        ])->getResponse();
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

        /** @var Term $Term */
        $Term = ClassRegistry::init("Term");
        $Term->Team->current_team_id = $this->getTeamId();
        $Term->Team->my_uid = $this->getUserId();
        $Term->current_team_id = $this->getTeamId();
        $Term->my_uid = $this->getUserId();
        $currentTerm = $Term->getCurrentTermData();

        // Find KeyResult ordered by actioned in recent
        $findForKeyResultListRequest = new FindForKeyResultListRequest(
            $this->getUserId(),
            $this->getTeamId(),
            $currentTerm);
        $findForKeyResultListRequest->setOnlyKrIncomplete(true);
        $keyResults = $KeyResultService->findForKeyResultList($findForKeyResultListRequest);

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

    public function get_kr_progress()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var KrProgressLog $KrProgressLog */
        $KrProgressLog = ClassRegistry::init('KrProgressLog');
        /** @var UserExtension $UserExtension */
        $UserExtension = ClassRegistry::init('UserExtension');
        /** @var GoalExtension $UserExtension */
        $GoalExtension = ClassRegistry::init('GoalExtension');
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        /** @var Term $Term */
        $Term = ClassRegistry::init("Term");
        $Term->Team->current_team_id = $this->getTeamId();
        $Term->Team->my_uid = $this->getUserId();
        $Term->current_team_id = $this->getTeamId();
        $Term->my_uid = $this->getUserId();
        $currentTerm = $Term->getCurrentTermData();

        $now = GoalousDateTime::now();
        $periodFrom = $now->copy()->startOfDay()->subDays(7);
        $periodTo = $now->copy();

        $goalIdSelected = intval($this->request->query('goal_id'));
        $limit = intval($this->request->query('limit'));
        $withKrProgressGraphValues = intval($this->request->query('with_kr_progress_graph_values'));

        // Find KeyResult ordered by actioned in recent
        $findForKeyResultListRequest = new FindForKeyResultListRequest(
            $this->getUserId(),
            $this->getTeamId(),
            $currentTerm);
        $findForKeyResultListRequest->setGoalIdSelected($goalIdSelected);
        $findForKeyResultListRequest->setLimit($limit);

        $keyResults = $KeyResultService->findForKeyResultList($findForKeyResultListRequest);

        $krs = [];
        foreach ($keyResults as $index => $keyResult) {
            // Find action that has filtered by period
            $actionResults = $ActionResult->getByKrIdAndCreatedFrom($keyResult['KeyResult']['id'], $periodFrom);
            $actionResults = Hash::extract($actionResults, "{n}.ActionResult");

            // Total action progress in period
            $changeValueTotal = 0;
            foreach ($actionResults as $i => $actionResult) {
                $krProgressLog = $KrProgressLog->getByActionResultId($actionResult['id'])->toArray();
                $actionResults[$i]['kr_progress_log'] = $krProgressLog;
                $changeValueTotal += $krProgressLog['change_value'];

                // Need a post_id to make link to action detail post.
                $post = $Post->getByActionResultId($actionResult['id'], $this->getTeamId());
                $actionResults[$i]['post_id'] = $post['Post']['id'];
                $actionResults[$i] = $UserExtension->extend($actionResults[$i], 'user_id');
            }

            $keyResult['KeyResult'] = $GoalExtension->extend($keyResult['KeyResult'], 'goal_id');

            array_push($krs, array_merge(
                $keyResult['KeyResult'],
                [
                    'progress_log_recent_total' => [
                        'change_value' => $changeValueTotal,
                    ],
                    'action_results' => $actionResults,
                ]
            ));
        }

        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $response = [
            'data' => [
                'period_kr_collection' => [
                    'from' => $periodFrom->getTimestamp(),
                    'to' => $periodTo->getTimestamp(),
                ],
                'krs_total' => $KeyResultService->countMine($goalIdSelected ?? null, false, $this->getUserId()),
                'krs' => $krs,
                'goals' => $GoalService->findNameListAsMember($this->getUserId(), $currentTerm['start_date'], $currentTerm['end_date']),
            ],
        ];

        if ($withKrProgressGraphValues) {
            $todayDate = AppUtil::dateYmd(REQUEST_TIMESTAMP + $currentTerm['timezone'] * HOUR);
            $graphRange = $GoalService->getGraphRange(
                $todayDate,
                GoalService::GRAPH_TARGET_DAYS,
                GoalService::GRAPH_MAX_BUFFER_DAYS
            );
            $progressGraph = $GoalService->getUserAllGoalProgressForDrawingGraph(
                $this->getUserId(),
                $graphRange['graphStartDate'],
                $graphRange['graphEndDate'],
                $graphRange['plotDataEndDate'],
                true
            );
            $TimeEx = new TimeExHelper(new View());
            $krProgressGraphValues = [
                'data'       => [
                    'sweet_spot_top' => $progressGraph[0],
                    'sweet_spot_bottom' => $progressGraph[1],
                    'data' => $progressGraph[2],
                    'x' => $progressGraph[3],
                ],
                'start_date' => $TimeEx->formatDateI18n(strtotime($graphRange['graphStartDate'])),
                'end_date'   => $TimeEx->formatDateI18n(strtotime($graphRange['graphEndDate'])),
            ];
            $response['data']['kr_progress_graph'] = $krProgressGraphValues;
        }

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
