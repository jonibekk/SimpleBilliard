<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'GoalService');

/** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 *
 * @property Goal $Goal
 */
class GoalsController extends ApiController
{
    // TODO:ここで定義しても$this->***で使用出来ない為要調査
    public $uses = [
        'Goal',
        'TeamVision',
        'GroupVision',
        'ApprovalHistory',
    ];

    /**
     * ゴール(KR除く)のバリデーションAPI
     * 成功(Status Code:200)、失敗(Status Code:400)
     * - fieldsパラメタにカンマ区切りで検査対象フィールドを指定。allもしくは指定なしの場合はすべてのフィールドのvalidationを行う。
     *
     * @query_param fields
     * @return CakeResponse
     */
    function post_validate()
    {
        $fields = [];
        if ($this->request->query('fields')) {
            $fields = explode(',', $this->request->query('fields'));
            //allが含まれる場合はすべて指定。それ以外はそのまま
            $fields = in_array('all', $fields) ? [] : $fields;
        }
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        $validationErrors = $GoalService->validateSave($this->request->data, $fields);
        if (!empty($validationErrors)) {
            return $this->_getResponseValidationFail($validationErrors);
        }
        return $this->_getResponseSuccess();
    }

    /**
     * ゴール更新のバリデーションAPI
     * 成功(Status Code:200)、失敗(Status Code:400)
     *
     * @return CakeResponse
     */
    function post_validate_update($goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $goal = $GoalService->get($goalId);
        if (empty($goal)) {
            $this->_getResponseNotFound();
        }
        if ($this->Auth->user('id') != $goal['user_id']) {
            $this->_getResponseForbidden();
        }

        $fields = $GoalService->goalValidateFields;
        $fields[] = 'key_result';
        $validationErrors = $GoalService->validateSave($this->request->data, $fields, $goalId);
        if (!empty($validationErrors)) {
            return $this->_getResponseValidationFail($validationErrors);
        }
        return $this->_getResponseSuccess();
    }

