<?php
App::uses('AppController', 'Controller');

/**
 * GoalApproval Controller
 *
 * @property PaginatorComponent $Paginator
 * @property SessionComponent   $Session
 * @property TeamMember         $TeamMember
 * @property Collaborator       $Collaborator
 * @property ApprovalHistory    $ApprovalHistory
 */
class GoalApprovalController extends AppController
{

    /*
     * 使用モデル
     */
    public $uses = [
        'Collaborator',
        'TeamMember',
        'ApprovalHistory',
    ];

    public $components = ['RequestHandler'];

    /*
     * 処理待ち && 自分のゴールの場合
     */
    const WAIT_MY_GOAL_MSG = 0;

    /*
     * 処理済み && 自分のゴールが承認されたの場合
     */
    const APPROVAL_MY_GOAL_YES_MSG = 1;

    /*
     * 処理済み && 自分のゴールが保留の場合
     */
    const APPROVAL_MY_GOAL_NG_MSG = 2;

    /*
     * 処理済み && メンバーのゴールが承認されたの場合
     */
    const APPROVAL_MEMBER_GOAL_YES_MSG = 3;

    /*
     * 処理済み && メンバーのゴールが保留の場合
     */
    const APPROVAL_MEMBER_GOAL_NG_MSG = 4;

    /*
     * 処理済み用のメッセージリスト
     */
    public $approval_msg_list = [];

    /*
     * コーチ判定フラグ
     * true: コーチがいる false: コーチがいない
     */
    public $coach_flag = false;

    /*
     * コーチID
     */
    public $coach_id = '';

    /*
     * メンバー判定フラグ
     * true: メンバーがいる false: メンバーがいない
     */
    public $member_flag = false;

    /*
     * メンバーIDリスト
     */
    public $member_ids = [];

    /*
     * ログインしているユーザータイプ
     * 1: コーチのみ存在
     * 2: コーチとメンバーが存在
     * 3: メンバーのみ存在
     */
    public $user_type = 0;

    /*
     * ログインユーザーのuser_id
     */
    public $user_id = null;

    /*
     * ログインユーザーのteam_id
     */
    public $team_id = null;

    /*
     * 評価ステータス
     */
    public $goal_status = [
        'unapproved' => 0,
        'approval'   => 1,
        'hold'       => 2,
        'modify'     => 3,
    ];

    /*
     * 検索対象のゴールID
     */
    public $goal_user_ids = [];

    /*
     * 承認前ページの「全ゴール - 自分のゴール」件数
     */
    public $done_cnt = 0;

    /*
     * ログインユーザーの評価対象フラグ
     */
    public $my_evaluation_flg = false;

    public function __construct(CakeRequest $request = null, CakeResponse $response = null)
    {
        parent::__construct($request, $response);
        $this->_setMsg();
    }

    private function _setMsg()
    {
        $this->approval_msg_list = [
            self::WAIT_MY_GOAL_MSG             => __d('gl', "承認待ち中"),
            self::APPROVAL_MY_GOAL_YES_MSG     => __d('gl', "コーチが承認しました"),
            self::APPROVAL_MY_GOAL_NG_MSG      => __d('gl', "コーチが保留しました"),
            self::APPROVAL_MEMBER_GOAL_YES_MSG => __d('gl', "承認しました"),
            self::APPROVAL_MEMBER_GOAL_NG_MSG  => __d('gl', "保留にしました"),
        ];
    }

    /*
     * オーバーライド
     */
    public function beforeFilter()
    {

        parent::beforeFilter();

        $this->user_id = $this->Auth->user('id');
        $this->team_id = $this->Session->read('current_team_id');

        $this->setCoachFlag($this->user_id, $this->team_id);
        $this->setMemberFlag($this->user_id, $this->team_id);

        // コーチ認定機能が使えるユーザーはトップページ
        $this->user_type = $this->getUserType();
        if ($this->user_type === 0) {
        }

        $this->my_evaluation_flg = $this->TeamMember->getEvaluationEnableFlg($this->user_id, $this->team_id);
        $this->goal_user_ids = $this->getCollaboratorUserId();

        $this->done_cnt = $this->Collaborator->countCollaboGoal(
            $this->team_id, $this->user_id, $this->goal_user_ids,
            [$this->goal_status['approval'], $this->goal_status['hold'], $this->goal_status['modify']]
        );

        $this->layout = LAYOUT_ONE_COLUMN;

    }

