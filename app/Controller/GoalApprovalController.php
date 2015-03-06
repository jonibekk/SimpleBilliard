<?php
App::uses('AppController', 'Controller');

/**
 * GoalApproval Controller
 *
 * @property GoalApproval $GoalApproval
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property TeamMember $TeamMember
 * @property Collaborator $Collaborator
 */
class GoalApprovalController extends AppController {

	/*
	 * 使用モデル
	 */
	public $uses = [
		'Collaborator',
		'TeamMember'
	];

	/*
	 * 処理待ち && 自分のゴールの場合
	 */
	const WAIT_MY_GOAL_MSG         = '承認待ち中です';

	/*
	 * 処理済み && 自分のゴールが承認されたの場合
	 */
	const APPROVAL_MY_GOAL_YES_MSG = 'コーチが承認しました';

	/*
	 * 処理済み && 自分のゴールが保留の場合
	 */
	const APPROVAL_MY_GOAL_NG_MSG  = 'コーチが保留しました';

	/*
	 * 処理済み && メンバーのゴールが承認されたの場合
	 */
	const APPROVAL_MEMBER_GOAL_YES_MSG  = '承認しました';

	/*
	 * 処理済み && メンバーのゴールが保留の場合
	 */
	const APPROVAL_MEMBER_GOAL_NG_MSG   = '保留にしました';

	/*
	 * 処理済み用のメッセージリスト
	 */
	private $approval_msg_list = [
		1 => GoalApprovalController::APPROVAL_MY_GOAL_YES_MSG,
		2 => GoalApprovalController::APPROVAL_MY_GOAL_NG_MSG,
		3 => GoalApprovalController::APPROVAL_MEMBER_GOAL_YES_MSG,
		4 => GoalApprovalController::APPROVAL_MEMBER_GOAL_NG_MSG
	];

	/*
	 * コーチ判定フラグ
	 * true: コーチがいる false: コーチがいない
	 */
	private $coach_flag = FALSE;

	/*
	 * コーチID
	 */
	private $coach_id = '';

	/*
	 * メンバー判定フラグ
	 * true: メンバーがいる false: メンバーがいない
	 */
	private $member_flag = FALSE;

	/*
	 * メンバーIDリスト
	 */
	private $member_ids = [];

	/*
	 * ログインしているユーザータイプ
	 * 1: コーチのみ存在
	 * 2: コーチとメンバーが存在
	 * 3: メンバーのみ存在
	 */
	private $user_type = 0;

	/*
	 * ログインユーザーのuser_id
	 */
	private $user_id = NULL;

	/*
	 * ログインユーザーのteam_id
	 */
	private $team_id = NULL;

	/*
	 * オーバーライド
	 */
	public function beforeFilter() {

		parent::beforeFilter();

		$Session = new CakeSession();
		$this->user_id = $Session->read('Auth.User.id');
		$this->team_id = $Session->read('current_team_id');

		$this->setCoachFlag($this->user_id, $this->team_id);
		$this->setMemberFlag($this->user_id, $this->team_id);

		// コーチ認定機能が使えるユーザーはトップページ
		$this->user_type = $this->getUserType();
		if ($this->user_type  === 0) {
		}
		$this->layout = LAYOUT_ONE_COLUMN;

	}

	/*
	 * 処理待ちページ
	 */
	public function index()
	{

		$goal_ids = $this->getCollaboratorGoalId();
		$goal_info = $this->Collaborator->getCollabeGoalDetail($goal_ids, 'wait');

		foreach ($goal_info as $key => $val) {
			if ($this->user_id === $val['User']['id']) {
				$goal_info[$key]['msg'] = GoalApprovalController::WAIT_MY_GOAL_MSG;
			}
		}

		$this->set(compact('goal_info'));
	}

	/*
	 * 処理済みページ
	 */
	public function done () {

		$goal_ids = $this->getCollaboratorGoalId();
		$goal_info = $this->Collaborator->getCollabeGoalDetail($goal_ids, true);

	}

	/*
	 * 承認する
	 */
	public function approval () {
		$id = $this->request->param('id');
		if (empty($id) === FALSE) {
			$this->Collaborator->changeApprovalStatus(intval($id), 'approval');
		}
		$this->redirect($this->referer());
	}

	/*
	 * 承認しない
	 */
	public function wait () {
		$id = $this->request->param('id');
		if (empty($id) === FALSE) {
			$this->Collaborator->changeApprovalStatus(intval($id), 'hold');
		}
		$this->redirect($this->referer());
	}

	/*
	 * 処理を取り消す
	 */
	public function cancle () {
		return $this->done();
	}

	/*
	 * リストに表示するゴールのIDを取得
	 */
	private function getCollaboratorGoalId () {
		$goal_ids = [];
		if ($this->user_type === 1) {
			$goal_ids = $this->Goal->getGoalIdFromUserId($this->user_id, $this->team_id);

		} elseif ($this->user_type === 2) {
			$my_goal_id = $this->Goal->getGoalIdFromUserId($this->user_id, $this->team_id);
			$member_goal_id = $this->Goal->getGoalIdFromUserId($this->member_ids, $this->team_id);
			$goal_ids = array_merge($my_goal_id, $member_goal_id);

		} elseif ($this->user_type === 3) {
			$goal_ids = $this->Goal->getGoalIdFromUserId($this->member_ids, $this->team_id);
		}
		return $goal_ids;
	}

	/*
	 * ログインしているユーザーはコーチが存在するのか
	 */
	private function setCoachFlag ($user_id, $team_id) {
		$coach_id = $this->TeamMember->selectCoachUserIdFromTeamMembersTB($user_id, $team_id);
		if (is_null($coach_id['TeamMember']['coach_user_id']) === FALSE) {
			$this->coach_id = $coach_id['TeamMember']['coach_user_id'];
			$this->coach_flag = TRUE;
		}
	}

	/*
	 * ログインしているユーザーは管理するメンバー存在するのか
	 */
	private function setMemberFlag ($user_id, $team_id) {
		$member_ids = $this->TeamMember->selectUserIdFromTeamMembersTB($user_id, $team_id);
		if (empty($member_ids) === FALSE) {
			$this->member_ids = $member_ids;
			$this->member_flag = TRUE;
		}
	}

	/*
	 * コーチ認定機能を使えるユーザーか判定
	 * 1: コーチがいる、メンバーいない
	 * 2: コーチいる、メンバーがいる
	 * 3: コーチがいない、メンバーがいる
	 */
	private function getUserType() {

		if ($this->coach_flag === TRUE && $this->member_flag === FALSE) {
			return 1;
		}

		if ($this->coach_flag === TRUE && $this->member_flag === TRUE) {
			return 2;
		}

		if ($this->coach_flag === FALSE && $this->member_flag === TRUE) {
			return 3;
		}

		return 0;
	}

}
