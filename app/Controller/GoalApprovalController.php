<?php
App::uses('AppController', 'Controller');
App::uses('Collaborator',  'Model');

/**
 * GoalApproval Controller
 *
 * @property GoalApproval $GoalApproval
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class GoalApprovalController extends AppController {

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
	 * メンバー判定フラグ
	 * true: メンバーがいる false: メンバーがいない
	 */
	private $member_flag = FALSE;

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
		if ($this->user_type = $this->getUserType() === 0) {
		}
		$this->layout = LAYOUT_ONE_COLUMN;

		// test code
		$this->user_type = 1;
	}

	/*
	 * 処理待ちページ
	 */
	public function index() {

		$result_data = array();
		$col_obj = new Collaborator();

		if ($this->user_type === 1) {
			$my_goal_id = $this->Goal->getGoalIdFromUserId($this->user_id, $this->team_id);
			$goal_info = $col_obj->getCollabeGoalDetail($my_goal_id, false);
			$wait_my_goal_msg = GoalApprovalController::WAIT_MY_GOAL_MSG;
			$this->set(compact('goal_info', 'wait_my_goal_msg'));

		} elseif ($this->user_type === 2) {
			// + メンバーのゴールを取得

		} elseif ($this->user_type === 3) {
			// + メンバーのゴールのみ取得
		}

		$this->set($result_data);
	}

	/*
	 * 処理済みページ
	 */
	public function done() {
	}

	/*
	 * 承認する
	 */
	public function doApproval() {
		return $this->index();
	}

	/*
	 * 承認しない
	 */
	public function dontApproval() {
		return $this->index();
	}

	/*
	 * 処理を取り消す
	 */
	public function cancle() {
		return $this->done();
	}

	/*
	 * ログインしているユーザーはコーチが存在するのか
	 */
	private function setCoachFlag ($user_id, $team_id) {
		$this->selectCoachUserIdFromTeamMembersTB($user_id, $team_id);
		$this->coach_flag = TRUE;
	}

	/*
	 * ログインしているユーザーのコーチIDを取得する
	 * TODO: Model/TeamMemberに定義するのが正しい
	 */
	private function selectCoachUserIdFromTeamMembersTB ($user_id, $team_id) {
		// 検索テーブル: team_members
		// 取得カラム: coach_user_id
		// 条件: user_id, team_id
	}

	/*
	 * ログインしているユーザーは管理するメンバー存在するのか
	 */
	private function setMemberFlag ($user_id, $team_id) {
		$this->selectUserIdFromTeamMembersTB($user_id, $team_id);
		$this->member_flag = TRUE;
	}

	/*
	 * ログインしているユーザーが管理するのメンバーIDを取得する
	 * TODO: Model/TeamMemberに定義するのが正しい
	 */
	private function selectUserIdFromTeamMembersTB ($user_id, $team_id) {
		// 検索テーブル: team_members
		// 取得カラム: user_id
		// 条件: coach_user_id = パラメータ1 team_id = パラメータ2
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
