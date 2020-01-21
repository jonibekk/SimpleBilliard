<?php
App::uses('AppController', 'Controller');
App::uses('AppUtil', 'Util');
App::uses('PaymentUtil', 'Util');
App::uses('Message', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::import('Service', 'AuthService');
App::import('Service', 'TermService');
App::import('Service', 'TeamService');
App::import('Service', 'EvaluationService');
App::import('Service', 'PaymentService');
App::import('Service', 'PaymentTiming');
App::import('Service', 'TeamMemberService');
App::import('Service', 'CampaignService');
App::import('Service', 'TeamTranslationLanguageService');
App::import('Controller/Traits', 'AuthTrait');

use Goalous\Enum as Enum;
use Goalous\Exception as GlException;

/**
 * Teams Controller
 *
 * @property Team $Team
 */
class TeamsController extends AppController
{

    use AuthTrait;

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkAdmin([
            'ajax_set_current_team_admin_user_flag',
            'ajax_set_current_team_evaluation_flag',
            'ajax_inactivate_team_member',
            'activate_team_member',
            'activate_confirm_with_payment',
            'activate_with_payment'
        ]);
    }

    /**
     * Basic info page
     *
     * @return CakeResponse
     */
    function index()
    {
        $this->layout = LAYOUT_TWO_COLUMN;
        $this->set('current_global_menu', 'team');

        $team = Hash::get($this->Team->getCurrentTeam(), 'Team');

        // Get current term info
        $currentTerm = $this->Team->Term->getCurrentTermData();
        $currentTermStartDate = Hash::get($currentTerm, 'start_date');
        $currentTermEndDate = Hash::get($currentTerm, 'end_date');

        // Get next term info
        $nextTerm = $this->Team->Term->getNextTermData();
        $nextTermStartDate = Hash::get($nextTerm, 'start_date');
        $nextTermEndDate = Hash::get($nextTerm, 'end_date');

        // If changed term in 2 weeks, Display information
        $changedTermFlg = $this->GlRedis->getChangedTerm($this->current_team_id);

        // Get timezone label
        $timezoneLabel = $this->getTimezoneLabel($team['timezone']);
        $this->set([
            'team'                    => $team,
            'current_term_start_date' => $currentTermStartDate,
            'current_term_end_date'   => $currentTermEndDate,
            'next_term_start_date'    => $nextTermStartDate,
            'next_term_end_date'      => $nextTermEndDate,
            'changed_term_flg'        => $changedTermFlg,
            'timezone_label'          => $timezoneLabel,
        ]);
    }

    /**
     * Get Label for timezone
     *
     * @param float $timezone
     *
     * @return mixed
     */
    private function getTimezoneLabel(float $timezone)
    {
        $timezones = AppUtil::getTimezoneList();
        $prefix = "";
        if ($timezone != 0 && abs($timezone) === $timezone) {
            $prefix = '+';
        }
        $timezone = $prefix . number_format($timezone, 1);
        return $timezones[$timezone];
    }

    public function add()
    {
        if (IS_DEMO) {
            throw new NotFoundException();
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        $border_months_options = $this->Team->getBorderMonthsOptions();
        $start_term_month_options = $this->Team->getMonths();
        //タイムゾーン
        $timezones = AppUtil::getTimezoneList();

        $this->set(compact('timezones'));

        if (!$this->request->is('post')) {
            return $this->render();
        }

        $userId = $this->Auth->user('id');

        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init("TeamService");

        if (!$TeamService->add($this->request->data, $userId)) {
            $this->Notification->outError(__("Failed to create a team."));
            return $this->render();
        }
        $newTeamId = $this->Team->getLastInsertID();

        $this->_refreshAuth($userId);
        $this->Session->write('team_id', $newTeamId);
        $this->Session->write('current_team_id', $newTeamId);

        $this->Notification->outSuccess(__("Created a team."));
        $this->Session->delete('user_has_no_team');
        return $this->redirect(['action' => 'invite']);
    }

    public function edit_team()
    {
        $this->request->allowMethod('post');
        $this->Team->id = $this->current_team_id;
        $team = $this->Team->getById($this->current_team_id);
        $updateData = Hash::get($this->request->data, 'Team');
        if (empty($updateData)) {
            $this->Notification->outError(__("Failed to change basic team settings."));
            return $this->redirect($this->referer());
        }

        $updateFields = [
            'name',
            'photo_file_name',
        ];
        $isPaidPlan = Hash::get($team, 'service_use_status') == Enum\Model\Team\ServiceUseStatus::PAID;
        if (!$isPaidPlan) {
            $updateFields[] = 'timezone';
        }

        if ($this->Team->save($updateData, $updateFields)) {
            Cache::clear(false, 'team_info');
            $this->Notification->outSuccess(__("Changed basic team settings."));
        } else {
            $this->Notification->outError(__("Failed to change basic team settings."));
        }

        // If change timezone, notify team members
        $newTimezone = Hash::get($this->request->data, 'Team.timezone');
        // Send notification
        if ((float)$team['timezone'] != (float)$newTimezone) {
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CHANGED_TEAM_BASIC_SETTING, $this->current_team_id);

        }

        return $this->redirect($this->referer());
    }

    /**
     * Update term
     * - validate forbitten
     * - validate request data
     * - update term
     * - notify to team members
     */
    public function edit_term()
    {
        /** @var TermService $TermService */
        $TermService = ClassRegistry::init("TermService");
        /** @var EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init("EvaluationService");

        $this->request->allowMethod('post');

        $teamId = $this->current_team_id;
        $userId = $this->Auth->user('id');

        // checking 403
        if (!$this->Team->TeamMember->isActiveAdmin($userId, $teamId)) {
            $this->Notification->outError(__("You have no right to operate it."));
            return $this->redirect($this->referer());
        }

        // checking can change term setting
        if ($EvaluationService->isStarted()) {
            $this->Notification->outError(__("The current term has already been evaluated and cannot be changed.  You can still apply changes to the next term."));
            return $this->redirect($this->referer());
        }

        // data validation
        $requestData = Hash::get($this->request->data, 'Team');
        $validRes = $TermService->validateUpdate($requestData);
        if ($validRes !== true) {
            $this->Notification->outError($validRes);
            return $this->redirect($this->referer());
        }

        // term updating
        if (!$TermService->update($requestData)) {
            $this->Notification->outError(__("Failed to change terms setting."));
            return $this->redirect($this->referer());
        }

        $this->Notification->outSuccess(__("Changed terms setting."));

        // Save changed term info to redis
        $this->GlRedis->saveChangedTerm($this->current_team_id);

        // Send notification
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CHANGED_TERM_SETTING, $this->current_team_id);

        return $this->redirect($this->referer());
    }

    /**
     * チームを削除する
     */
    public function delete_team()
    {
        $this->request->allowMethod('post');

        // TODO: Disabled to delete team in all case now. see -> https://jira.goalous.com/browse/GL-6022
        $this->log(sprintf("■ User attempted to delete the team! user_id: %s, team_id: %s, debugger trace: %s",
            $this->my_uid,
            var_export(CakeSession::read('current_team_id'), true),
            Debugger::trace()
        ));
        throw new RuntimeException(__('You cannot delete the team. Please contact to Goalous team.'));

        // チーム管理者かチェック
        try {
            $this->Team->TeamMember->adminCheck($this->current_team_id, $this->Auth->user('id'));
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            $this->redirect($this->referer());
        }

        $this->Team->begin();
        if (!(
            // チーム削除
            $this->Team->deleteTeam($this->current_team_id) &&
            // 削除されたチームが default_team_id になっている場合、null にする
            $this->User->clearDefaultTeamId($this->current_team_id)
        )
        ) {
            $this->Team->rollback();
            $this->Notification->outError(__("Failed to delete a team."));
            return $this->redirect($this->referer());
        }
        $this->Team->commit();

        // セッション中の default_team_id を更新
        $this->_refreshAuth();

        // 事前に全ユーザのteam listデータを削除
        Cache::clear(false, 'team_info');
        // 所属チームリストを更新して取得
        $this->User->TeamMember->setActiveTeamList($this->Auth->user('id'));
        $active_team_list = $this->User->TeamMember->getActiveTeamList($this->Auth->user('id'));

        // 他に所属チームがある場合
        if ($active_team_list) {
            $this->_switchTeam(key($active_team_list), $this->Auth->user('id'));
            $url = '/';
        } // 他に所属チームがない場合
        else {
            $this->Session->write('current_team_id', null);
            $url = ['controller' => 'teams', 'action' => 'add'];
        }
        $this->Notification->outSuccess(__("Deleted the team."));
        return $this->redirect($url);
    }

    public function settings()
    {
        /** @var EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init("EvaluationService");

        $this->layout = LAYOUT_TWO_COLUMN;
        $team_id = $this->Session->read('current_team_id');
        try {
            $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $border_months_options = $this->Team->getBorderMonthsOptions();
        $start_term_month_options = $this->Team->getMonths();
        $this->set(compact('border_months_options', 'start_term_month_options'));

        $team = $this->Team->findById($team_id);
        unset($team['Team']['id']);
        $term_start_date = $this->Team->Term->getCurrentTermData()['start_date'];
        $term_end_date = $this->Team->Term->getCurrentTermData()['end_date'];
        //get evaluation setting
        $eval_enabled = $this->Team->EvaluationSetting->isEnabled();
        $eval_setting = $this->Team->EvaluationSetting->getEvaluationSetting();
        $eval_scores = $this->Team->Evaluation->EvaluateScore->getScore($team_id);
        $goal_categories = [
            'GoalCategory' => Hash::extract($this->Goal->GoalCategory->getCategories(), '{n}.GoalCategory')
        ];

        $this->request->data = array_merge($this->request->data, $eval_setting, $eval_scores, $goal_categories, $team);

        // current term data
        $current_term_id = $this->Team->Term->getCurrentTermId();
        $current_term = $this->Team->Term->getById($current_term_id);
        /** @var bool $current_term_exists */
        $current_term_exists = !is_null($current_term);
        /** @var Enum\Model\Term\EvaluateStatus $current_term_evaluation_status */
        $current_term_evaluation_status = null;
        if ($current_term_exists) {
            $current_term_evaluation_status = new Enum\Model\Term\EvaluateStatus(intval($current_term['evaluate_status']));
        }

        // previous term data
        $previous_term_id = $this->Team->Term->getPreviousTermId();
        $previous_term = $this->Team->Term->getById($previous_term_id);
        /** @var bool $previous_term_exists */
        $previous_term_exists = !is_null($previous_term);
        /** @var Enum\Model\Term\EvaluateStatus $previous_term_evaluation_status */
        $previous_term_evaluation_status = null;
        if ($previous_term_exists) {
            $previous_term_evaluation_status = new Enum\Model\Term\EvaluateStatus(intval(Hash::get($previous_term, 'evaluate_status', 0)));
        }

        $eval_start_button_enabled = true;
        if (!$this->Team->Term->isAbleToStartEvaluation($current_term_id)) {
            $eval_start_button_enabled = false;
        }
        $previous_eval_start_button_enabled = true;
        if (!$this->Team->Term->isAbleToStartEvaluation($previous_term_id)) {
            $previous_eval_start_button_enabled = false;
        }
        $this->set(compact('team', 'term_start_date', 'term_end_date', 'eval_enabled', 'eval_start_button_enabled',
            'previous_eval_start_button_enabled', 'eval_scores'));
        $current_statuses = $this->Team->Evaluation->getAllStatusesForTeamSettings($current_term_id);
        $current_progress = $this->_getEvalProgress($current_statuses);
        $previous_statuses = $this->Team->Evaluation->getAllStatusesForTeamSettings($previous_term_id);
        $previous_progress = $this->_getEvalProgress($previous_statuses);

        // Get term info
        $current_eval_is_frozen = $this->Team->Term->checkFrozenEvaluateTerm($current_term_id);
        $current_eval_is_started = $this->Team->Term->isStartedEvaluation($current_term_id);
        $current_term = $this->Team->Term->getCurrentTermData();
        $current_term_start_date = Hash::get($current_term, 'start_date');
        $current_term_end_date = Hash::get($current_term, 'end_date');
        $current_term_timezone = Hash::get($current_term, 'timezone');

        $previous_eval_is_frozen = $this->Team->Term->checkFrozenEvaluateTerm($previous_term_id);
        $previous_eval_is_started = $this->Team->Term->isStartedEvaluation($previous_term_id);
        $previous_term = $this->Team->Term->getPreviousTermData();
        $previous_term_start_date = Hash::get($previous_term, 'start_date');
        $previous_term_end_date = Hash::get($previous_term, 'end_date');
        $previous_term_timezone = Hash::get($previous_term, 'timezone');
        $next_term = $this->Team->Term->getNextTermData();
        $next_term_start_date = Hash::get($next_term, 'start_date');
        $next_term_end_date = Hash::get($next_term, 'end_date');
        $next_term_timezone = Hash::get($next_term, 'timezone');

        $can_start_evaluation = false;
        // Decide $can_start_evaluation
        if (!$previous_term_exists) {
            $can_start_evaluation = !$current_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        } else if (!$current_term_exists) {
            $can_start_evaluation = !$previous_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        } else {
            $isInProgress = $current_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::IN_PROGRESS()) || $previous_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::IN_PROGRESS());
            $can_start_evaluation = !$isInProgress;
        }
        $disable_current_radio = false;
        if (!$can_start_evaluation || !$current_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::NOT_STARTED())) {
            $disable_current_radio = true;
        }
        $disable_previous_radio = false;
        if (!$can_start_evaluation || !$previous_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::NOT_STARTED())) {
            $disable_previous_radio = true;
        }

        $either_start_button_enabled = ($eval_start_button_enabled xor $previous_eval_start_button_enabled);
        $current_radio_checked = false;
        if ($current_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::IN_PROGRESS())) {
            $current_radio_checked = true;
        } elseif ($can_start_evaluation) {
            $current_radio_checked = ($either_start_button_enabled && $eval_start_button_enabled);
        }

        $previous_radio_checked = false;
        if ($previous_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::IN_PROGRESS())) {
            $previous_radio_checked = true;
        } elseif ($can_start_evaluation) {
            $previous_radio_checked = ($either_start_button_enabled && $previous_eval_start_button_enabled);
        }

        $both_term_selectable = ($current_term_exists
            && $previous_term_exists
            && $current_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::NOT_STARTED())
            && $previous_term_evaluation_status->equals(Enum\Model\Term\EvaluateStatus::NOT_STARTED()));

        // term changing init data
        /** @var TermService $TermService */
        $TermService = ClassRegistry::init("TermService");
        $nextSelectableStartYm = $TermService->getSelectableNextStartYmList($current_term_start_date, date('Y-m'));
        $termLength = $team['Team']['border_months'];
        $currentTermStartYm = date('Y-m', strtotime($current_term_start_date));
        $currentTermEndYm = date('Y-m', strtotime($current_term_end_date));
        $nextTermStartYm = date('Y-m', strtotime($next_term_start_date));
        $nextTermEndYm = date('Y-m', strtotime($next_term_end_date));

        //タイムゾーン
        $timezones = AppUtil::getTimezoneList();
        $isPaidPlan = Hash::get($team, 'Team.service_use_status') == Enum\Model\Team\ServiceUseStatus::PAID;
        // Get timezone label
        $timezoneLabel = $this->getTimezoneLabel($team['Team']['timezone']);

        // Translation
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');
        if ($TeamTranslationLanguage->hasLanguage($team_id)) {
            try {
                $translationTeamLanguageCandidates = $TeamTranslationLanguageService->getAllLanguages($team_id);
                $translationTeamDefaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguage($team_id);
                $translationTeamTotalUsage = $TeamTranslationStatus->getTotalUsageCount($team_id);
                $translationTeamTotalLimit = $TeamTranslationStatus->getLimit($team_id);

                // Only paid teams have reset date for translation usage status
                if ($this->Team->isPaidPlan($team_id)) {
                    App::import('View', 'Helper/TimeExHelper');
                    $TimeEx = new TimeExHelper(new View());
                    $translationTeamResetText = $TimeEx->formatYearDayI18nFromDate($PaymentService->getNextBaseDate($team_id));
                } else {
                    $translationTeamResetText = __("Translation usage won't be reset in free trial plan.");
                }
            } catch (Exception $e) {
                GoalousLog::error("Error in getting translation setting for team setting", [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'team_id' => $team_id,
                    'user_id' => $this->Auth->user('id')
                ]);
                $translationTeamLanguageCandidates = [];
                $translationTeamDefaultLanguage = [];
                $translationTeamTotalUsage = 0;
                $translationTeamTotalLimit = 0;
                $translationTeamResetText = "-";
            }
        }
        $isStartedEvaluation = $EvaluationService->isStarted();
        $this->set(compact(
            'timezones',
            'current_statuses',
            'current_progress',
            'previous_statuses',
            'previous_progress',
            'eval_is_frozen',
            'current_term_id',
            'current_eval_is_frozen',
            'current_eval_is_started',
            'current_term_start_date',
            'current_term_end_date',
            'current_term_timezone',
            'previous_term_id',
            'previous_eval_is_frozen',
            'previous_eval_is_started',
            'previous_term_start_date',
            'previous_term_end_date',
            'previous_term_timezone',
            'next_term_start_date',
            'next_term_end_date',
            'next_term_timezone',
            'nextSelectableStartYm',
            'currentTermStartYm',
            'currentTermEndYm',
            'nextTermStartYm',
            'nextTermEndYm',
            'termLength',
            'isStartedEvaluation',
            'isPaidPlan',
            'timezoneLabel',
            'current_term_exists',
            'previous_term_exists',
            'can_start_evaluation',
            'disable_previous_radio',
            'disable_current_radio',
            'either_start_button_enabled',
            'current_radio_checked',
            'previous_radio_checked',
            'both_term_selectable',
            'translationTeamLanguageCandidates',
            'translationTeamDefaultLanguage',
            'translationTeamTotalUsage',
            'translationTeamTotalLimit',
            'translationTeamResetText'
        ));

        return $this->render();
    }

    function _getEvalProgress($statuses)
    {
        if (!$statuses) {
            return null;
        }
        // 全体progressカウント
        $all_cnt = array_sum(Hash::extract($statuses, "{n}.all_num"));
        $incomplete_cnt = array_sum(Hash::extract($statuses, "{n}.incomplete_num"));
        $complete_cnt = (int)$all_cnt - (int)$incomplete_cnt;
        $progress_percent = 0;
        if ($complete_cnt != 0) {
            $progress_percent = round(((int)$complete_cnt / (int)$all_cnt) * 100, 1);
        }
        return $progress_percent;
    }

    function save_evaluation_setting()
    {
        $this->request->allowMethod(['post', 'put']);
        $this->Team->begin();
        if ($this->Team->EvaluationSetting->save($this->request->data['EvaluationSetting'])) {
            $this->Team->commit();
            $this->Notification->outSuccess(__("Changed evaluation setting."));
            Cache::delete($this->Team->getCacheKey(CACHE_KEY_TEAM_EVAL_SETTING, false), 'team_info');
        } else {
            $this->Team->rollback();
            $this->Notification->outError(__("Failed to change evaluation setting."));
        }
        return $this->redirect($this->referer());
    }

    function save_evaluation_scores()
    {
        $this->request->allowMethod(['post', 'put']);
        $this->Team->begin();
        if ($this->Team->Evaluation->EvaluateScore->saveScores($this->request->data['EvaluateScore'],
            $this->Session->read('current_team_id'))
        ) {
            $this->Team->commit();
            $this->Notification->outSuccess(__("Changed evaluation score setting."));
        } else {
            $this->Team->rollback();
            $this->Notification->outError(__("Failed to change evaluation score setting."));
        }
        return $this->redirect($this->referer());
    }

    function save_goal_categories()
    {
        $this->request->allowMethod(['post', 'put']);
        $this->Team->begin();
        if ($this->Goal->GoalCategory->saveGoalCategories($this->request->data['GoalCategory'],
            $this->Session->read('current_team_id'))
        ) {
            $this->Team->commit();
            $this->Notification->outSuccess(__("Saved Goal category setting."));
        } else {
            $this->Team->rollback();
            $this->Notification->outError(__("Failed to save Goal category setting."));
        }
        return $this->redirect($this->referer());

    }

    function to_inactive_score()
    {
        $id = Hash::get($this->request->params, 'named.team_id');
        $this->request->allowMethod(['post']);
        $this->Team->Evaluation->EvaluateScore->setToInactive($id);
        $this->Notification->outSuccess(__("Deleted score definition."));
        return $this->redirect($this->referer());
    }

    function ajax_get_confirm_inactive_score_modal()
    {
        $id = Hash::get($this->request->params, 'named.team_id');
        $this->_ajaxPreProcess();
        $this->set(compact('id'));
        $response = $this->render('Team/confirm_to_inactive_score_modal');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function ajax_get_score_elm()
    {
        $this->_ajaxPreProcess();
        if (Hash::get($this->request->params, 'named.index')) {
            $this->set(['index' => $this->request->params['named']['index']]);
        }
        $response = $this->render('Team/eval_score_form_elm');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function ajax_get_confirm_inactive_goal_category_modal()
    {
        $id = Hash::get($this->request->params, 'named.team_id');
        $this->_ajaxPreProcess();
        $this->set(compact('id'));
        $response = $this->render('Team/confirm_to_inactive_goal_category_modal');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function ajax_get_goal_category_elm()
    {
        $this->_ajaxPreProcess();
        if (Hash::get($this->request->params, 'named.index')) {
            $this->set(['index' => $this->request->params['named']['index']]);
        }
        $response = $this->render('Team/goal_category_form_elm');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function to_inactive_goal_category()
    {
        $id = Hash::get($this->request->params, 'named.team_id');
        $this->request->allowMethod(['post']);
        $this->Goal->GoalCategory->setToInactive($id);
        $this->Notification->outSuccess(__("Deleted Goal category."));
        return $this->redirect($this->referer());
    }

    function ajax_get_term_start_end($start_term_month, $border_months, $timezone)
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->Term->getNewStartEndBeforeAdd($start_term_month, $border_months, $timezone);
        $res['start'] = date('Y/m/d', $res['start'] + $timezone * 3600);
        $res['end'] = date('Y/m/d', $res['end'] + $timezone * 3600);
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_term_start_end_by_edit($start_term_month, $border_months, $option, $timezone = null)
    {
        if (!$timezone) {
            $timezone = $this->Team->me['timezone'];
        }
        $this->_ajaxPreProcess();
        $save_data = $this->Team->Term->getSaveDataBeforeUpdate($option, $start_term_month, $border_months);
        $current_id = $this->Team->Term->getCurrentTermId();
        $next_id = $this->Team->Term->getNextTermId();
        $res = [];
        if ($option == Team::OPTION_CHANGE_TERM_FROM_CURRENT) {
            $res = [
                'current' => [
                    'start_date' => AppUtil::dateYmdReformat($save_data[$current_id]['start_date'], "/"),
                    'end_date'   => AppUtil::dateYmdReformat($save_data[$current_id]['end_date'], "/"),
                    'timezone'   => $timezone,
                ],
                'next'    => [
                    'start_date' => AppUtil::dateYmdReformat($save_data[$next_id]['start_date'], "/"),
                    'end_date'   => AppUtil::dateYmdReformat($save_data[$next_id]['end_date'], "/"),
                    'timezone'   => $timezone,
                ],
            ];
        }
        if ($option == Team::OPTION_CHANGE_TERM_FROM_NEXT) {
            $res = [
                'current' => [
                    'start_date' => null,
                    'end_date'   => null,
                    'timezone'   => $timezone,
                ],
                'next'    => [
                    'start_date' => AppUtil::dateYmdReformat($save_data[$next_id]['start_date'], "/"),
                    'end_date'   => AppUtil::dateYmdReformat($save_data[$next_id]['end_date'], "/"),
                    'timezone'   => $timezone,
                ],
            ];
        }
        return $this->_ajaxGetResponse($res);
    }

    function start_evaluation()
    {
        $this->request->allowMethod('post');
        try {
            if (!$this->Team->EvaluationSetting->isEnabled()) {
                throw new RuntimeException(__("Evaluation setting is not active."));
            }
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($this->referer());
        }
        //start evaluation process
        $this->Team->Evaluation->begin();
        if (!$this->Team->Evaluation->startEvaluation()) {
            $this->Team->Evaluation->rollback();
            $this->Notification->outError(__("Evaluation could not start."));
            return $this->redirect($this->referer());
        }
        $this->Team->Evaluation->commit();

        // 評価期間判定キャッシュ削除
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_IS_STARTED_EVALUATION, true), 'team_info');

        $this->Notification->outSuccess(__("Evaluation started."));
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_START,
            $this->Team->Term->getCurrentTermId());
        Cache::clear(false, 'team_info');
        return $this->redirect($this->referer());
    }

    public function invite()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->set('with_header_menu', false);

        $from_setting = false;
        if (strstr($this->referer(), "/settings")) {
            $from_setting = true;
        }
        $this->set(compact('from_setting'));

        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        $team = $this->Team->findById($team_id);
        $this->set(compact('team'));

        if (!$this->request->is('post')) {
            return $this->render();
        }

        $data = $this->request->data;
        //convert mail-address to array
        $email_list = $this->Team->getEmailListFromPost($data);

        //not exists correct email address.
        if (!$email_list) {
            $this->Notification->outError(__("Email address is incorrect."));
            return $this->redirect($this->referer());
        }

        //max 100 invitation
        $max_invitation_count = 100;
        if (count($email_list) > $max_invitation_count) {
            $this->Notification->outError(__("%s invitations are the limits in one time.", $max_invitation_count));
            return $this->redirect($this->referer());
        }

        $alreadyBelongTeamEmails = [];
        $sentEmails = [];
        //generate token and send mail one by one.
        foreach ($email_list as $email) {
            //don't process in case of exists in team.
            if ($this->User->Email->isActiveOnTeamByEmail($email, $team_id)) {
                $alreadyBelongTeamEmails[] = $email;
                continue;
            }
            //save invite mail data
            $invite = $this->Team->Invite->saveInvite(
                $email,
                $team_id,
                $this->Auth->user('id'),
                !empty($data['Team']['comment']) ? $data['Team']['comment'] : null
            );
            if (!$invite) {
                $this->Notification->outError(__("Error, failed to invite."));
                return $this->redirect($this->referer());
            }
            //send invite mail
            $team_name = $this->Team->TeamMember->myTeams[$this->Session->read('current_team_id')];
            $this->GlEmail->sendMailInvite($invite, $team_name);
            $sentEmails[] = $email;
        }

        $already_joined_usr_msg = null;
        if (!empty($alreadyBelongTeamEmails)) {
            $already_joined_usr_msg .= __("Cancelled sending email to %s people who are already in the circle.",
                count($alreadyBelongTeamEmails));
        }

        if (empty($sentEmails)) {
            $this->Notification->outError($already_joined_usr_msg);
            return $this->redirect($this->referer());
        }

        $msg = __("Invited %s people.", count($sentEmails)) . "\n" . $already_joined_usr_msg;
        $this->Notification->outSuccess($msg);

        if (!$from_setting) {
            $this->Session->write('referer_status', REFERER_STATUS_SIGNUP_WITH_INVITING);
            return $this->redirect('/');
        }

        return $this->redirect($this->referer());
    }

    public function invite_skip()
    {
        $this->Session->write('referer_status', REFERER_STATUS_SIGNUP_WITH_NOT_INVITING);
        return $this->redirect('/');
    }

    function ajax_upload_update_members_csv()
    {
        $this->request->allowMethod('post');
        $result = [
            'error' => false,
            'css'   => 'alert-success',
            'title' => __("Update completed"),
            'msg'   => '',
        ];
        $this->_ajaxPreProcess('post');
        $csv = $this->Csv->convertCsvToArray($this->request->data['Team']['csv_file']['tmp_name']);
        $this->Team->TeamMember->begin();
        $save_res = $this->Team->TeamMember->updateMembersFromCsv($csv);

        $this->GlRedis->dellKeys("cache_user_data:unapproved_count:team:" . $this->current_team_id . ":user:*");
        if ($save_res['error']) {
            $this->Team->TeamMember->rollback();
            $result['error'] = true;
            $result['css'] = 'alert-danger';
            $result['msg'] = $save_res['error_msg'];
            if ($save_res['error_line_no'] == 0) {
                $result['title'] = __("Error in extension data.");
            } else {
                $result['title'] = __("Error in column %s (Column number included in text)",
                    $save_res['error_line_no']);
            }
        } else {
            $this->Team->TeamMember->commit();
            $result['msg'] = __("%s members updated.", $save_res['success_count']);
            //Cacheをすべて削除
            Cache::clear(false, 'team_info');
        }
        return $this->_ajaxGetResponse($result);
    }

    function download_team_members_csv()
    {
        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        $this->layout = false;
        $filename = 'team_members_' . date('YmdHis');

        //見出し
        $th = $this->Team->TeamMember->_getCsvHeading();
        $td = $this->Team->TeamMember->getAllMembersCsvData();

        $this->set(compact('filename', 'th', 'td'));
        $this->_setResponseCsv($filename);
    }

    function ajax_upload_final_evaluations_csv()
    {
        $evaluate_term_id = Hash::get($this->request->params, 'named.evaluate_term_id');
        $this->request->allowMethod('post');
        $result = [
            'error' => false,
            'css'   => 'alert-success',
            'title' => __("Last evaluation is completed."),
            'msg'   => '',
        ];
        $this->_ajaxPreProcess('post');
        $csv = $this->Csv->convertCsvToArray($this->request->data['Team']['csv_file']['tmp_name']);
        $this->Team->TeamMember->begin();
        $save_res = $this->Team->TeamMember->updateFinalEvaluationFromCsv($csv, $evaluate_term_id);
        if ($save_res['error']) {
            $this->Team->TeamMember->rollback();
            $result['error'] = true;
            $result['css'] = 'alert-danger';
            $result['msg'] = $save_res['error_msg'];
            if ($save_res['error_line_no'] == 0) {
                $result['title'] = __("Error in extension data.");
            } else {
                $result['title'] = __("Error in the column %s (Column number included in text)",
                    $save_res['error_line_no']);
            }
        } else {
            $this->Team->TeamMember->commit();
            $result['msg'] = __("%s people's last evaluations are completed.", $save_res['success_count']);
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_DONE_FINAL, $evaluate_term_id);
            Cache::clear(false, 'team_info');
        }
        return $this->_ajaxGetResponse($result);
    }

    function download_final_evaluations_csv()
    {
        $evaluate_term_id = Hash::get($this->request->params, 'named.evaluate_term_id');
        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        $this->layout = false;
        $filename = 'final_evaluations_' . date('YmdHis');

        //見出し
        $th = $this->Team->TeamMember->_getCsvHeadingEvaluation();
        $td = $this->Team->TeamMember->getAllEvaluationsCsvData($evaluate_term_id, $team_id);

        $this->set(compact('filename', 'th', 'td'));
        $this->_setResponseCsv($filename);
    }

    public function ajax_switch_team()
    {
        $newTeamId = Hash::get($this->request->params, 'named.team_id');
        $this->layout = 'ajax';
        Configure::write('debug', 0);
        $redirect_url = Router::url("/", true);
        $this->set(compact("redirect_url"));
        if (!$newTeamId || !$this->request->is('ajax')) {
            $this->Notification->outError(__("Invalid access."));
            return $this->render();
        }
        $userId = $this->Auth->user('id');
        $sessionId = $this->Session->id();
        $previousTeamId = $this->Session->read('current_team_id');

        //チーム所属チェック
        $my_teams = $this->Team->TeamMember->getActiveTeamList($userId);
        if (!array_key_exists($newTeamId, $my_teams)) {
            $this->Notification->outError(__("You are not a member of this team."));
            return $this->render();
        }
        try {
            $jwtToken = $this->GlRedis->getMapSesAndJwt($previousTeamId, $userId, $sessionId);
            $newJwtAuth = $this->resetAuth($userId, $newTeamId,
                AccessAuthenticator::verify($jwtToken)->getJwtAuthentication());
            $this->GlRedis->delMapSesAndJwt($previousTeamId, $userId, $sessionId);
            $this->GlRedis->saveMapSesAndJwtWithToken($newTeamId, $userId, $newJwtAuth->token(), $sessionId);
        } catch (Exception $exception) {
            // If user does not have valid JWT token in Redis
            $this->GlRedis->delMapSesAndJwt($previousTeamId, $userId, $sessionId);
            $this->_switchTeam($newTeamId, $userId);
            $newJwtAuth = $this->GlRedis->saveMapSesAndJwt($newTeamId, $userId, $sessionId);
        } finally {
            $this->set('jwt_token', $newJwtAuth->token());
        }
        $this->Notification->outSuccess(__("Changed team to %s.", $my_teams[$newTeamId]));
        return $this->render();
    }

    function change_freeze_status()
    {
        $termId = $this->request->params['named']['evaluate_term_id'];
        $this->request->allowMethod('post');
        try {
            $res = $this->Team->Term->changeFreezeStatus($termId);
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($this->referer());
        }
        if ($res['Term']['evaluate_status'] == Term::STATUS_EVAL_FROZEN) {
            $this->Notification->outSuccess(__("Evaluation suspended."));
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_FREEZE,
                $this->Team->Term->getCurrentTermId());
        } else {
            $this->Notification->outSuccess(__("Removed evaluation suspension."));
        }
        CAche::clear(false, 'team_info');
        return $this->redirect($this->referer());
    }

    function main()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $current_global_menu = "team";
        // グルーブの絞り込みが選択された場合
        $this->set(compact('current_global_menu'));
        return $this->render();
    }

    /**
     * グループビジョン一覧取得
     *
     * @param int $teamId
     * @param int $activeFlg
     *
     * @return CakeResponse
     */
    function ajax_get_group_vision(int $teamId, int $activeFlg = 1): CakeResponse
    {
        $this->_ajaxPreProcess();
        App::import('Service', 'VisionService');
        /** @var VisionService $VisionService */
        $VisionService = ClassRegistry::init('VisionService');
        $group_vision_list = $VisionService->buildGroupVisionListForResponse($teamId, $activeFlg);
        return $this->_ajaxGetResponse($group_vision_list);
    }

    /**
     * グループビジョンアーカイブ設定
     *
     * @param     $group_vision_id
     * @param int $active_flg
     *
     * @return CakeResponse
     */
    function ajax_set_group_vision_archive($group_vision_id, $active_flg = 1)
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->GroupVision->setGroupVisionActiveFlag($group_vision_id, $active_flg);
        return $this->_ajaxGetResponse($res);
    }

    /**
     * 所属のグループ情報を取得
     *
     * @param $team_id
     * @param $user_id
     *
     * @return CakeResponse
     */
    function ajax_get_login_user_group_id($team_id, $user_id)
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->Group->MemberGroup->getMyGroupList();
        return $this->_ajaxGetResponse($res);
    }

    /**
     * グループビジョンの削除
     *
     * @param int $groupVisionId
     *
     * @return CakeResponse
     */
    function ajax_delete_group_vision(int $groupVisionId): CakeResponse
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->GroupVision->deleteGroupVision($groupVisionId);
        return $this->_ajaxGetResponse($res);
    }

    /**
     * チームビジョンの詳細を取得
     *
     * @param int $teamVisionId
     * @param int $activeFlg
     *
     * @return CakeResponse
     */
    function ajax_get_team_vision_detail(int $teamVisionId, int $activeFlg): CakeResponse
    {
        $this->_ajaxPreProcess();
        App::import('Service', 'VisionService');
        /** @var VisionService $VisionService */
        $VisionService = ClassRegistry::init('VisionService');
        $team_vision_detail = $VisionService->buildTeamVisionDetailForResponse($teamVisionId, $activeFlg);
        return $this->_ajaxGetResponse($team_vision_detail);
    }

    /**
     * グループビジョンの詳細を取得
     *
     * @param int $groupVisionId
     * @param int $activeFlg
     *
     * @return CakeResponse
     */
    function ajax_get_group_vision_detail(int $groupVisionId, int $activeFlg): CakeResponse
    {
        $this->_ajaxPreProcess();
        App::import('Service', 'VisionService');
        /** @var VisionService $VisionService */
        $VisionService = ClassRegistry::init('VisionService');
        $group_vision_detail = $VisionService->buildGroupVisionDetailForResponse($groupVisionId, $activeFlg);
        return $this->_ajaxGetResponse($group_vision_detail);
    }

    function ajax_team_admin_user_check()
    {
        $this->_ajaxPreProcess();
        $is_admin_user = true;
        $team_id = $this->Session->read('current_team_id');
        $user_id = $this->Auth->user('id');
        try {
            $this->Team->TeamMember->adminCheck($team_id, $user_id);
        } catch (RuntimeException $e) {
            $is_admin_user = false;
        }
        return $this->_ajaxGetResponse(['is_admin_user' => $is_admin_user]);
    }

    /**
     * チームビジョンの削除
     *
     * @param int $teamVisionId
     *
     * @return CakeResponse
     */
    function ajax_delete_team_vision(int $teamVisionId): CakeResponse
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->TeamVision->deleteTeamVision($teamVisionId);
        return $this->_ajaxGetResponse($res);
    }

    /**
     * チームビジョンの一覧取得
     *
     * @param int $teamId
     * @param int $activeFlg
     *
     * @return CakeResponse
     */
    function ajax_get_team_vision(int $teamId, int $activeFlg = 1): CakeResponse
    {
        $this->_ajaxPreProcess();
        App::import('Service', 'VisionService');
        /** @var VisionService $VisionService */
        $VisionService = ClassRegistry::init('VisionService');
        $team_vision_list = $VisionService->buildTeamVisionListForResponse($teamId, $activeFlg);
        return $this->_ajaxGetResponse($team_vision_list);
    }

    /**
     * チームビジョンをアーカイブする
     *
     * @param int $teamArchiveId
     * @param int $activeFlg
     *
     * @return CakeResponse
     */
    function ajax_set_team_vision_archive(int $teamArchiveId, int $activeFlg = 1): CakeResponse
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->TeamVision->setTeamVisionActiveFlag($teamArchiveId, $activeFlg);
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_team_member_init()
    {
        $this->_ajaxPreProcess();
        // ログインユーザーは管理者なのか current_team_idのadmin_flgがtrueを検索
        $current_team_id = $this->Session->read('current_team_id');
        $login_user_id = $this->Auth->user('id');
        $login_user_admin_flg = $this->Team->TeamMember->getLoginUserAdminFlag($current_team_id, $login_user_id);
        $admin_user_cnt = $this->Team->TeamMember->getAdminUserCount($current_team_id);

        $res = [
            'current_team_id'      => $current_team_id,
            'admin_user_cnt'       => $admin_user_cnt,
            'login_user_id'        => $login_user_id,
            'login_user_admin_flg' => $login_user_admin_flg,
            'login_user_language'  => $this->Session->read('Auth.User.language'),
        ];
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_team_member()
    {
        $this->_ajaxPreProcess();
        $team_id = $this->Session->read('current_team_id');
        $user_info = $this->Team->TeamMember->selectMemberInfo($team_id);
        $res = [
            'user_info' => $user_info,
        ];
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_group_member($group_id = '')
    {
        $this->_ajaxPreProcess();
        $team_id = $this->Session->read('current_team_id');
        $user_info = $this->Team->TeamMember->selectGroupMemberInfo($team_id, $group_id);
        $res = [
            'user_info' => $user_info,
        ];
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_current_team_group_list()
    {
        $this->_ajaxPreProcess();
        $team_id = $this->Session->read('current_team_id');
        // グループ名を取得
        $group_info = $this->Team->Group->findAllList($team_id, true);
        return $this->_ajaxGetResponse($group_info);
    }

    function ajax_get_current_team_admin_list()
    {
        $this->_ajaxPreProcess();
        $team_id = $this->Session->read('current_team_id');
        $user_info = $this->Team->TeamMember->selectAdminMemberInfo($team_id);
        $res = [
            'user_info' => $user_info,
        ];
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_current_not_2fa_step_user_list()
    {
        $this->_ajaxPreProcess();
        $team_id = $this->Session->read('current_team_id');
        $user_info = $this->Team->TeamMember->select2faStepMemberInfo($team_id);
        $res = [
            'user_info' => $user_info,
        ];
        return $this->_ajaxGetResponse($res);
    }

    function ajax_inactivate_team_member($teamMemberId)
    {
        $this->_ajaxPreProcess();

        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        try {
            $res = $TeamMemberService->inactivate($teamMemberId);
        } catch (Exception $exception) {
            GoalousLog::error(sprintf('Failed to inactivate team member: %s', AppUtil::jsonOneLine([
                'teams.id'        => $this->current_team_id,
                'team_members.id' => $teamMemberId,
            ])));
            $res = false;
        }
        GoalousLog::info(sprintf('inactivate team member: %s', AppUtil::jsonOneLine([
            'teams.id'        => $this->current_team_id,
            'team_members.id' => $teamMemberId,
        ])));
        PaymentUtil::logCurrentTeamChargeUsers($this->current_team_id);
        return $this->_ajaxGetResponse($res);
    }

    /**
     * TODO: delete this API if used place migrated to
     * /api/v1/invitations/reInvite
     *
     * @param $user_id
     * @param $action_flg
     *
     * @return CakeResponse|null
     * @deprecated
     *
     */
    function ajax_invite_setting($user_id, $action_flg)
    {
        CakeLog::info(sprintf("[%s]%s data:%s", __METHOD__,
            'called deprecated method',
            AppUtil::varExportOneLine([
                'user_id'    => $user_id,
                'action_flg' => $action_flg,
            ])));
        return $this->_ajaxGetResponse([
            'title' => __("Some error occurred. Please try again from the start."),
            'error' => true,
        ]);
    }

    function ajax_set_current_team_admin_user_flag($member_id, $adminFlg)
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->TeamMember->setAdminUserFlag($member_id, $adminFlg);
        return $this->_ajaxGetResponse($res);
    }

    function ajax_set_current_team_evaluation_flag($member_id, $evaluation_flg)
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->TeamMember->setEvaluationFlag($member_id, $evaluation_flg);
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_invite_member_list()
    {
        $this->_ajaxPreProcess();
        $team_id = $this->Session->read('current_team_id');
        $invite_member_list = $this->Team->Invite->getInviteUserList($team_id);
        $res = [
            'user_info' => $invite_member_list,
        ];
        return $this->_ajaxGetResponse($res);
    }

    function ajax_get_check_if_payment_timing() {
        $this->_ajaxPreProcess();
        $team_id = $this->Session->read('current_team_id');

        $PaymentTiming = ClassRegistry::init("PaymentTiming");
        $payment_timing_flag = $PaymentTiming->checkIfPaymentTiming($team_id);
        $res = [
            'payment_timing_flag' => $payment_timing_flag,
        ];
        return $this->_ajaxGetResponse($res);
    }

    /**
     * チームビジョンを追加
     */
    function add_team_vision()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        $redirectTo = "/teams/main#/vision/" . $this->current_team_id;

        try {
            $this->Team->TeamMember->adminCheck();
            if (!empty($this->Team->TeamVision->getTeamVision($this->current_team_id, true))) {
                throw new RuntimeException(__("Team vision already exists, new one cannot be made."));
            }
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($redirectTo);
        }

        if ($this->request->is('get')) {
            return $this->render();
        }

        if ($this->Team->TeamVision->saveTeamVision($this->request->data)) {
            $this->Notification->outSuccess(__("Team vision is added."));
        } else {
            $this->Notification->outError(__("Failed to save team vision."));
        }
        return $this->redirect($redirectTo);
    }

    /**
     * チームビジョンを変更
     *
     * @return mixed
     */
    function edit_team_vision()
    {
        $redirectTo = "/teams/main#/vision/" . $this->current_team_id;
        $this->layout = LAYOUT_ONE_COLUMN;
        try {
            $this->Team->TeamMember->adminCheck();
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($redirectTo);
        }

        if (!$team_vision_id = Hash::get($this->request->params, 'named.team_vision_id')) {
            $this->Notification->outError(__("Invalid screen transition."));
            return $this->redirect($redirectTo);
        }
        if (!$this->Team->TeamVision->exists($team_vision_id)) {
            $this->Notification->outError(__("Page does not exist."));
            return $this->redirect($redirectTo);
        }

        if ($this->request->is('get')) {
            $this->request->data = $this->Team->TeamVision->findById($team_vision_id);
            return $this->render();
        }

        if ($this->Team->TeamVision->saveTeamVision($this->request->data)) {
            $this->Notification->outSuccess(__("Updated team vision."));
        } else {
            $this->Notification->outError(__("Failed to save team vision."));
        }
        return $this->redirect($redirectTo);
    }

    /**
     * グループビジョンの作成
     * # グループビジョンを作成できるユーザ
     * - チーム管理者
     * - グループに所属しているメンバー
     *
     * @return mixed
     */
    function add_group_vision()
    {
        App::import('Service', 'VisionService');
        /** @var VisionService $VisionService */
        $VisionService = ClassRegistry::init('VisionService');

        $this->layout = LAYOUT_ONE_COLUMN;
        $groupList = $VisionService->getGroupListAddableVision();
        $redirectTo = "/teams/main#/group_vision/" . $this->current_team_id;

        if (empty($groupList)) {
            $this->Notification->outError(__("Unable to create group vision as you don't belong to this group or the vision already exists."));
            return $this->redirect($redirectTo);
        }

        $this->set(compact('groupList'));

        if ($this->request->is('get')) {
            return $this->render();
        }

        if ($this->Team->GroupVision->saveGroupVision($this->request->data)) {
            $this->Notification->outSuccess(__("Added group vision."));
        } else {
            $this->Notification->outError(__("Failed to save group vision."));
        }
        return $this->redirect($redirectTo);
    }

    /**
     * グループビジョンの変更
     * # グループビジョンを作成できるユーザ
     * - チーム管理者
     * - そのグループに所属しているメンバー
     *
     * @return mixed
     */
    function edit_group_vision()
    {
        App::import('Service', 'VisionService');
        /** @var VisionService $VisionService */
        $VisionService = ClassRegistry::init('VisionService');

        $this->layout = LAYOUT_ONE_COLUMN;

        $redirectTo = "/teams/main#/group_vision/" . $this->current_team_id;

        if (!$groupVisionId = Hash::get($this->request->params, 'named.group_vision_id')) {
            $this->Notification->outError(__("Invalid screen transition."));
            return $this->redirect($redirectTo);
        }
        if (!$VisionService->existsGroupVision($groupVisionId)) {
            $this->Notification->outError(__("Page does not exist."));
            return $this->redirect($redirectTo);
        }

        //変更できるのはチーム管理者もしくは、そのグループに所属しているメンバ
        if (!$VisionService->hasPermissionToEdit($groupVisionId)) {
            $this->Notification->outError(__("You don't have a permission."));
            return $this->redirect($redirectTo);
        }
        if ($this->request->is('get')) {
            $this->request->data = $this->Team->GroupVision->findWithGroupById($groupVisionId);
            return $this->render();
        }

        if ($this->Team->GroupVision->saveGroupVision($this->request->data)) {
            $this->Notification->outSuccess(__("Updated group vision."));
        } else {
            $this->Notification->outError(__("Failed to save group vision."));
        }
        return $this->redirect($redirectTo);
    }

    /**
     * メールアドレスが招待可能なものか判定
     *
     * @return CakeResponse
     */
    public function ajax_validate_email_can_invite()
    {
        $this->_ajaxPreProcess();
        $email = $this->request->query('email');
        $valid = false;
        $message = '';
        if ($email) {
            // メールアドレスだけ validate
            $this->User->Email->create(['email' => $email]);
            $this->User->Email->validate = [
                'email' => [
                    'maxLength' => ['rule' => ['maxLength', 255]],
                    'notBlank'  => ['rule' => 'notBlank',],
                    'email'     => ['rule' => ['email'],],
                ],
            ];
            $this->User->Email->validates(['fieldList' => ['email']]);
            if ($this->User->Email->validationErrors) {
                $message = $this->User->Email->validationErrors['email'][0];
            } elseif ($this->User->Email->getEmailsBelongTeamByEmail($email)) {
                $message = __('This email address has already been joined.');
            } else {
                $valid = true;
            }
        }
        return $this->_ajaxGetResponse([
            'valid'   => $valid,
            'message' => $message
        ]);
    }

    /**
     * インサイト
     */
    public function insight()
    {
        $this->layout = LAYOUT_TWO_COLUMN;
        $this->set('current_global_menu', 'team');

        // システム管理者のためのセットアップ
        $this->_setupForSystemAdminInsight();

        // デフォルトのタイムゾーン（日本時間）
        $timezone = 9;

        // 日付範囲
        $date_info = $this->_getInsightDateInfo($timezone);
        $this->set($date_info);

        // 全グループ
        $group_list = $this->Team->Group->findAllList($this->current_team_id);
        $this->set('group_list', $group_list);

        // システム管理者のためのクリーンアップ
        $this->_cleanupForSystemAdminInsight();
    }

    /**
     * インサイト 集計結果
     *
     * @return CakeResponse
     */
    public function ajax_get_insight()
    {
        $this->_ajaxPreProcess();

        $date_range = $this->request->query('date_range');
        $group_id = $this->request->query('group');
        $timezone = $this->request->query('timezone');

        // システム管理者のためのセットアップ
        $this->_setupForSystemAdminInsight();

        // 日付範囲
        $date_info = $this->_getInsightDateInfo($timezone);

        $start_date = $end_date = $date_range_type = null;
        $isPast = false;
        if (!isset($date_info['date_ranges'][$date_range])) {
            foreach ($date_info['date_ranges']['past_terms'] as $term) {
                if ($term['id'] == $date_range) {
                    $isPast = true;
                    $start_date = $term['start'];
                    $end_date = $term['end'];
                    $date_range_type = 'term';
                    break;
                }
            }
            if (!$isPast) {
                throw new NotFoundException();
            }
        } else {
            // 集計 開始日付, 終了日付
            $start_date = $date_info['date_ranges'][$date_range]['start'];
            $end_date = $date_info['date_ranges'][$date_range]['end'];
            // 'week' or 'month' or 'term'
            $date_range_type = $this->_insightGetDateRangeType($date_range);
        }

        // 集計 開始日付, 終了日付
        $this->set('start_date', $start_date);
        $this->set('end_date', $end_date);

        // 今週、今月、今期の場合に true
        $is_current = $this->_insightIsCurrentDateRange($date_range);

        // 指定期間 + １つ前の期間 のデータ
        $insights = [];

        // 今週、今月、今期 の場合
        // 最新データが変動するのでキャッシュの有効期限を１日にする
        $cache_expire = $is_current ? DAY : WEEK;

        // 週単位、月単位の場合
        if ($date_range_type == 'week' || $date_range_type == 'month') {
            $targetStartDate = $start_date;
            $targetEndDate = $this->_insightAdjustEndDate($end_date, $date_info['today']);
            for ($i = 0; $i < 2; $i++) {
                // 指定範囲のデータ
                $insights[] = $this->_getInsightData($targetStartDate, $targetEndDate, $timezone, $group_id,
                    $cache_expire);

                $next_target = null;
                if ($date_range_type == 'week') {
                    $next_target = $this->Team->TeamInsight->getWeekRangeDate($targetStartDate, ['offset' => -1]);
                } elseif ($date_range_type == 'month') {
                    $next_target = $this->Team->TeamInsight->getMonthRangeDate($targetStartDate,
                        ['offset' => -1]);
                }
                $targetStartDate = $next_target['start'];
                $targetEndDate = $next_target['end'];

                // 古いデータのキャッシュ有効期限は１週間
                $cache_expire = WEEK;
            }
        } // 期単位の場合
        elseif ($date_range_type == 'term') {
            $all_terms = $this->Team->Term->getAllTerm();
            $start_term_id = null;
            if ($date_range == 'prev_term') {
                $start_term_id = $this->Team->Term->getPreviousTermId();
            } elseif ($date_range == 'current_term') {
                $start_term_id = $this->Team->Term->getCurrentTermId();
            } else {
                $start_term_id = $date_range;
            }
            $skip = true;
            foreach ($all_terms as $term_id => $v) {
                // 集計対象期間まで読み飛ばし
                if ($skip && $term_id != $start_term_id) {
                    continue;
                }
                $skip = false;

                $insights[] = $this->_getInsightData(
                    $v['start_date'],
                    $this->_insightAdjustEndDate($v['end_date'], $date_info['today']),
                    $timezone,
                    $group_id,
                    $cache_expire);

                if (count($insights) >= 2) {
                    break;
                }

                // 古いデータのキャッシュ有効期限は１週間
                $cache_expire = WEEK;
            }
        }

        // １つ前の期間との比較
        foreach ($insights[0] as $k => $v) {
            if (!empty($insights[1][$k]) && is_numeric($insights[1][$k])) {
                $cmp_key = $k . "_cmp";
                if (strpos($k, '_percent') !== false) {
                    $insights[0][$cmp_key] = $insights[0][$k] - $insights[1][$k];
                } else {
                    $insights[0][$cmp_key] = $insights[0][$k] / $insights[1][$k] * 100.0 - 100.0;
                }
                $insights[0][$cmp_key] = abs($insights[0][$cmp_key]) >= 1 ?
                    round($insights[0][$cmp_key]) : round($insights[0][$cmp_key], 1);
            }
        }
        $this->set('insights', $insights);

        $response = $this->render('Team/insight_result');
        $html = $response->__toString();

        // システム管理者のためのクリーンアップ
        $this->_cleanupForSystemAdminInsight();

        return $this->_ajaxGetResponse(['html' => $html]);
    }

    /**
     * インサイトのグラフデータ
     */
    public function ajax_get_insight_graph()
    {
        $this->_ajaxPreProcess();

        $date_range = $this->request->query('date_range');
        $group_id = $this->request->query('group');
        $timezone = $this->request->query('timezone');
        $graph_type = $this->request->query('graph_type');

        // システム管理者のためのセットアップ
        $this->_setupForSystemAdminInsight();

        // 日付範囲
        $date_info = $this->_getInsightDateInfo($timezone);

        // あらかじめ決められた期間でなければエラー
        $start_date = $end_date = $date_range_type = null;
        $isPast = false;
        if (!isset($date_info['date_ranges'][$date_range])) {
            foreach ($date_info['date_ranges']['past_terms'] as $term) {
                if ($term['id'] == $date_range) {
                    $isPast = true;
                    $start_date = $term['start'];
                    $end_date = $term['end'];
                    $date_range_type = 'term';
                    break;
                }
            }
            if (!$isPast) {
                throw new NotFoundException();
            }
        } else {
            // 集計 開始日付, 終了日付
            $start_date = $date_info['date_ranges'][$date_range]['start'];
            $end_date = $date_info['date_ranges'][$date_range]['end'];
            // 'week' or 'month' or 'term'
            $date_range_type = $this->_insightGetDateRangeType($date_range);
        }

        // 集計 開始日付, 終了日付
        $this->set('start_date', $start_date);
        $this->set('end_date', $end_date);

        // 今週、今月、今期の場合に true
        $is_current = $this->_insightIsCurrentDateRange($date_range);
        // 'week' or 'month' or 'term'

        // 集計期間とグラフ種類の組み合わせが有効かチェック
        if ($date_range_type == 'term') {
            if (!in_array($graph_type, ['term', 'month'])) {
                throw new NotFoundException();
            }
        } elseif ($date_range_type == 'month') {
            if (!in_array($graph_type, ['month', 'week'])) {
                throw new NotFoundException();
            }
        } elseif ($date_range_type == 'week') {
            if (!in_array($graph_type, ['week', 'day'])) {
                throw new NotFoundException();
            }
        }

        // ６ 週/月/期 前までのデータ
        $insights = [];

        // グラフ種類が「日」の場合
        if ($graph_type == 'day') {
            // 今週、先週 の場合に日別で表示する
            $max_days = 7;

            $target_start_date = $start_date;
            $target_end_date = $start_date;   // １日分のデータ
            $target_start_date_time = strtotime($target_start_date);
            for ($i = 0; $i < $max_days; $i++) {
                // キャッシュの有効期限
                // 古いデータは１週間
                // 当日以降のデータはアクセス日の24時まで（当日より後はデータは存在しないが処理単純化とデータ構造を合わせるため）
                $cache_expire = ($target_start_date_time < $date_info['today_time']) ?
                    WEEK : DAY - (REQUEST_TIMESTAMP + $date_info['time_adjust'] - $date_info['today_time']);
                $insights[] = $this->_getInsightData($target_start_date, $target_end_date, $timezone, $group_id,
                    $cache_expire);
                $target_start_date_time += DAY;
                $target_start_date = date('Y-m-d', $target_start_date_time);
                $target_end_date = $target_start_date;
            }
        }

        // グラフ種別が「週」の場合
        if ($graph_type == 'week') {
            // 今週、先週 の場合
            $max_weeks = 6;
            $target_start_date = $start_date;
            $target_end_date = $this->_insightAdjustEndDate($end_date, $date_info['today']);
            $limit = 0;

            // 今月、前月の場合
            if ($date_range_type == 'month') {
                $week = $this->Team->TeamInsight->getWeekRangeDate($end_date);
                $target_start_date = $week['start'];
                $target_end_date = $week['end'];
                $limit = strtotime($start_date);
            }

            for ($i = 0; $i < $max_weeks; $i++) {
                // キャッシュの有効期限
                // 古いデータは１週間
                // 今週のデータはアクセス日の24時まで
                $cache_expire = (strtotime($target_end_date) < $date_info['today_time']) ?
                    WEEK : DAY - (REQUEST_TIMESTAMP + $date_info['time_adjust'] - $date_info['today_time']);
                // 指定範囲のデータ
                array_unshift($insights,
                    $this->_getInsightData($target_start_date, $target_end_date, $timezone, $group_id,
                        $cache_expire));

                $next_target = $this->Team->TeamInsight->getWeekRangeDate($target_start_date, ['offset' => -1]);
                $target_start_date = $next_target['start'];
                $target_end_date = $next_target['end'];

                if (strtotime($target_end_date) < $limit) {
                    break;
                }
            }
        }

        // グラフ種別が「月」の場合
        if ($graph_type == 'month') {
            // 今月、先月の場合
            // 対象月から６ヶ月前までのデータを取得する
            $max_months = 6;
            $target_start_date = $start_date;
            $target_end_date = $this->_insightAdjustEndDate($end_date, $date_info['today']);
            $limit = 0;

            // 今期、前期の場合
            // 期内の月データを最大12ヶ月分取得する
            if ($date_range_type == 'term') {
                $max_months = 12;
                $target_end_date = $end_date;
                $target_start_date = date('Y-m-01', strtotime($target_end_date));
                $limit = strtotime($start_date);
            }

            for ($i = 0; $i < $max_months; $i++) {
                // キャッシュの有効期限
                // 古いデータは１週間
                // 今月のデータはアクセス日の24時まで
                $cache_expire = (strtotime($target_end_date) < strtotime($date_info['today'])) ?
                    WEEK : DAY - (REQUEST_TIMESTAMP + $date_info['time_adjust'] - $date_info['today_time']);

                array_unshift($insights,
                    $this->_getInsightData($target_start_date, $target_end_date, $timezone, $group_id,
                        $cache_expire));

                $next_target = $this->Team->TeamInsight->getMonthRangeDate($target_start_date, ['offset' => -1]);
                $target_start_date = $next_target['start'];
                $target_end_date = $next_target['end'];

                if (strtotime($target_end_date) < $limit) {
                    break;
                }
            }
        }

        // グラフ種別が「期」の場合
        if ($graph_type == 'term') {
            // 今期、前期 の場合に過去６期分のデータを取得
            $max_terms = 6;

            $all_terms = $this->Team->Term->getAllTerm();
            $start_term_id = null;
            if ($date_range == 'prev_term') {
                $start_term_id = $this->Team->Term->getPreviousTermId();
            } elseif ($date_range == 'current_term') {
                $start_term_id = $this->Team->Term->getCurrentTermId();
            } else {
                $start_term_id = $date_range;
            }

            // キャッシュの有効期限
            // 古いデータは１週間
            // 今期のデータはアクセス日の24時まで
            $cache_expire = $is_current ?
                DAY - (REQUEST_TIMESTAMP + $date_info['time_adjust'] - $date_info['today_time']) : WEEK;

            $skip = true;
            foreach ($all_terms as $term_id => $v) {
                // 集計対象期間まで読み飛ばし
                if ($skip && $term_id != $start_term_id) {
                    continue;
                }
                $skip = false;

                array_unshift($insights,
                    $this->_getInsightData(
                        date('Y-m-d', $v['start_date'] + $date_info['time_adjust']),
                        $this->_insightAdjustEndDate(date('Y-m-d',
                            $v['end_date'] + $date_info['time_adjust']),
                            $date_info['today']),
                        $timezone,
                        $group_id,
                        $cache_expire));

                if (count($insights) >= $max_terms) {
                    break;
                }
                // 古いデータのキャッシュ有効期限は１週間
                $cache_expire = WEEK;
            }
        }

        // システム管理者のためのクリーンアップ
        $this->_cleanupForSystemAdminInsight();

        return $this->_ajaxGetResponse(['insights' => $insights]);
    }

    /**
     * サークル利用状況
     */
    public function insight_circle()
    {
        $this->layout = LAYOUT_TWO_COLUMN;
        $this->set('current_global_menu', 'team');

        // システム管理者のためのセットアップ
        $this->_setupForSystemAdminInsight();

        // デフォルトのタイムゾーン（日本時間）
        $timezone = 9;

        // 日付範囲
        $date_info = $this->_getInsightDateInfo($timezone);
        $this->set($date_info);

        // システム管理者のためのクリーンアップ
        $this->_cleanupForSystemAdminInsight();
    }

    /**
     * サークル利用状況 集計結果
     *
     * @return CakeResponse
     */
    public function ajax_get_insight_circle()
    {
        $this->_ajaxPreProcess();

        $date_range = $this->request->query('date_range');
        $timezone = $this->request->query('timezone');

        // システム管理者のためのセットアップ
        $this->_setupForSystemAdminInsight();

        // 日付範囲
        $date_info = $this->_getInsightDateInfo($timezone);

        // あらかじめ決められた期間でなければエラー
        $start_date = $end_date = $date_range_type = null;
        $isPast = false;
        if (!isset($date_info['date_ranges'][$date_range])) {
            foreach ($date_info['date_ranges']['past_terms'] as $term) {
                if ($term['id'] == $date_range) {
                    $isPast = true;
                    $start_date = $term['start'];
                    $end_date = $term['end'];
                    $date_range_type = 'term';
                    break;
                }
            }
            if (!$isPast) {
                throw new NotFoundException();
            }
        } else {
            // 集計 開始日付, 終了日付
            $start_date = $date_info['date_ranges'][$date_range]['start'];
            $end_date = $date_info['date_ranges'][$date_range]['end'];
            // 'week' or 'month' or 'term'
            $date_range_type = $this->_insightGetDateRangeType($date_range);
        }

        $this->set('start_date', $start_date);
        $this->set('end_date', $end_date);

        // 今週、今月、今期の場合に true
        $is_current = $this->_insightIsCurrentDateRange($date_range);
        // 今週、今月、今期 の場合
        // 最新データが変動するのでキャッシュの有効期限を１日にする
        $cache_expire = $is_current ? DAY : WEEK;

        // 指定範囲のデータ
        $circle_insights = $this->_getCircleInsightData(
            $start_date,
            $this->_insightAdjustEndDate($end_date, $date_info['today']),
            $timezone,
            $cache_expire);

        // 指定範囲の１つ前の期間のデータ
        $circle_insights2 = null;
        $target_start_date = null;
        $target_end_date = null;
        if ($date_range_type == 'week') {
            $prev_week2 = $this->Team->TeamInsight->getWeekRangeDate($start_date, ['offset' => -1]);
            $target_start_date = $prev_week2['start'];
            $target_end_date = $prev_week2['end'];
        } elseif ($date_range_type == 'month') {
            $prev_month2 = $this->Team->TeamInsight->getMonthRangeDate($start_date, ['offset' => -1]);
            $target_start_date = $prev_month2['start'];
            $target_end_date = $prev_month2['end'];
        } elseif ($date_range_type == 'term') {
            $prev_term2 = null;
            if ($date_range == 'current_term') {
                // 前期の日付
                $prev_term2 = $this->Team->Term->getPreviousTermData();
            } elseif ($date_range == 'prev_term') {
                // 前々期の日付
                $prev_term_id = $isPast ? $date_range : $this->Team->Term->getPreviousTermId();
                $all_terms = $this->Team->Term->getAllTerm();
                $found = false;
                foreach ($all_terms as $term_id => $v) {
                    if ($found) {
                        $prev_term2 = $v;
                        break;
                    }
                    if ($term_id == $prev_term_id) {
                        $found = true;
                    }
                }
            }
            if ($prev_term2) {
                $target_start_date = date('Y-m-d', $prev_term2['start_date'] + $date_info['time_adjust']);
                $target_end_date = date('Y-m-d', $prev_term2['end_date'] + $date_info['time_adjust']);
            }
        }
        if ($target_start_date && $target_end_date) {
            $circle_insights2 = $this->_getCircleInsightData($target_start_date, $target_end_date, $timezone, WEEK);
        }

        $circle_list = $this->Team->Circle->getList();
        foreach ($circle_insights as $circle_id => $insight) {
            // 前週-前々週 or 前月-前々月 の比較
            foreach ($insight as $k => $v) {
                if (isset($circle_insights2[$circle_id][$k]) && $circle_insights2[$circle_id][$k]) {
                    $cmp_key = $k . "_cmp";
                    if (strpos($k, '_percent') !== false) {
                        $insight[$cmp_key] = $insight[$k] - $circle_insights2[$circle_id][$k];
                    } else {
                        $insight[$cmp_key] = $insight[$k] / $circle_insights2[$circle_id][$k] * 100.0 - 100.0;
                    }
                    $insight[$cmp_key] = abs($insight[$cmp_key]) >= 1 ?
                        round($insight[$cmp_key]) : round($insight[$cmp_key], 1);
                }
            }
            $circle_insights[$circle_id] = $insight;

            // サークル名
            $circle_insights[$circle_id]['name'] = $circle_list[$circle_id];
        }

        // sorting by columns
        if (!empty($this->request->query('sort_by'))) {
            $sort_by = $this->request->query('sort_by');
            $sort_type = 'desc';
            if (!empty($this->request->query('sort_type'))) {
                $sort_type = $this->request->query('sort_type');
            }

            uasort($circle_insights, function ($a, $b) use ($sort_by, $sort_type) {
                if ($a[$sort_by] == $b[$sort_by]) {
                    return 0;
                }
                $ret = ($a[$sort_by] > $b[$sort_by]) ? -1 : 1;
                if ($sort_type == 'asc') {
                    $ret = ($a[$sort_by] < $b[$sort_by]) ? -1 : 1;
                }
                return $ret;
            });
        }

        $this->set('circle_insights', $circle_insights);
        $this->set('sort_type', $sort_type);
        $this->set('sort_by', $sort_by);
        $response = $this->render('Team/insight_circle_result');
        $html = $response->__toString();

        // システム管理者のためのクリーンアップ
        $this->_cleanupForSystemAdminInsight();

        return $this->_ajaxGetResponse(['html' => $html]);
    }

    /**
     * ランキング
     */
    public function insight_ranking()
    {
        $this->layout = LAYOUT_TWO_COLUMN;
        $this->set('current_global_menu', 'team');

        // システム管理者のためのセットアップ
        $this->_setupForSystemAdminInsight();

        // デフォルトのタイムゾーン（日本時間）
        $timezone = 9;

        // 日付範囲
        $date_info = $this->_getInsightDateInfo($timezone);
        $this->set($date_info);

        // 全グループ
        $group_list = $this->Team->Group->findAllList($this->current_team_id);
        $this->set('group_list', $group_list);

        // システム管理者のためのクリーンアップ
        $this->_cleanupForSystemAdminInsight();
    }

    /**
     * ランキング 集計結果
     *
     * @return CakeResponse
     */
    public function ajax_get_insight_ranking()
    {
        $this->_ajaxPreProcess();

        $date_range = $this->request->query('date_range');
        $group_id = $this->request->query('group');
        $type = $this->request->query('type');
        $timezone = $this->request->query('timezone');
        $lastRank = !empty($this->request->query('last_rank')) ? intval($this->request->query('last_rank')) : 0;

        // システム管理者のためのセットアップ
        $this->_setupForSystemAdminInsight();

        // 日付範囲
        $date_info = $this->_getInsightDateInfo($timezone);

        $start_date = $end_date = null;
        $isPast = false;
        if (!isset($date_info['date_ranges'][$date_range])) {
            foreach ($date_info['date_ranges']['past_terms'] as $term) {
                if ($term['id'] == $date_range) {
                    $isPast = true;
                    $start_date = $term['start'];
                    $end_date = $term['end'];
                    break;
                }
            }
            if (!$isPast) {
                throw new NotFoundException();
            }
        } else {
            // 集計 開始日付, 終了日付
            $start_date = $date_info['date_ranges'][$date_range]['start'];
            $end_date = $date_info['date_ranges'][$date_range]['end'];
        }

        // 集計 開始日付, 終了日付
        $this->set('start_date', $start_date);
        $this->set('end_date', $end_date);
        $this->set('type', $type);
        $this->set('group', $group_id);
        $this->set('date_range', $date_range);
        $this->set('timezone', $timezone);

        // 今週、今月、今期の場合に true
        $is_current = $this->_insightIsCurrentDateRange($date_range);

        // 今週、今月、今期 の場合
        // 最新データが変動するのでキャッシュの有効期限を１日にする
        $cache_expire = $is_current ? DAY : WEEK;

        // ランキングデータ取得
        $ranking = [];
        switch ($type) {
            case 'action_goal_ranking':
                $rankings = $this->_getGoalRankingData($start_date,
                    $this->_insightAdjustEndDate($end_date, $date_info['today']),
                    $timezone,
                    $group_id,
                    $cache_expire);
                $ranking = $rankings[$type];
                foreach ($ranking as $k => $v) {
                    $ranking[$k] = ['count' => $v];
                }

                // ゴール情報取得
                $goal_ids = array_keys($rankings['action_goal_ranking']);
                $goals = $this->Goal->getGoalsWithUser($goal_ids);
                foreach ($goals as $goal) {
                    $ranking[$goal['Goal']['id']]['text'] = $goal['Goal']['name'];
                    $ranking[$goal['Goal']['id']]['Goal'] = $goal['Goal'];
                    $ranking[$goal['Goal']['id']]['url'] = Router::url([
                        'controller' => 'goals',
                        'action'     => 'view_krs',
                        'goal_id'    => $goal['Goal']['id']
                    ]);
                }
                break;

            case 'action_user_ranking':
            case 'post_user_ranking':
                $rankings = $this->_getUserRankingData($start_date,
                    $this->_insightAdjustEndDate($end_date, $date_info['today']),
                    $timezone,
                    $group_id,
                    $cache_expire);
                $ranking = $rankings[$type];
                foreach ($ranking as $k => $v) {
                    $ranking[$k] = ['count' => $v];
                }

                // ユーザーデータ取得
                $user_ids = array_keys($ranking);
                $users = $this->User->getUsersProf($user_ids);
                foreach ($users as $user) {
                    $ranking[$user['User']['id']]['text'] = $user['User']['display_username'];
                    $ranking[$user['User']['id']]['User'] = $user['User'];
                    $ranking[$user['User']['id']]['url'] = Router::url([
                        'controller' => 'users',
                        'action'     => 'view_goals',
                        'user_id'    => $user['User']['id']
                    ]);
                }
                break;

            case 'post_like_ranking':
            case 'action_like_ranking':
            case 'post_comment_ranking':
            case 'action_comment_ranking':
                $rankings = $this->_getPostRankingData($start_date,
                    $this->_insightAdjustEndDate($end_date, $date_info['today']),
                    $timezone,
                    $group_id,
                    $cache_expire);
                $ranking = $rankings[$type];
                foreach ($ranking as $k => $v) {
                    $ranking[$k] = ['count' => $v];
                }

                // 投稿情報取得
                $post_ids = array_keys($ranking);
                $posts = $this->Post->getPostsById($post_ids, ['include_action' => true, 'include_user' => true]);
                foreach ($posts as $post) {
                    $ranking[$post['Post']['id']]['text'] = $post['ActionResult']['id'] ?
                        $post['ActionResult']['name'] : $post['Post']['body'];
                    $ranking[$post['Post']['id']]['User'] = $post['User'];
                    $ranking[$post['Post']['id']]['url'] = Router::url([
                        'controller' => 'posts',
                        'action'     => 'feed',
                        'post_id'    => $post['Post']['id']
                    ]);
                }
                break;
        }

        // calculating rank logic
        // $count_rank with key as count and value as rank
        $count_rank = $filter_ranking = [];
        $rank = 1;
        $max_ranking_no = $lastRank + 30;
        foreach ($ranking as $rankKey => $rankArrVal) {
            if ($rank > $lastRank) {
                if (!isset($count_rank[$rankArrVal['count']])) {
                    if ($rank > $max_ranking_no) {
                        break;
                    }
                    $count_rank[$rankArrVal['count']] = $rank;
                    $ranking[$rankKey]['rank'] = $count_rank[$rankArrVal['count']];
                } else {
                    $ranking[$rankKey]['rank'] = $count_rank[$rankArrVal['count']];
                }
                $filter_ranking[$rankKey] = $ranking[$rankKey];
            }
            $rank++;
        }

        $this->set('ranking', $filter_ranking);
        $this->set('last_rank', $max_ranking_no);

        $response = $this->render('Team/insight_ranking_result');
        $html = $response->__toString();

        // システム管理者のためのクリーンアップ
        $this->_cleanupForSystemAdminInsight();

        return $this->_ajaxGetResponse(['html' => $html]);
    }

    /**
     * insight 系処理の日付データを返す
     *
     * @param $timezone
     *
     * @return array
     */
    protected function _getInsightDateInfo($timezone)
    {
        // 指定タイムゾーンの UTC からの差分秒数
        $time_adjust = intval($timezone * HOUR);
        // タイムゾーンを考慮した「本日」
        $today = date('Y-m-d', time() + $time_adjust);
        // 「本日」の 0 時のタイムスタンプ
        $today_time = strtotime($today);

        // 今週、先週、今月、先月、前期、今期 の start_date, end_date
        $date_ranges = [];
        $date_ranges['current_week'] = $this->Team->TeamInsight->getWeekRangeDate($today);
        $date_ranges['prev_week'] = $this->Team->TeamInsight->getWeekRangeDate($today, ['offset' => -1]);
        $date_ranges['current_month'] = $this->Team->TeamInsight->getMonthRangeDate($today);
        $date_ranges['prev_month'] = $this->Team->TeamInsight->getMonthRangeDate($today, ['offset' => -1]);
        $row = $this->Team->Term->getCurrentTermData();
        $date_ranges['current_term'] = [
            'start' => $row['start_date'],
            'end'   => $row['end_date'],
        ];
        $row = $this->Team->Term->getPreviousTermData();
        if (!empty($row)) {
            $date_ranges['prev_term'] = [
                'start' => $row['start_date'],
                'end'   => $row['end_date'],
            ];
            $rows = $this->Team->Term->getPreviousTermDataMore($row);
            $dateRangesMore = array();
            foreach ($rows as $r) {
                $dateRangesMore[] = array('id' => $r['id'], 'start' => $r['start_date'], 'end' => $r['end_date']);
            }
            $date_ranges['past_terms'] = $dateRangesMore;
        }


        return compact('time_adjust', 'today', 'today_time', 'date_ranges');
    }

    /**
     * $end_date が $today 以降であれば、$today の一日前の日付を返す
     * $end_date が $today より前であれば、$end_date をそのまま返す
     *
     * @param string $end_date YYYY-MM-DD
     * @param string $today    YYYY-MM-DD
     *
     * @return string
     */
    protected function _insightAdjustEndDate($end_date, $today)
    {
        $t1 = strtotime($end_date);
        $t2 = strtotime($today);
        if ($t1 < $t2) {
            return $end_date;
        }
        return date('Y-m-d', $t2 - DAY);
    }

    /**
     * 期間指定の値($date_range) が「本日」を含む期間の場合に true を返す
     *
     * @param $date_range
     *
     * @return bool
     */
    protected function _insightIsCurrentDateRange($date_range)
    {
        return strpos($date_range, 'current_') === 0;
    }

    /**
     * 期間指定の値($date_range) の種類を返す
     *
     * @param $date_range
     *
     * @return string 'week' or 'month' or 'term'
     */
    protected function _insightGetDateRangeType($date_range)
    {
        // array_pop()は引数に参照渡しを要求するので、
        // 一旦date_rangeを変数に格納した後、コールする
        $exploded_date_range = explode('_', $date_range);
        return array_pop($exploded_date_range);
    }

    /**
     * チーム集計データを返す
     *
     * @param      $start_date
     * @param      $end_date
     * @param      $timezone
     * @param null $groupId
     * @param null $cache_expire
     *
     * @return array
     */
    protected function _getInsightData($start_date, $end_date, $timezone, $groupId = null, $cache_expire = null)
    {
        // キャッシュにデータがあればそれを返す
        $insight = null;
        if ($groupId) {
            $insight = $this->GlRedis->getGroupInsight($this->current_team_id, $start_date, $end_date,
                $timezone, $groupId);
        } else {
            $insight = $this->GlRedis->getTeamInsight($this->current_team_id, $start_date, $end_date, $timezone);
        }
        if ($insight) {
            return $insight;
        }

        $timeAdjust = intval($timezone * HOUR);
        $startTimestamp = strtotime($start_date . " 00:00:00") - $timeAdjust;
        $endTimestamp = strtotime($end_date . " 23:59:59") - $timeAdjust;

        // グループ指定がある場合は、グループに所属する user_id で絞る
        $userIds = null;
        if ($groupId) {
            $userIds = $this->Team->Group->MemberGroup->getGroupMemberUserId($this->current_team_id, $groupId);
        }

        // 登録者数
        if ($groupId) {
            $total = $this->Team->GroupInsight->getTotal($groupId, $start_date, $end_date, $timezone);
        } else {
            $total = $this->Team->TeamInsight->getTotal($start_date, $end_date, $timezone);
        }
        $user_count = intval($total[0]['max_user_count']);

        // アクセスユーザー数
        $access_user_count = $this->Team->AccessUser->getUniqueUserCount($start_date, $end_date, $timezone,
            ['user_id' => $userIds]);

        // アクション数
        $action_count = $this->Post->ActionResult->getCount($userIds, $startTimestamp, $endTimestamp, 'created');

        // アクションユーザー数
        $action_user_count = $this->Post->ActionResult->getUniqueUserCount([
            'start'   => $startTimestamp,
            'end'     => $endTimestamp,
            'user_id' => $userIds
        ]);

        // 投稿数
        $post_count = $this->Post->getCount($userIds, $startTimestamp, $endTimestamp, 'created');

        // 投稿ユーザー数
        $post_user_count = $this->Post->getUniqueUserCount([
            'start'   => $startTimestamp,
            'end'     => $endTimestamp,
            'user_id' => $userIds
        ]);

        // 投稿いいね数
        $post_like_count = $this->Post->PostLike->getCount([
            'start'   => $startTimestamp,
            'end'     => $endTimestamp,
            'user_id' => $userIds
        ]);
        // コメントいいね数
        $comment_like_count = $this->Post->Comment->CommentLike->getCount([
            'start'   => $startTimestamp,
            'end'     => $endTimestamp,
            'user_id' => $userIds
        ]);
        // 総イイね数
        $like_count = $post_like_count + $comment_like_count;

        // 投稿いいねユーザー数
        $post_like_user_list = $this->Post->PostLike->getUniqueUserList([
            'start'   => $startTimestamp,
            'end'     => $endTimestamp,
            'user_id' => $userIds
        ]);
        // コメントいいねユーザー数
        $comment_like_user_list = $this->Post->Comment->CommentLike->getUniqueUserList([
            'start'   => $startTimestamp,
            'end'     => $endTimestamp,
            'user_id' => $userIds
        ]);
        $like_user_count = count(array_unique(array_merge($post_like_user_list, $comment_like_user_list)));

        // コメント数
        $comment_count = $this->Post->Comment->getCount([
            'start'     => $startTimestamp,
            'end'       => $endTimestamp,
            'post_type' => [Post::TYPE_NORMAL, Post::TYPE_ACTION],
            'user_id'   => $userIds
        ]);

        // コメントユーザー数
        $comment_user_count = $this->Post->Comment->getUniqueUserCount([
            'start'     => $startTimestamp,
            'end'       => $endTimestamp,
            'post_type' => [Post::TYPE_NORMAL, Post::TYPE_ACTION],
            'user_id'   => $userIds
        ]);

        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        // メッセージ数
        $message_count = $Message->getCount($userIds, $startTimestamp, $endTimestamp);
        // メッセージユーザー数
        $message_user_count = $Message->getUniqueUserCount($userIds, $startTimestamp, $endTimestamp);

        // ログイン率
        $access_user_percent = $user_count ? $access_user_count / $user_count * 100 : 0;
        $access_user_percent = $access_user_percent >= 1 ? round($access_user_percent) : round($access_user_percent, 1);

        // アクション率
        $action_user_percent = $user_count ? $action_user_count / $user_count * 100 : 0;
        $action_user_percent = $action_user_percent >= 1 ? round($action_user_percent) : round($action_user_percent, 1);

        // 投稿率
        $post_user_percent = $user_count ? $post_user_count / $user_count * 100 : 0;
        $post_user_percent = $post_user_percent >= 1 ? round($post_user_percent) : round($post_user_percent, 1);

        // いいね率
        $like_user_percent = $user_count ? $like_user_count / $user_count * 100 : 0;
        $like_user_percent = $like_user_percent >= 1 ? round($like_user_percent) : round($like_user_percent, 1);

        // コメント率
        $comment_user_percent = $user_count ? $comment_user_count / $user_count * 100 : 0;
        $comment_user_percent = $comment_user_percent >= 1 ?
            round($comment_user_percent) : round($comment_user_percent, 1);

        // メッセージ率
        $message_user_percent = $user_count ? $message_user_count / $user_count * 100 : 0;
        $message_user_percent = $message_user_percent >= 1 ?
            round($message_user_percent) : round($message_user_percent, 1);

        $insight = compact(
            'start_date',
            'end_date',
            'user_count',
            'access_user_count',
            'action_count',
            'action_user_count',
            'post_count',
            'post_user_count',
            'like_count',
            'like_user_count',
            'comment_count',
            'comment_user_count',
            'message_count',
            'message_user_count',
            'access_user_percent',
            'action_user_percent',
            'post_user_percent',
            'like_user_percent',
            'comment_user_percent',
            'message_user_percent'
        );

        // キャッシュに保存
        if ($groupId) {
            $this->GlRedis->saveGroupInsight($this->current_team_id, $start_date, $end_date, $timezone,
                $groupId, $insight, $cache_expire);
        } else {
            $this->GlRedis->saveTeamInsight($this->current_team_id, $start_date, $end_date, $timezone,
                $insight, $cache_expire);
        }
        return $insight;
    }

    /**
     * サークルの集計データを返す
     *
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $cache_expire
     *
     * @return array
     */
    protected function _getCircleInsightData($start_date, $end_date, $timezone, $cache_expire = null)
    {
        // キャッシュにデータがあればそれを返す
        $insight = $this->GlRedis->getCircleInsight($this->current_team_id, $start_date, $end_date, $timezone);
        if ($insight) {
            return $insight;
        }

        $time_adjust = intval($timezone * HOUR);
        $start_time = strtotime($start_date . " 00:00:00") - $time_adjust;
        $end_time = strtotime($end_date . " 23:59:59") - $time_adjust;

        $circle_insights = [];
        $public_circle_list = $this->Team->Circle->getPublicCircleList();
        foreach ($public_circle_list as $circle_id => $circle_name) {
            // fetching active members count in circle
            $circle_member_count = $this->Team->CircleInsight->getTotal($circle_id, $start_date, $end_date, $timezone);

            // 投稿数
            $circle_post_count = $this->Post->PostShareCircle->getPostCountByCircleId($circle_id, [
                'start' => $start_time,
                'end'   => $end_time,
            ]);

            // リーチ数
            // 指定期間内の投稿を読んだメンバーの合計数（現在まで）
            $circle_post_read_count = $this->Post->PostShareCircle->getTotalPostReadCountByCircleId($circle_id, [
                'start' => $start_time,
                'end'   => $end_time,
            ]);

            // 指定期間内の投稿投稿へのいいね数の合計数（現在まで）
            $circle_post_like_count = $this->Post->PostShareCircle->getTotalPostLikeCountByCircleId($circle_id, [
                'start' => $start_time,
                'end'   => $end_time,
            ]);

            $engage_percent = $circle_post_read_count ?
                round($circle_post_like_count / $circle_post_read_count * 100, 1) : 0;

            $circle_insights[$circle_id] = [
                'circle_id'       => $circle_id,
                'user_count'      => $circle_member_count,
                'post_count'      => $circle_post_count,
                'post_read_count' => $circle_post_read_count,
                'engage_percent'  => $engage_percent,
            ];
        }

        // 並び順変更
        // チーム全体サークルは常に先頭、それ以外はリーチの多い順
        $team_all_circle_id = $this->Post->Circle->getTeamAllCircleId();
        uasort($circle_insights, function ($a, $b) use ($team_all_circle_id) {
            if ($a['circle_id'] == $team_all_circle_id) {
                return -1;
            }
            if ($b['circle_id'] == $team_all_circle_id) {
                return 1;
            }
            if ($a['post_read_count'] == $b['post_read_count']) {
                return 0;
            }
            return ($a['post_read_count'] < $b['post_read_count']) ? 1 : -1;
        });

        // キャッシュに保存
        $this->GlRedis->saveCircleInsight($this->current_team_id, $start_date, $end_date, $timezone, $circle_insights,
            $cache_expire);
        return $circle_insights;
    }

    /**
     * 投稿ランキングのデータを返す
     *
     * @param      $start_date
     * @param      $end_date
     * @param      $timezone
     * @param null $group_id
     * @param null $cache_expire
     *
     * @return array|mixed|null
     */
    protected function _getPostRankingData($start_date, $end_date, $timezone, $group_id = null, $cache_expire = null)
    {
        // キャッシュにデータがあればそれを返す
        $ranking = null;
        $type = 'post_ranking';
        if ($group_id) {
            $ranking = $this->GlRedis->getGroupRanking($this->current_team_id, $start_date, $end_date,
                $timezone, $group_id, $type);
        } else {
            $ranking = $this->GlRedis->getTeamRanking($this->current_team_id, $start_date, $end_date,
                $timezone, $type);
        }
        if ($ranking) {
            return $ranking;
        }

        $time_adjust = intval($timezone * HOUR);
        $start_time = strtotime($start_date . " 00:00:00") - $time_adjust;
        $end_time = strtotime($end_date . " 23:59:59") - $time_adjust;

        // グループ指定がある場合は、グループに所属する user_id で絞る
        $user_ids = null;
        if ($group_id) {
            $user_ids = $this->Team->Group->MemberGroup->getGroupMemberUserId($this->current_team_id, $group_id);
        }

        // 全公開サークル
        // 投稿のランキングは公開サークルに共有されたものだけを対象にする
        $public_circle_list = $this->Team->Circle->getPublicCircleList();

        // 最もいいねされた投稿
        $post_like_ranking = $this->Post->PostLike->getRanking(
            [
                'start'           => $start_time,
                'end'             => $end_time,
                'post_user_id'    => $user_ids,
                'post_type'       => Post::TYPE_NORMAL,
                'share_circle_id' => array_keys($public_circle_list),
            ]);

        // 最もいいねされたアクション
        $action_like_ranking = $this->Post->PostLike->getRanking(
            [
                'post_user_id' => $user_ids,
                'post_type'    => Post::TYPE_ACTION,
                'start'        => $start_time,
                'end'          => $end_time,

            ]);

        // 最もコメントされた投稿
        $post_comment_ranking = $this->Post->Comment->getRanking(
            [
                'post_user_id'    => $user_ids,
                'post_type'       => Post::TYPE_NORMAL,
                'start'           => $start_time,
                'end'             => $end_time,
                'share_circle_id' => array_keys($public_circle_list),

            ]);

        // 最もコメントされたアクション
        $action_comment_ranking = $this->Post->Comment->getRanking(
            [
                'post_user_id' => $user_ids,
                'post_type'    => Post::TYPE_ACTION,
                'start'        => $start_time,
                'end'          => $end_time,

            ]);

        $ranking = compact(
            'start_date',
            'end_date',
            'post_like_ranking',
            'action_like_ranking',
            'post_comment_ranking',
            'action_comment_ranking'
        );

        // キャッシュに保存
        if ($group_id) {
            $this->GlRedis->saveGroupRanking($this->current_team_id, $start_date, $end_date, $timezone,
                $group_id, $type, $ranking, $cache_expire);
        } else {
            $this->GlRedis->saveTeamRanking($this->current_team_id, $start_date, $end_date, $timezone,
                $type, $ranking, $cache_expire);
        }
        return $ranking;
    }

    /**
     * ゴールランキングのデータを返す
     *
     * @param      $start_date
     * @param      $end_date
     * @param      $timezone
     * @param null $group_id
     * @param null $cache_expire
     *
     * @return array|mixed|null
     */
    protected function _getGoalRankingData($start_date, $end_date, $timezone, $group_id = null, $cache_expire = null)
    {
        // キャッシュを調べる
        $ranking = null;
        $type = 'goal_ranking';
        if ($group_id) {
            $ranking = $this->GlRedis->getGroupRanking($this->current_team_id, $start_date, $end_date,
                $timezone, $group_id, $type);
        } else {
            $ranking = $this->GlRedis->getTeamRanking($this->current_team_id, $start_date, $end_date,
                $timezone, $type);
        }
        if ($ranking) {
            return $ranking;
        }

        $time_adjust = intval($timezone * HOUR);
        $start_time = strtotime($start_date . " 00:00:00") - $time_adjust;
        $end_time = strtotime($end_date . " 23:59:59") - $time_adjust;

        // グループ指定がある場合は、グループに所属する user_id で絞る
        $user_ids = null;
        if ($group_id) {
            $user_ids = $this->Team->Group->MemberGroup->getGroupMemberUserId($this->current_team_id, $group_id);
        }

        // 最もアクションされたゴール
        $action_goal_ranking = $this->Post->ActionResult->getGoalRanking(
            [
                'goal_user_id' => $user_ids,
                'start'        => $start_time,
                'end'          => $end_time,

            ]);

        $ranking = compact(
            'start_date',
            'end_date',
            'action_goal_ranking'
        );

        // キャッシュに保存
        if ($group_id) {
            $this->GlRedis->saveGroupRanking($this->current_team_id, $start_date, $end_date, $timezone,
                $group_id, $type, $ranking, $cache_expire);
        } else {
            $this->GlRedis->saveTeamRanking($this->current_team_id, $start_date, $end_date, $timezone,
                $type, $ranking, $cache_expire);
        }
        return $ranking;
    }

    /**
     * ユーザーランキングのデータを返す
     *
     * @param      $start_date
     * @param      $end_date
     * @param      $timezone
     * @param null $group_id
     * @param null $cache_expire
     *
     * @return array|mixed|null
     */
    protected function _getUserRankingData($start_date, $end_date, $timezone, $group_id = null, $cache_expire = null)
    {
        // キャッシュを調べる
        $ranking = null;
        $type = 'user_ranking';
        if ($group_id) {
            $ranking = $this->GlRedis->getGroupRanking($this->current_team_id, $start_date, $end_date,
                $timezone, $group_id, $type);
        } else {
            $ranking = $this->GlRedis->getTeamRanking($this->current_team_id, $start_date, $end_date,
                $timezone, $type);
        }
        if ($ranking) {
            return $ranking;
        }

        $time_adjust = intval($timezone * HOUR);
        $start_time = strtotime($start_date . " 00:00:00") - $time_adjust;
        $end_time = strtotime($end_date . " 23:59:59") - $time_adjust;

        // グループ指定がある場合は、グループに所属する user_id で絞る
        $user_ids = null;
        if ($group_id) {
            $user_ids = $this->Team->Group->MemberGroup->getGroupMemberUserId($this->current_team_id, $group_id);
        }

        // 最もアクションした人
        $action_user_ranking = $this->Post->ActionResult->getUserRanking(
            [
                'user_id' => $user_ids,
                'start'   => $start_time,
                'end'     => $end_time,

            ]);

        // 最も投稿した人
        $post_user_ranking = $this->Post->getPostCountUserRanking(
            [
                'user_id' => $user_ids,
                'start'   => $start_time,
                'end'     => $end_time,

            ]);

        $ranking = compact(
            'start_date',
            'end_date',
            'action_user_ranking',
            'post_user_ranking'
        );

        // キャッシュに保存
        if ($group_id) {
            $this->GlRedis->saveGroupRanking($this->current_team_id, $start_date, $end_date, $timezone,
                $group_id, $type, $ranking, $cache_expire);
        } else {
            $this->GlRedis->saveTeamRanking($this->current_team_id, $start_date, $end_date, $timezone,
                $type, $ranking, $cache_expire);
        }
        return $ranking;
    }

    /**
     * Insight ページのシステム管理者用のセットアップ
     */
    protected function _setupForSystemAdminInsight()
    {
        // システム管理者でない場合は何もしない
        if (!$this->Auth->user('admin_flg')) {
            return;
        }

        // チーム選択を出来るようにする
        $team_list = $this->Team->getListWithTeamId();
        $this->set('team_list', $team_list);

        // team のパラメータがあれば、モデルの team_id を上書きする
        if ($team_id = $this->request->query('team')) {
            $this->orig_team_id = $this->current_team_id;
            $this->current_team_id = $team_id;
            foreach (ClassRegistry::keys() as $k) {
                $obj = ClassRegistry::getObject($k);
                if ($obj instanceof AppModel) {
                    $obj->current_team_id = $team_id;
                }
            }

            // まだロードされてないモデル用に一時的に書き換え
            $this->Session->write('current_team_id', $team_id);

            //EvaluateTermのプロパティにログインteamのtermがすでにセットされている(コントローラの共有処理による)のでリセット。
            $this->Team->Term->resetAllTermProperty();
            //期間データが存在しない場合に対応できるようにする
            $this->_setTerm();

        }
    }

    /**
     * Insight ページのシステム管理者用のクリーンアップ
     */
    protected function _cleanupForSystemAdminInsight()
    {
        // システム管理者でない場合は何もしない
        if (!$this->Auth->user('admin_flg')) {
            return;
        }

        // チームID を元に戻す
        if ($this->orig_team_id) {
            $this->current_team_id = $this->orig_team_id;
            foreach (ClassRegistry::keys() as $k) {
                $obj = ClassRegistry::getObject($k);
                if ($obj instanceof AppModel) {
                    $obj->current_team_id = $this->orig_team_id;
                }
            }
            $this->Session->write('current_team_id', $this->orig_team_id);
        }
    }

    /**
     * Activate team member action
     * # Paid & charge case
     *  - Activate team member & charge card|invoice
     *  - Redirect confirmation page
     * # Paid & not charge case
     *  - Activate team member only
     *  - Reload page
     * # Free trial case
     *  - Activate team member only
     *  - Reload page
     *
     * @param int $teamMemberId
     *
     * @return void
     */
    function activate_team_member(int $teamMemberId)
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init("TeamMemberService");

        $teamId = $this->current_team_id;

        // Validate activation
        if (!$TeamMemberService->validateActivation($teamId, $teamMemberId)) {
            $this->Notification->outError(__("Failed to activate team member."));
            return $this->redirect($this->referer());
        }

        // Paid charge case
        if ($PaymentService->isChargeUserActivation($teamId)) {
            return $this->redirect(['action' => 'confirm_user_activation', $teamMemberId]);
        }

        // Campaign team case
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init('CampaignService');
        if ($CampaignService->purchased($teamId) &&
            $CampaignService->willExceedMaximumCampaignAllowedUser($teamId, 1)) {
            $this->Notification->outError(__("Your campaign plan reached the maximum user allowed. Please contact for the larger plans."));
            // TODO: change to the campaign plan select in the future(?)
            // campaign 1st release does not support the changes of campaign plans
            return $this->redirect("/payments");
        }

        // Paid or free trial case
        if ($this->Team->TeamMember->activate($teamMemberId)) {
            // TODO: Should display translation correctry by @kohei
            $this->Notification->outSuccess(__("Changed active status inactive to active."));

            CakeLog::info(sprintf('activate team member: %s', AppUtil::jsonOneLine([
                'teams.id'        => $this->current_team_id,
                'team_members.id' => $teamMemberId,
            ])));
            PaymentUtil::logCurrentTeamChargeUsers($this->current_team_id);
        } else {
            // TODO: Should display translation correctry by @kohei
            $this->Notification->outError(__("Failed to activate team member."));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Activate team member confirmation page
     *
     * @param int $teamMemberId
     *
     * @return CakeResponse
     */
    function confirm_user_activation(int $teamMemberId)
    {
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init("TeamMemberService");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        $teamId = $this->current_team_id;

        // Validate activation
        if (!$TeamMemberService->validateActivation($teamId, $teamMemberId)) {
            $this->Notification->outError(__("Failed to activate team member."));
            return $this->redirect($this->referer());
        }

        if (!$PaymentService->isChargeUserActivation($teamId)) {
            $this->Notification->outError(__("Failed to activate team member."));
            return $this->redirect($this->referer());
        }

        // User
        $user = $this->Team->TeamMember->getUserById($teamMemberId);
        $displayUserName = $user['display_username'];

        // Payment info
        $paymentSetting = $PaymentService->get($this->current_team_id);
        $amountPerUser = $PaymentService->formatCharge($paymentSetting['amount_per_user'], $paymentSetting['currency']);
        $useDaysByNext = $PaymentService->getUseDaysByNextBaseDate($teamId);
        $allUseDays = $PaymentService->getCurrentAllUseDays($teamId);
        $currency = new Enum\Model\PaymentSetting\Currency((int)$paymentSetting['currency']);
        $totalCharge = $PaymentService->formatTotalChargeByAddUsers($teamId, 1, $currency, $useDaysByNext, $allUseDays);

        $this->set(compact('teamMemberId', 'displayUserName', 'amountPerUser', 'useDaysByNext', 'totalCharge'));

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    /**
     * Activate team member with payment
     *
     * @param int $teamId
     *
     * @return void
     */
    function activate_with_payment()
    {
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init("TeamMemberService");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");

        $teamId = $this->current_team_id;
        $teamMemberId = Hash::get($this->request->data, 'TeamMember.id');

        // Validate activation
        if (!$TeamMemberService->validateActivation($teamId, $teamMemberId)) {
            $this->Notification->outSuccess(__("Failed to activate team member."));
            return $this->redirect($this->referer());
        }

        if (!$PaymentService->isChargeUserActivation($teamId)) {
            $this->Notification->outError(__("Failed to activate team member."));
            return $this->redirect($this->referer());
        }

        // Activate
        $userId = $this->Auth->user('id');
        $activationRes = $TeamMemberService->activateWithPayment($teamId, $teamMemberId, $userId);
        if ($activationRes['error']) {
            // TODO: Should display translation correctry by @kohei
            $this->Notification->outError($activationRes['msg']);
        } else {
            // TODO: Should display translation correctry by @kohei
            $this->Notification->outSuccess(__("Changed active status inactive to active."));
        }

        return $this->redirect('/teams/main');
    }
}
