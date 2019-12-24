<?php

use Goalous\Exception\Follow\ValidationToFollowException;

App::uses('ApiController', 'Controller/Api');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'GoalService');
App::import('Service', 'FollowService');
App::import('Service/Api', 'ApiGoalService');
App::import('Service/Api', 'ApiKeyResultService');

App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::uses('ErrorResponse', 'Lib/Network/Response');

/** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 *
 * @property Goal        $Goal
 * @property TeamVision  $TeamVision
 * @property GroupVision $GroupVision
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

    public $components = [
        'Notification',
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

        $data = $this->request->data;
        if (!empty($_FILES['photo'])) {
            $data['photo'] = $_FILES['photo'];
        }

        $validationErrors = $GoalService->validateSave($data, $fields);
        if (!empty($validationErrors)) {
            return $this->_getResponseValidationFail($validationErrors);
        }
        return $this->_getResponseSuccess();
    }

    /**
     * ゴール検索
     *
     * @query_param fields
     * @return CakeResponse
     */
    function get_search()
    {
        /** @var ApiGoalService $ApiGoalService */
        $ApiGoalService = ClassRegistry::init("ApiGoalService");
        // 取得件数上限チェック
        if (!$ApiGoalService->checkMaxLimit((int)$this->request->query('limit'))) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }
        $searchResult = $this->_findSearchResults();
        return $this->_getResponsePagingSuccess($searchResult);
    }

    /**
     * Download goals based on filter
     */
    public function get_download_csv()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        // Check permission
        if (!$TeamMember->getLoginUserAdminFlag($this->current_team_id, $this->Auth->user('id'))) {
            return $this->_getResponseForbidden();
        }
        $conditions = $this->_fetchSearchConditions();

        /** @var ApiGoalService $ApiGoalService */
        $ApiGoalService = ClassRegistry::init("ApiGoalService");

        $team = $Team->getCurrentTeam();
        $timezoneTeam = floatval($team['Team']['timezone']);
        $currentDateTime = GoalousDateTime::now()->setTimeZoneByHour($timezoneTeam)->format("YmdHi");

        $fileName = "goalous_goals_export_$currentDateTime.csv";
        $csvFile = $ApiGoalService->createCsvFile($this->current_team_id, $conditions);

        return ApiResponse::ok()->getResponseForDL($csvFile, $fileName);
    }

    /**
     * ゴール検索の共通処理
     * ゴール初期データと検索用API両方で利用する
     *
     * @return array
     * @throws Exception もし取得上限数超えていたら例外投げる
     */
    private function _findSearchResults(): array
    {
        /** @var ApiGoalService $ApiGoalService */
        $ApiGoalService = ClassRegistry::init("ApiGoalService");

        /* リクエストパラメータ取得 */
        $offset = $this->request->query('offset');
        $limit = (int)$this->request->query('limit');
        $order = $this->request->query('order');
        $conditions = $this->_fetchSearchConditions();

        $limit = empty($limit) ? ApiGoalService::GOAL_SEARCH_DEFAULT_LIMIT : $limit;

        // ゴール検索
        $searchResult = $ApiGoalService->search($this->Auth->user('id'), $conditions, $offset, $limit, $order);
        return $searchResult;
    }

    /**
     * クエリパラメータからゴール検索条件を取得
     *
     * @return array
     */
    private function _fetchSearchConditions(): array
    {
        $conditions = [
            'keyword'  => $this->request->query('keyword'),
            'category' => $this->request->query('category'),
            'progress' => $this->request->query('progress'),
            'term'     => $this->request->query('term'),
            'labels'   => $this->request->query('labels') ?? [],
        ];
        return $conditions;
    }

    /**
     * ゴール検索初期データ取得
     *
     * @return CakeResponse
     */
    function get_init_search()
    {
        /** @var ApiGoalService $ApiGoalService */
        $ApiGoalService = ClassRegistry::init("ApiGoalService");
        // 取得件数上限チェック
        if (!$ApiGoalService->checkMaxLimit((int)$this->request->query('limit'))) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }

        $res = [];
        // ゴール検索
        $res['search_result'] = $this->_findSearchResults();
        // 検索条件を返却
        $res['search_conditions'] = $this->_fetchSearchConditions();

        /* @var Label $Label */
        $Label = ClassRegistry::init('Label');

        $res['categories'] = Hash::extract(
            $this->Goal->GoalCategory->getCategories(['id', 'name']),
            '{n}.GoalCategory'
        );

        $res['labels'] = Hash::extract($Label->getListWithGoalCount(), '{n}.Label');

        return $this->_getResponseSuccess($res);
    }

    /**
     * Call this /api/v1/goals/hide_goal_create_guidance from
     * goal crete guidance close button.
     * Guidance displayed when user does not have any goal.
     * @return CakeResponse
     */
    function post_hide_goal_create_guidance()
    {
        $this->Session->write('hide_goal_create_guidance', true);
        return $this->_getResponseSuccess();
    }

    /**
     * ゴール更新のバリデーションAPI
     * 成功(Status Code:200)、失敗(Status Code:400)
     *
     * @param integer $goalId
     *
     * @return CakeResponse
     */
    function post_validate_update($goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        // 403/404チェック
        $errResponse = $this->_validateEditForbiddenOrNotFound($goalId);
        if ($errResponse !== true) {
            return $errResponse;
        }

        $fields = [];
        $data = $this->request->data;
        if (!empty($_FILES['photo'])) {
            $data['photo'] = $_FILES['photo'];
        }

        $validationErrors = $GoalService->validateSave($data, $fields, $goalId);
        if (!empty($validationErrors)) {
            return $this->_getResponseValidationFail($validationErrors);
        }
        return $this->_getResponseSuccess();
    }

    /**
     * Goal作成&編集においての初期化処理API
     * formで利用する値を取得する
     *
     * @query_params bool data_types `all` is returning all data_types, it can be selected individually(e.g.
     *               `categories,labels`)
     *
     * @param integer|null $id
     *
     * @return CakeResponse
     */
    function get_init_form($id = null)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $res = [];

        // 編集の場合、idからゴール情報を取得・設定
        if (!empty($id)) {
            // 403/404チェック
            $errResponse = $this->_validateEditForbiddenOrNotFound($id);
            if ($errResponse !== true) {
                return $errResponse;
            }

            $res['goal'] = $GoalService->get($id, $this->Auth->user('id'), [
                GoalService::EXTEND_TOP_KEY_RESULT,
                GoalService::EXTEND_GOAL_LABELS,
                GoalService::EXTEND_GOAL_MEMBERS,
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
            // TODO:サービスに移行
            $tmp = $this->TeamVision->getTeamVision($this->current_team_id, true, true);
            $team_visions = [];
            foreach ($tmp as $vision) {
                $v = $vision['TeamVision'];
                $v['team'] = $vision['Team'];
                $team_visions[] = $v;
            }
            $team_visions = Hash::insert($team_visions, '{n}.type', 'team_vision');

            $group_visions = Hash::insert($this->GroupVision->getMyGroupVision(true), '{n}.type', 'group_vision');
            $visions = am($group_visions, $team_visions);
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
            $current = $this->Team->Term->getTermData(Term::TYPE_CURRENT);
            $next = $this->Team->Term->getTermData(Term::TYPE_NEXT);
            $res['terms'] = [Term::TERM_TYPE_CURRENT => $current, Term::TERM_TYPE_NEXT => $next];
        }

        if ($dataTypes == 'all' || in_array('priorities', $dataTypes)) {
            $res['priorities'] = Configure::read("label.priorities");
        }

        if ($dataTypes == 'all' || in_array('units', $dataTypes)) {
            $res['units'] = Configure::read("label.units");
        }

        if ($dataTypes == 'all' || in_array('default_end_dates', $dataTypes)) {
            $currentTerm = $this->Team->Term->getCurrentTermData();
            $nextTerm = $this->Team->Term->getNextTermData();
            $res['default_end_dates'] = [
                Term::TERM_TYPE_CURRENT => $currentTerm['end_date'],
                Term::TERM_TYPE_NEXT    => $nextTerm['end_date'],
            ];
        }

        // ログインユーザーがゴール認定可能か
        if ($dataTypes == 'all' || in_array('can_approve', $dataTypes)) {
            /** @var GoalApprovalService $GoalApprovalService */
            $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
            $res['can_approve'] = $GoalApprovalService->isApprovable(
                $this->Auth->user('id'), $this->Session->read('current_team_id')
            );
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
        if (!empty($_FILES['photo'])) {
            $data['photo'] = $_FILES['photo'];
        }

        // バリデーション
        $validateErrors = $this->_validateCreateGoal($data);
        if (!empty($validateErrors)) {
            return $this->_getResponseValidationFail($validateErrors);
        }

        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        // ゴール作成
        $newGoalId = $GoalService->create($this->Auth->user('id'), $data);
        if (!$newGoalId) {
            return $this->_getResponseInternalServerError();
        }

        // コーチへ通知
        // 来期のゴール関係の処理はコーチへ通知しない
        if ($this->Goal->isPresentTermGoal($newGoalId)) {
            $this->NotifyBiz->push(Hash::get($data, 'socket_id'), "all");
            $this->_sendNotifyToCoach($newGoalId, NotifySetting::TYPE_COACHEE_CREATE_GOAL);
        }

        // セットアップガイドステータス更新
        $this->_updateSetupStatusIfNotCompleted();

        //コーチの未認定件数を更新(キャッシュを削除)
        $coachId = $this->User->TeamMember->getCoachUserIdByMemberUserId($this->my_uid);
        if ($coachId) {
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coachId), 'user_data');
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
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("Term");

        // 403/404チェック
        $errResponse = $this->_validateEditForbiddenOrNotFound($goalId);
        if ($errResponse !== true) {
            return $errResponse;
        }

        $data = $this->request->data;
        if (!empty($_FILES['photo'])) {
            $data['photo'] = $_FILES['photo'];
        }

        // 変更タイプ
        $preUpdatedTerm = $Goal->getTermTypeById($goalId);
        $afterUpdatedTerm = Hash::get($data, 'term_type');
        $isNextToCurrentUpdate = ($preUpdatedTerm == Term::TERM_TYPE_NEXT) && ($afterUpdatedTerm == Term::TERM_TYPE_CURRENT);

        // バリデーション
        // 来期から今期への期変更の場合はKR日付バリデーションはoffにする
        if ($isNextToCurrentUpdate) {
            unset($Goal->update_validate['end_date']['checkAfterKrEndDate']);
        }
        $validateErrors = $this->_validateUpdateGoal($data, $goalId);
        if (!empty($validateErrors)) {
            return $this->_getResponseValidationFail($validateErrors);
        }

        // ゴール更新
        if (!$GoalService->update($this->Auth->user('id'), $goalId, $data)) {
            return $this->_getResponseInternalServerError();
        }

        // リファラに表示する通知カード
        $this->Notification->outSuccess(__("Saved Goal & Top Key Result"));

        // コーチへ通知
        // 来期のゴール関係の処理はコーチへ通知しない
        if ($this->Goal->isPresentTermGoal($goalId)) {
            if ($isNextToCurrentUpdate) {
                $this->_sendNotifyToCoach($goalId, NotifySetting::TYPE_COACHEE_CHANGE_GOAL_NEXT_TO_CURRENT);
            } else {
                $this->_sendNotifyToCoach($goalId, NotifySetting::TYPE_COACHEE_CHANGE_GOAL);
            }
        }

        //コラボレータへの通知
        if ($isNextToCurrentUpdate) {
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_CHANGED_NEXT_TO_CURRENT_BY_LEADER, $goalId,
                null);
        } else {
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_CHANGED_BY_LEADER, $goalId, null);
        }

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
     * @return array
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
     * @param array   $data
     * @param integer $goalId
     *
     * @return array
     */
    private function _validateUpdateGoal($data, $goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $fields = $GoalService->goalValidateFields;
        $fields[] = "key_result";
        $fields[] = "approval_history";
        $fields[] = "labels";
        return $GoalService->validateSave($data, $fields, $goalId);
    }

    /**
     * ゴール編集の403/404バリデーション
     *
     * @param $goalId
     *
     * @return CakeResponse|true
     * @internal param array $data
     */
    private function _validateEditForbiddenOrNotFound($goalId)
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
        // 今季以降のゴールか
        if (!$GoalService->isGoalAfterCurrentTerm($goalId)) {
            return $this->_getResponseNotFound();
        }
        return true;
    }

    /**
     * フォロー
     *
     * @param $goalId
     *
     * @return CakeResponse|true
     * @internal param array $data
     */
    public function post_follow($goalId)
    {
        /** @var FollowService $FollowService */
        $FollowService = ClassRegistry::init("FollowService");
        try {
            $FollowService->validateToFollow(
                $this->Session->read('current_team_id'),
                $goalId,
                $this->Auth->user('id')
            );
        } catch (ValidationToFollowException $e) {
            return $this->_getResponseBadFail($e->getMessage());
        }

        // フォロー
        $newId = $FollowService->add($goalId, $this->Auth->user('id'));
        if (!$newId) {
            return $this->_getResponseInternalServerError();
        }

        // トラッキング
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_FOLLOW_GOAL, $goalId);
        // 通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_FOLLOW, $goalId);

        return $this->_getResponseSuccess(['follow_id' => $newId]);
    }

    /**
     * フォロー解除
     *
     * @param $goalId
     *
     * @return CakeResponse|true
     * @internal param array $data
     */
    public function delete_follow($goalId)
    {
        /** @var FollowService $FollowService */
        $FollowService = ClassRegistry::init("FollowService");

        // ゴール存在チェック
        if (!$this->Goal->isBelongCurrentTeam($goalId, $this->Session->read('current_team_id'))) {
            return $this->_getResponseBadFail(__("The Goal doesn't exist."));
        }
        // 解除対象のフォロー存在チェック
        $userId = $this->Auth->user('id');
        $following = $FollowService->getUnique($goalId, $userId);
        if (empty($following)) {
            return $this->_getResponseBadFail(__("The following doesn't exist."));
        }

        // フォロー解除
        if (!$FollowService->delete($goalId, $userId)) {
            return $this->_getResponseInternalServerError();
        }

        // トラッキング
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_FOLLOW_GOAL, $goalId);

        return $this->_getResponseSuccess();
    }

    /**
     * トップページ右カラムの初期表示データ取得API
     * - APIレスポンス
     *{
     *  "data": {
     *    "progress_graph": [],
     *    "krs": [],
     *    "goals": []
     *  }
     *}
     *
     * @return CakeResponse
     */
    public function get_dashboard()
    {
        /** @var ApiGoalService $ApiGoalService */
        $ApiGoalService = ClassRegistry::init("ApiGoalService");

        // クエリパラメータ取得
        $limit = $this->request->query('limit') ?? ApiKeyResultService::DASHBOARD_KRS_DEFAULT_LIMIT;
        // KR取得件数上限チェック
        if (!$ApiGoalService->checkMaxLimit($limit)) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }

        // レスポンスデータ取得
        try {
            $response = $ApiGoalService->findDashboardFirstViewResponse($limit);
        } catch (Exception $e) {
            return $this->_getResponseBadFail($e->getMessage());
        }
        /** @noinspection PhpUndefinedVariableInspection */
        return $this->_getResponsePagingSuccess($response);
    }

    public function get_dashboard_krs()
    {
        /** @var ApiKeyResultService $ApiKeyResultService */
        $ApiKeyResultService = ClassRegistry::init("ApiKeyResultService");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        // クエリパラメータ取得
        $limit = $this->request->query('limit') ?? ApiKeyResultService::DASHBOARD_KRS_DEFAULT_LIMIT;
        $offset = $this->request->query('offset') ?? 0;
        $goalId = $this->request->query('goal_id');

        // KR取得件数上限チェック
        if (!$ApiKeyResultService->checkMaxLimit($limit)) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }

        // レスポンスデータ定義
        $response = [
            'data'   => [],
            'paging' => [
                'next' => ''
            ],
            'count'  => null
        ];

        // レスポンスデータ取得
        // Paging目的で1つ多くデータを取得する
        $krs = $ApiKeyResultService->findInDashboard($limit + 1, $offset, $goalId, false);

        // ページング情報セット。次回リクエストデータが存在する場合のみ。
        if (count($krs) > $limit) {
            $paging = $ApiKeyResultService->generatePagingInDashboard($limit, $offset, $goalId);
            $response['paging'] = $paging;
            array_pop($krs);
        }
        $response['data'] = $krs;
        // カウント数をセット
        $response['count'] = $KeyResult->countMine($goalId);

        return $this->_getResponsePagingSuccess($response);
    }
}
