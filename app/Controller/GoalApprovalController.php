<?php
App::uses('AppController', 'Controller');
App::uses('Collaborator', 'Model');

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

    /*
     * 処理待ち && 自分のゴールの場合
     */
    const WAIT_MY_GOAL_MSG = 0;

    /*
     * 処理待ち && メンバーのゴール && valued_flag=3 の場合
     */
    const MODIFY_MEMBER_GOAL_MSG = 1;

    /*
     * 処理済み && メンバーのゴール && valued_flag=1 の場合
     */
    const APPROVAL_MEMBER_GOAL_MSG = 2;

    /*
     * 処理済み && メンバーのゴール && valued_flag=2 の場合
     */
    const NOT_APPROVAL_MEMBER_GOAL_MSG = 3;

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
    const USER_TYPE_NOT_AVAILABLE = 0;
    const USER_TYPE_ONLY_COACH = 1;
    const USER_TYPE_COACH_AND_MEMBER = 2;
    const USER_TYPE_ONLY_MEMBER = 3;

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
        'unapproved' => Collaborator::STATUS_UNAPPROVED,
        'approval'   => Collaborator::STATUS_APPROVAL,
        'hold'       => Collaborator::STATUS_HOLD,
        'modify'     => Collaborator::STATUS_MODIFY,
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
            self::WAIT_MY_GOAL_MSG             => __("Waiting for approval"),
            self::APPROVAL_MEMBER_GOAL_MSG     => __("In Evaluation"),
            self::NOT_APPROVAL_MEMBER_GOAL_MSG => __("Out of Evaluation"),
            self::MODIFY_MEMBER_GOAL_MSG       => __("Waiting for modified"),
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

        $this->_setCoachFlag($this->user_id);
        $this->_setMemberFlag($this->user_id);

        // コーチ認定機能が使えるユーザーはトップページ
        $this->user_type = $this->_getUserType();
        if ($this->user_type === self::USER_TYPE_NOT_AVAILABLE) {
        }

        $this->my_evaluation_flg = $this->TeamMember->getEvaluationEnableFlg($this->user_id, $this->team_id);
        $this->goal_user_ids = $this->_getCollaboratorUserId();

        $this->done_cnt = $this->Collaborator->countCollaboGoal(
            $this->team_id, $this->user_id, $this->goal_user_ids,
            [$this->goal_status['approval'], $this->goal_status['hold']]
        );

        $this->layout = LAYOUT_ONE_COLUMN;
    }

    /*
     * 処理待ちページ
     */
    public function index()
    {
        if ($this->request->is('post')) {
            $this->ApprovalHistory->begin();
            if ($this->_saveApprovalData()) {
                $this->ApprovalHistory->commit();
            } else {
                $this->ApprovalHistory->rollback();
            }
            return $this->redirect($this->referer());
        }

        $goal_info = $this->_getGoalInfo([$this->goal_status['unapproved'], $this->goal_status['modify']]);

        foreach ($goal_info as $key => $val) {
            $goal_info[$key]['my_goal'] = false;

            if ($this->user_id === $val['User']['id']) {
                $goal_info[$key]['my_goal'] = true;
                $goal_info[$key]['status'] = $this->approval_msg_list[self::WAIT_MY_GOAL_MSG];
                if ($this->my_evaluation_flg === false) {
                    unset($goal_info[$key]);
                }
            }

            if ($val['Collaborator']['approval_status'] === (string)Collaborator::STATUS_MODIFY) {
                $goal_info[$key]['status'] = $this->approval_msg_list[self::MODIFY_MEMBER_GOAL_MSG];
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
        if ($this->request->is('post')) {
            $this->ApprovalHistory->begin();
            if ($this->_saveApprovalData()) {
                $this->ApprovalHistory->commit();
            } else {
                $this->ApprovalHistory->rollback();
            }
            return $this->redirect($this->referer());
        }

        $goal_info = $this->_getGoalInfo([$this->goal_status['approval'], $this->goal_status['hold']]);

        foreach ($goal_info as $key => $val) {
            $goal_info[$key]['my_goal'] = false;
            $goal_info[$key]['is_present_term'] = $this->Goal->isPresentTermGoal($val['Goal']['id']);

            if ($this->user_id === $val['User']['id']) {
                $goal_info[$key]['my_goal'] = true;
                if ($this->my_evaluation_flg === false) {
                    unset($goal_info[$key]);
                }
            }

            if ($val['Collaborator']['approval_status'] === (string)Collaborator::STATUS_APPROVAL) {
                $goal_info[$key]['status'] = $this->approval_msg_list[self::APPROVAL_MEMBER_GOAL_MSG];

            } else {
                if ($val['Collaborator']['approval_status'] === (string)Collaborator::STATUS_HOLD) {
                    $goal_info[$key]['status'] = $this->approval_msg_list[self::NOT_APPROVAL_MEMBER_GOAL_MSG];
                }
            }
        }

        $done_cnt = $this->done_cnt;
        $kr = new KeyResult();
        $value_unit_list = $kr::$UNIT;

        $this->set(compact('value_unit_list', 'goal_info', 'done_cnt'));
    }

    function _saveApprovalData()
    {
        $data = viaIsSet($this->request->data['GoalApproval']);
        if (empty($data)) {
            return false;
        }
        if (!$this->_changeStatus($data)) {
            return false;
        }

        $cb_id = viaIsSet($data['collaborator_id']);
        if (!$cb_id) {
            return false;
        }
        $collaborator = $this->Collaborator->findById($cb_id);
        $goal_id = viaIsSet($collaborator['Collaborator']['goal_id']);
        if (!$goal_id) {
            return false;
        }
        $this->_trackToMixpanel($goal_id);
        $this->_notifyToCollaborator($cb_id);
        return true;

    }

    /*
     * 認定状態変更コントロール
     */
    public function _changeStatus($data)
    {
        $res = false;
        if (isset($this->request->data['comment_btn']) === true) {
            $res = $this->_comment($data);
            $this->Pnotify->outSuccess(__("Commented"));
        } elseif (isset($this->request->data['wait_btn']) === true) {
            $res = $this->_wait($data);
            $this->Pnotify->outSuccess(__("Move to Done."));
        } elseif (isset($this->request->data['approval_btn']) === true) {
            $res = $this->_approval($data);
            $this->Pnotify->outSuccess(__("Move to Done."));
        } elseif (isset($this->request->data['modify_btn']) === true) {
            $res = $this->_modify($data);
            $this->Pnotify->outSuccess(__("Sent amend request."));
        }
        if (!$res) {
            $this->Pnotify->outError(__("Failed to update Approvement."));
        }
        return $res;
    }

    /*
     * 承認する
     */
    public function _approval($data)
    {
        $cb_id = isset($data['collaborator_id']) === true ? $data['collaborator_id'] : '';
        if (empty($cb_id) === false) {
            $this->Collaborator->changeApprovalStatus(intval($cb_id), $this->goal_status['approval']);
            return $this->_comment($data);
        }
    }

    /*
     * 承認しない
     */
    public function _wait($data)
    {
        $cb_id = isset($data['collaborator_id']) === true ? $data['collaborator_id'] : '';
        if (empty($cb_id) === false) {
            $this->Collaborator->changeApprovalStatus(intval($cb_id), $this->goal_status['hold']);
            return $this->_comment($data);
        }
    }

    /*
     * 修正依頼をする
     */
    public function _modify($data)
    {
        $cb_id = isset($data['collaborator_id']) === true ? $data['collaborator_id'] : '';
        if (empty($cb_id) === false) {
            $this->Collaborator->changeApprovalStatus(intval($cb_id), $this->goal_status['modify']);
            return $this->_comment($data);
        }
    }

    /*
     *  コメントする
     */
    public function _comment($data)
    {
        $cb_id = isset($data['collaborator_id']) === true ? $data['collaborator_id'] : '';
        $comment = isset($data['comment']) === true ? $data['comment'] : '';

        // 現状はコメントがある時、履歴を追加している。
        // 今後はコメントなくてもアクションステータスを格納する必要あり。
        if (empty($cb_id) === false && empty($comment) === false) {
            return $this->ApprovalHistory->add($cb_id, $this->user_id, ApprovalHistory::ACTION_STATUS_ONLY_COMMENT,
                $comment);
        }
        return true;
    }

    function _trackToMixpanel($goal_id)
    {

        $approval_type = $approval_member_type = null;
        if (isset($this->request->data['comment_btn']) === true) {
            $approval_type = MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_COMMENT_GOAL;
            $approval_member_type = MixpanelComponent::PROP_APPROVAL_MEMBER_MEMBER;
        } elseif (isset($this->request->data['wait_btn']) === true) {
            $approval_type = MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_INEVALUABLE;
            $approval_member_type = MixpanelComponent::PROP_APPROVAL_MEMBER_COACH;
        } elseif (isset($this->request->data['approval_btn']) === true) {
            $approval_type = MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_EVALUABLE;
            $approval_member_type = MixpanelComponent::PROP_APPROVAL_MEMBER_COACH;
        } elseif (isset($this->request->data['modify_btn']) === true) {
            $approval_type = MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_REVISION_REQUESTS;
            $approval_member_type = MixpanelComponent::PROP_APPROVAL_MEMBER_COACH;
        }

        $this->Mixpanel->trackApproval(
            $approval_type,
            $approval_member_type,
            $goal_id
        );
    }

    /*
     * リストに表示するゴールのUserIDを取得
     */
    public function _getCollaboratorUserId()
    {
        $goal_user_ids = [];
        if ($this->user_type === self::USER_TYPE_ONLY_COACH) {
            $goal_user_ids = [$this->user_id];
        } elseif ($this->user_type === self::USER_TYPE_COACH_AND_MEMBER) {
            $goal_user_ids = array_merge([$this->user_id], $this->member_ids);
        } elseif ($this->user_type === self::USER_TYPE_ONLY_MEMBER) {
            $goal_user_ids = $this->member_ids;
        }
        return $goal_user_ids;
    }

    /*
     * リストに表示するゴールのUserIDを取得
     */
    public function _getGoalInfo($goal_status)
    {
        $goal_info = [];
        if ($this->user_type === self::USER_TYPE_ONLY_COACH) {
            $goal_info = $this->Collaborator->getCollaboGoalDetail(
                $this->team_id, [$this->user_id], $goal_status, true, EvaluateTerm::TYPE_CURRENT);

        } elseif ($this->user_type === self::USER_TYPE_COACH_AND_MEMBER) {
            $member_goal_info = $this->Collaborator->getCollaboGoalDetail(
                $this->team_id, $this->member_ids, $goal_status, false, EvaluateTerm::TYPE_CURRENT);

            $my_goal_info = $this->Collaborator->getCollaboGoalDetail(
                $this->team_id, [$this->user_id], $goal_status, true, EvaluateTerm::TYPE_CURRENT);

            $goal_info = array_merge($member_goal_info, $my_goal_info);

        } elseif ($this->user_type === self::USER_TYPE_ONLY_MEMBER) {
            $goal_info = $this->Collaborator->getCollaboGoalDetail(
                $this->team_id, $this->member_ids, $goal_status, false, EvaluateTerm::TYPE_CURRENT);
        }

        return $goal_info;
    }

    /*
     * ログインしているユーザーはコーチが存在するのか
     */
    public function _setCoachFlag($user_id)
    {
        $coach_id = $this->TeamMember->getCoachUserIdByMemberUserId($user_id);
        if ($coach_id) {
            $this->coach_id = $coach_id;
            $this->coach_flag = true;
        }
    }

    /*
     * ログインしているユーザーは管理するメンバー存在するのか
     */
    public function _setMemberFlag($user_id)
    {
        $member_ids = $this->TeamMember->getMyMembersList($user_id);
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
    public function _getUserType()
    {

        if ($this->coach_flag === true && $this->member_flag === false) {
            return self::USER_TYPE_ONLY_COACH;
        }

        if ($this->coach_flag === true && $this->member_flag === true) {
            return self::USER_TYPE_COACH_AND_MEMBER;
        }

        if ($this->coach_flag === false && $this->member_flag === true) {
            return self::USER_TYPE_ONLY_MEMBER;
        }

        return self::USER_TYPE_NOT_AVAILABLE;
    }

    /**
     * send notify to collaborator
     *
     * @param $collabo_id
     */
    function _notifyToCollaborator($collabo_id)
    {
        $collaborator = $this->Collaborator->findById($collabo_id);
        if (!viaIsSet($collaborator['Collaborator'])) {
            return;
        }
        $notify_type = null;
        if (isset($this->request->data['comment_btn']) === true) {
            //TODO コーチ宛に通知出さなくていいのかな？ by Daiki
        } elseif (isset($this->request->data['wait_btn']) === true) {
            $notify_type = NotifySetting::TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION;
        } elseif (isset($this->request->data['approval_btn']) === true) {
            $notify_type = NotifySetting::TYPE_MY_GOAL_TARGET_FOR_EVALUATION;
        } elseif (isset($this->request->data['modify_btn']) === true) {
            $notify_type = NotifySetting::TYPE_MY_GOAL_AS_LEADER_REQUEST_TO_CHANGE;
        }

        //Notify
        $this->NotifyBiz->execSendNotify($notify_type,
            $collaborator['Collaborator']['goal_id'],
            null,
            $collaborator['Collaborator']['user_id']
        );
    }

}