    /*
     * 処理待ちページ
     */
    public function index()
    {
        $goal_info = $this->Collaborator->getCollaboGoalDetail(
            $this->team_id, $this->goal_user_ids, $this->goal_status['unapproved']);
        foreach ($goal_info as $key => $val) {
            if ($this->user_id === $val['User']['id']) {
                $goal_info[$key]['msg'] = $this->approval_msg_list[self::WAIT_MY_GOAL_MSG];
                if ($this->my_evaluation_flg === false) {
                    unset($goal_info[$key]);
                }
            }
        }

        $done_cnt = $this->done_cnt;
        $kr = new KeyResult();
        $value_unit_list = $kr::$UNIT;

        $this->set(compact('value_unit_list', 'goal_info', 'done_cnt'));
    }

    /*
     * 処理済みページ
     */
    public function done()
    {
        $goal_info = $this->Collaborator->getCollaboGoalDetail(
            $this->team_id, $this->goal_user_ids,
            [$this->goal_status['approval'], $this->goal_status['hold'], $this->goal_status['modify']]
        );
        foreach ($goal_info as $key => $val) {
            if ($this->user_id === $val['User']['id']) {
                $goal_info[$key]['msg'] = '自分のゴール';
                if ($this->my_evaluation_flg === false) {
                    unset($goal_info[$key]);
                }
            }
        }

        $done_cnt = $this->done_cnt;
        $kr = new KeyResult();
        $value_unit_list = $kr::$UNIT;

        $this->set(compact('value_unit_list', 'goal_info', 'done_cnt'));
    }

    /*
     * 承認する
     */
    public function approval()
    {
        $id = $this->request->param('id');
        if (empty($id) === false) {
            $this->Collaborator->changeApprovalStatus(intval($id), $this->goal_status['approval']);
        }
        $this->redirect($this->referer());
    }

    /*
     * 承認しない
     */
    public function wait()
    {
        $id = $this->request->param('collaborator_id');
        $comment = $this->request->param('comment');
        if (empty($id) === false) {
            $this->Collaborator->changeApprovalStatus(intval($id), $this->goal_status['hold']);
            $this->ApprovalHistory->add($id, $this->user_id, 1, $comment);
        }
        $this->redirect($this->referer());
    }

    /*
     *  コメントする
     */
    public function comment()
    {
        $id = $this->request->param('id');
        $comment = $this->request->param('comment');
        if (empty($id) === false) {
            $this->ApprovalHistory->add($id, $this->user_id, 1, $comment);
        }
        $this->redirect($this->referer());
        //return $this->response;
    }

    public function get_history() {
        $id = $this->request->param('id');
        if (empty($id) === false) {
            $comment_list = $this->ApprovalHistory->getHistory($id);
            $this->response->type('json');
            $this->response->body(json_encode($comment_list));
        }
        return $this->response;
    }

    /*
     * リストに表示するゴールのUserIDを取得
     */
    public function getCollaboratorUserId()
    {
        $goal_user_ids = [];
        if ($this->user_type === 1) {
            $goal_user_ids = [$this->user_id];
        }
        elseif ($this->user_type === 2) {
            $goal_user_ids = array_merge([$this->user_id], $this->member_ids);
        }
        elseif ($this->user_type === 3) {
            $goal_user_ids = $this->member_ids;
        }
        return $goal_user_ids;
    }

    /*
     * ログインしているユーザーはコーチが存在するのか
     */
    public function setCoachFlag($user_id, $team_id)
    {
        $coach_id = $this->TeamMember->selectCoachUserIdFromTeamMembersTB($user_id, $team_id);
        if (isset($coach_id['TeamMember']['coach_user_id']) === true
            && is_null($coach_id['TeamMember']['coach_user_id']) === false
        ) {
            $this->coach_id = $coach_id['TeamMember']['coach_user_id'];
            $this->coach_flag = true;
        }
    }

    /*
     * ログインしているユーザーは管理するメンバー存在するのか
     */
    public function setMemberFlag($user_id, $team_id)
    {
        $member_ids = $this->TeamMember->selectUserIdFromTeamMembersTB($user_id, $team_id);
        if (empty($member_ids) === false) {
            $this->member_ids = $member_ids;
            $this->member_flag = true;
        }
    }

    /*
     * コーチ認定機能を使えるユーザーか判定
     * 1: コーチがいる、メンバーいない
     * 2: コーチいる、メンバーがいる
     * 3: コーチがいない、メンバーがいる
     */
    public function getUserType()
    {

        if ($this->coach_flag === true && $this->member_flag === false) {
            return 1;
        }

        if ($this->coach_flag === true && $this->member_flag === true) {
            return 2;
        }

        if ($this->coach_flag === false && $this->member_flag === true) {
            return 3;
        }

        return 0;
    }

}
