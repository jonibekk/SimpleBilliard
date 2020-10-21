<?php

App::import('Lib/DataExtender', 'BaseExtender');
App::import('Service', 'ImageStorageService');
App::uses('CircleMember', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Evaluation', 'Model');
App::uses('Email', 'Model');
App::uses('User', 'Model');
App::uses('Team', 'Model');
App::uses('Goal', 'Model');
App::import('Service', 'GoalApprovalService');
App::import('Service', 'TeamService');
App::import('Service', 'SetupService');
App::import('Service', 'SavedPostService');
App::uses('LangUtil', 'Util');

App::uses('GlRedis', 'Model');

use Goalous\Enum as Enum;

class MeExtender extends BaseExtender
{
    const EXTEND_ALL                      = "ext:user:all";
    const EXTEND_CURRENT_TEAM_MEMBER_OWN  = "ext:user:is_current_team_admin";
    const EXTEND_JOINED_ACTIVE_TEAMS      = "ext:user:joined_active_teams";
    const EXTEND_NOTIFICATION_SETTING     = "ext:user:notification_setting";
    const EXTEND_UNAPPROVED_GOAL_COUNT    = "ext:user:unapproved_goal_count";
    const EXTEND_EVALUABLE_COUNT          = "ext:user:evaluable_count";
    const EXTEND_IS_EVALUATION_AVAILABLE  = "ext:user:is_evaluation_available";
    const EXTEND_JOINED_NOTIFYING_CIRCLES = "ext:user:joined_notifying_circles";
    const EXTEND_NEW_NOTIFICATION_COUNT   = "ext:user:new_notification_count";
    const EXTEND_NEW_MESSAGE_COUNT        = "ext:user:new_message_count";
    const EXTEND_EMAIL                    = "ext:user:email";
    const EXTEND_IS_2FA_COMPLETED         = "ext:user:is_2fa_completed";
    const EXTEND_SETUP_REST_COUNT         = "ext:user:setup_rest_count";
    const EXTEND_ACTION_COUNT             = "ext:user:action_count";
    const EXTEND_SAVED_ITEM_COUNT         = "ext:user:saved_item_count";

    public function extend(array $data, int $userId, int $currentTeamId, array $extensions = []): array
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var NotifySetting $NotifySetting */
        $NotifySetting = ClassRegistry::init('NotifySetting');
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');

        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        $data['profile_img_url'] = $ImageStorageService->getImgUrlEachSize($data, 'User');
        $data['cover_img_url'] = $ImageStorageService->getImgUrlEachSize($data, 'User', 'cover_photo');

        $data['current_team_id'] = $currentTeamId;
        $currentTeam = $TeamService->get(new TeamResourceRequest($currentTeamId, $userId, $currentTeamId));
        $data['current_team'] = $currentTeam;
        $data['language'] = LangUtil::convertISOFrom3to2($data['language']);

        if ($this->includeExt($extensions, self::EXTEND_CURRENT_TEAM_MEMBER_OWN)) {
            $data['current_team_member_own'] = $TeamMember->getUnique($userId, $currentTeamId);
        }
        if ($this->includeExt($extensions, self::EXTEND_JOINED_ACTIVE_TEAMS)) {
            $activeTeams = $TeamMember->getActiveTeamList($userId);
            $data['my_active_teams'] = [];
            if (!empty($TeamMember->getSsoEnabledTeams($userId))) {
                $data['my_active_teams'][] = [
                    'id'   => $currentTeam['id'],
                    'name' => $currentTeam['name']
                ];
            } else {
                foreach ($activeTeams as $activeTeamId => $name) {
                    $data['my_active_teams'][] = [
                        'id'   => $activeTeamId,
                        'name' => $name
                    ];
                }
            }
        }
        if ($this->includeExt($extensions, self::EXTEND_NOTIFICATION_SETTING)) {
            $NotifySetting->current_team_id = $currentTeamId;
            $data['notify_setting'] = $NotifySetting->getMySettings($userId);
        }
        if ($this->includeExt($extensions, self::EXTEND_UNAPPROVED_GOAL_COUNT)) {
            /** @var GoalApprovalService $GoalApprovalService */
            $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
            $data['unapproved_goal_count'] = $GoalApprovalService->countUnapprovedGoal($userId, $currentTeamId);
        }
        if ($this->includeExt($extensions, self::EXTEND_EVALUABLE_COUNT)) {
            /** @var Evaluation $Evaluation */
            $Evaluation = ClassRegistry::init("Evaluation");
            $Evaluation->current_team_id = $currentTeamId;
            $Evaluation->Team->current_team_id = $currentTeamId;
            $Evaluation->Team->EvaluationSetting->current_team_id = $currentTeamId;
            $Evaluation->my_uid = $userId;
            $data['evaluable_count'] = $Evaluation->getMyTurnCount();
        }
        if ($this->includeExt($extensions, self::EXTEND_IS_EVALUATION_AVAILABLE)) {
            /** @var EvaluationSetting $EvaluationSetting */
            $EvaluationSetting = ClassRegistry::init("EvaluationSetting");
            $EvaluationSetting->my_uid = $userId;
            $EvaluationSetting->current_team_id = $currentTeamId;

            $data['is_evaluation_available'] = $EvaluationSetting->isEnabled();
        }
        if ($this->includeExt($extensions, self::EXTEND_NEW_MESSAGE_COUNT)) {
            $data['new_message_count'] = $GlRedis->getCountOfNewMessageNotification($currentTeamId, $userId);
        }
        if ($this->includeExt($extensions, self::EXTEND_NEW_NOTIFICATION_COUNT)) {
            $data['new_notification_count'] = $GlRedis->getCountOfNewNotification($currentTeamId, $userId);
        }
        if ($this->includeExt($extensions, self::EXTEND_JOINED_NOTIFYING_CIRCLES)) {
            /** @var CircleMember $CircleMember */
            $CircleMember = ClassRegistry::init('CircleMember');
            $circleIds = [];

            $circles = $CircleMember->getCirclesWithNotificationFlg($currentTeamId, $userId, true);
            foreach ($circles as $circle) {
                $circleIds[] = (string)$circle['circle_id'];
            }
            $data['my_notifying_circles'] = $circleIds;
        }
        if ($this->includeExt($extensions, self::EXTEND_IS_2FA_COMPLETED)) {
            /** @var User $User */
            $User = ClassRegistry::init('User');
            $user = $User->getById($data['id']);
            $data['is_2fa_completed'] = !empty($user['2fa_secret']);
        }
        if ($this->includeExt($extensions, self::EXTEND_EMAIL)) {
            /** @var Email $Email */
            $Email = ClassRegistry::init('Email');
            $email = $Email->getById($data['primary_email_id']);
            $data['email'] = $email['email'] ?? '';
        }
        if ($this->includeExt($extensions, self::EXTEND_SETUP_REST_COUNT)) {
            /** @var SetupService $SetupService */
            $SetupService = ClassRegistry::init("SetupService");
            $setupResolved = $SetupService->resolveSetupCompleteAndRest($data['id'], $data['setup_complete_flg']);
            $data['setup_complete_flg'] = $setupResolved['complete'];
            $data['setup_rest_count'] = $setupResolved['rest_count'];
        }

        if ($this->includeExt($extensions, self::EXTEND_ACTION_COUNT)) {
            /** @var Team $Team */
            $Team = ClassRegistry::init('Team');
            /** @var Goal $Goal */
            $Goal = ClassRegistry::init('Goal');

            $expire = 60 * 60 * 24;
            $Team->current_team_id = $currentTeamId;
            $Team->Term->current_team_id = $currentTeamId;
            $Team->Term->Team->current_team_id = $currentTeamId;
            $Goal->current_team_id = $currentTeamId;
            $currentTerm = $Team->Term->getCurrentTermData();
            Cache::set('duration', $expire, 'user_data');
            $action_count = Cache::remember(
                $Goal->getCacheKey(CACHE_KEY_ACTION_COUNT, true, $userId),
                function () use ($currentTerm, $Team, $Goal) {
                    if (empty($currentTerm)) {
                        return 0;
                    }
                    $timezone = $Team->getTimezone();
                    $startTimestamp = AppUtil::getStartTimestampByTimezone($currentTerm['start_date'], $timezone);
                    $endTimestamp = AppUtil::getEndTimestampByTimezone($currentTerm['end_date'], $timezone);
                    $res = $Goal->ActionResult->getCount('me', $startTimestamp, $endTimestamp);
                    return $res;
                },
                'user_data'
            );
            $data['action_count'] = $action_count;
        }

        if ($this->includeExt($extensions, self::EXTEND_SAVED_ITEM_COUNT)) {
            /** @var SavedPostService $SavedPostService */
            $SavedPostService = ClassRegistry::init("SavedPostService");
            $savedItemCountEachType = $SavedPostService->countSavedPostEachType($currentTeamId, $userId);
            $data['saved_item_count'] = $savedItemCountEachType['all'];
        }

        return $data;
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        throw new RuntimeException("Please implement " . __METHOD__);
    }
}