    /**
     * Goal作成&編集においての初期化処理API
     * formで利用する値を取得する
     *
     * @query_params bool data_types `all` is returning all data_types, it can be selected individually(e.g. `categories,labels`)
     *
     * @param integer|null $id
     *
     * @return CakeResponse
     */
    function get_init_form($id = null)
    {
        $res = [];

        // 編集の場合、idからゴール情報を取得・設定
        if (!empty($id)) {
            try {
                $this->Goal->isPermittedAdmin($id);
            } catch (RuntimeException$e) {
                return $this->_getResponseForbidden();
            }
            $GoalService = ClassRegistry::init("GoalService");
            $res['goal'] = $GoalService->get($id, $this->Auth->user('id'), [
                GoalService::EXTEND_TOP_KEY_RESULT,
                GoalService::EXTEND_GOAL_LABELS,
                GoalService::EXTEND_COLLABORATOR,
            ]);
        }

        /* @var Label $Label */
        $Label = ClassRegistry::init('Label');

        if ($this->request->query('data_types')) {
            $dataTypes = explode(',', $this->request->query('data_types'));
            if (in_array('all', $dataTypes)) {
                $dataTypes = 'all';
            }
        } else {
            $dataTypes = 'all';
        }

        if ($dataTypes == 'all' || in_array('visions', $dataTypes)) {
            $team_visions = Hash::insert(
                Hash::extract($this->TeamVision->getTeamVision($this->current_team_id, true, true),
                    '{n}.TeamVision'), '{n}.type', 'team_vision');
            $group_visions = Hash::insert($this->GroupVision->getMyGroupVision(true), '{n}.type', 'group_vision');

            $visions = am($team_visions, $group_visions);
            $res['visions'] = $visions;
        }

        if ($dataTypes == 'all' || in_array('categories', $dataTypes)) {
            $res['categories'] = Hash::extract(
                $this->Goal->GoalCategory->getCategories(['id', 'name']),
                '{n}.GoalCategory'
            );
        }

        if ($dataTypes == 'all' || in_array('labels', $dataTypes)) {
            $res['labels'] = Hash::extract($Label->getListWithGoalCount(), '{n}.Label');
        }

        if ($dataTypes == 'all' || in_array('terms', $dataTypes)) {
            $current = $this->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);
            $current['type'] = 'current';
            //TODO 個別にdate formatしているが一括で変更する仕組みを考えたほうがいい
            $current['start_date'] = date('Y-m-d', $current['start_date'] + $current['timezone'] * HOUR);
            $current['end_date'] = date('Y-m-d', $current['end_date'] + $current['timezone'] * HOUR);
            $next = $this->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_NEXT);
            $next['type'] = 'next';
            $next['start_date'] = date('Y-m-d', $next['start_date'] + $next['timezone'] * HOUR);
            $next['end_date'] = date('Y-m-d', $next['end_date'] + $next['timezone'] * HOUR);
            $res['terms'] = [$current, $next];
        }

        if ($dataTypes == 'all' || in_array('priorities', $dataTypes)) {
            $res['priorities'] = Configure::read("label.priorities");
        }

        if ($dataTypes == 'all' || in_array('units', $dataTypes)) {
            $res['units'] = Configure::read("label.units");
        }

        if ($dataTypes == 'all' || in_array('default_end_dates', $dataTypes)) {
            $TimeExHelper = new TimeExHelper(new View());
            $currentTerm = $this->Team->EvaluateTerm->getCurrentTermData();
            $nextTerm = $this->Team->EvaluateTerm->getNextTermData();
            $res['default_end_dates'] = [
                'current' => $TimeExHelper->dateFormat($currentTerm['end_date'], $currentTerm['timezone']),
                'next'    => $TimeExHelper->dateFormat($nextTerm['end_date'], $nextTerm['timezone']),
            ];
        }

        return $this->_getResponseSuccess($res);
    }

    /**
     * ゴール新規登録API
     * *必須フィールド
     * - socket_id: pusherへのpush用
     * *処理
     * - バリデーション(失敗したらレスポンス返す)
     * - ゴール新規登録(トランザクションかける。失敗したらレスポンス返す) TODO: タグの保存処理まだやっていない
     * - フィードへ新しい投稿がある旨を知らせる
     * - コーチへ通知
     * - セットアップガイドのステータスを更新
     * - コーチと自分の認定件数を更新(キャッシュを削除)
     * - Mixpanelでトラッキング
     * - TODO: 遷移先の情報は渡さなくて大丈夫か？api以外の場合はリダイレクトを分岐している。
     * - ゴールIDをレスポンスに含めて返却
     *
     * @return CakeResponse
     */
    function post()
    {
        $data = $this->request->data;
        $data['photo'] = $_FILES['photo'];

        // バリデーション
        $validateErrors = $this->_validateCreateGoal($data);
        if (!empty($validateErrors)) {
            return $this->_getResponseValidationFail($validateErrors);
        }

        $this->Goal->begin();
        $isSaveSuccess = $this->Goal->add(
            [
                'Goal'      => $data,
                'KeyResult' => [Hash::get($data, 'key_result')],
                'Label'     => Hash::get($data, 'labels'),
            ]
        );
        if ($isSaveSuccess === false) {
            $this->Goal->rollback();
            return $this->_getResponseBadFail(__('Failed to save a goal.'));
        }
        $this->Goal->commit();

        $newGoalId = $this->Goal->getLastInsertID();

        //通知
        $this->NotifyBiz->push(Hash::get($data, 'socket_id'), "all");
        $this->_sendNotifyToCoach($newGoalId, NotifySetting::TYPE_COACHEE_CREATE_GOAL);

        $this->updateSetupStatusIfNotCompleted();
        //コーチと自分の認定件数を更新(キャッシュを削除)
        $coach_id = $this->User->TeamMember->getCoachUserIdByMemberUserId($this->my_uid);
        if ($coach_id) {
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), 'user_data');
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coach_id), 'user_data');
        }

        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_GOAL, $newGoalId);

        return $this->_getResponseSuccess(['goal_id' => $newGoalId]);
    }

    /**
     * ゴール更新
     *
     * @param $goalId
     *
     * @return CakeResponse
     */
    function post_update($goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        // ゴール取得
        $goal = $GoalService->get($goalId);
        // ゴールが存在するか
        if (empty($goal)) {
            return $this->_getResponseNotFound();
        }
        // ゴール作成者か
        if ($this->Auth->user('id') != $goal['user_id']) {
            return $this->_getResponseForbidden();
        }

        $data = $this->request->data;
        $data['photo'] = $_FILES['photo'];

        // バリデーション
        $validateErrors = $this->_validateUpdateGoal($data, $goalId);
        if (!empty($validateErrors)) {
            return $this->_getResponseValidationFail($validateErrors);
        }

        // ゴール更新
        if (!$GoalService->update($this->Auth->user('id'), $goalId, $data)) {
            return $this->_getResponseInternalServerError();
        }

        //コーチへの通知
        $this->_sendNotifyToCoach($goalId, NotifySetting::TYPE_COACHEE_CHANGE_GOAL);
        //コラボレータへの通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_CHANGED_BY_LEADER, $goalId, null);

        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_UPDATE_GOAL, $goalId);

        return $this->_getResponseSuccess();
    }

    /**
     * ゴール作成のバリデーション
     * - key resultがなければバリデーションを通さずレスポンスを返す
     * - ゴールとKRのバリデーションは後ほど組み立てやすいようにそれぞれ別々に実行し、結果をマージしている。
     * TODO: 厳密にバリデーションルール、メッセージを再定義する
     *
     * @param array $data
     *
     * @return true|CakeResponse
     */
    private function _validateCreateGoal($data)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $fields = $GoalService->goalValidateFields;
        $fields[] = "key_result";
        return $GoalService->validateSave($data, $fields);
    }

    /**
     * ゴール編集のバリデーション
     *
     * @param array $data
     *
     * @return true|CakeResponse
     */
    private function _validateUpdateGoal($data, $goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $fields = $GoalService->goalValidateFields;
        $fields[] = "key_result";
        $fields[] = "approval_history";
        return $GoalService->validateSave($data, $fields, $goalId);
    }
}
