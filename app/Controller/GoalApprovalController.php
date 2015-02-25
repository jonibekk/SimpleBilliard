<?php
App::uses('AppController', 'Controller');
App::uses('AppModel',      'Model');
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
	 * コーチ判定フラグ
	 * true: コーチがいる false: コーチがいない
	 */
	//private $coach_flag = false;

	/*
	 * メンバー判定フラグ
	 * true: メンバーがいる false: メンバーがいない
	 */
	private $member_flag = false;

	/*
	 *  ログイン中のuser_id
	 */
	private $user_id = null;

	/*
	 * ログインメンバーのteam_id
	 */
	private $team_id = null;

	/*
	 * オーバーライド
	 */
	public function beforeFilter() {

		parent::beforeFilter();

		$this->layout = LAYOUT_ONE_COLUMN;

		$Session = new CakeSession();
		$this->user_id = $Session->read('Auth.User.id');
		$this->team_id = $Session->read('current_team_id');

		// コーチ認定機能が使えるユーザーはトップページ
		if ($this->check_valid_user() === FALSE) {
		}
	}

	/*
	 * 処理待ちページ
	 */
	public function index() {
		// 自分のゴールを取得
		$this->Goal->getMyGoals();
		// メンバーがいればのメンバーのゴールも取得
		if ($this->member_flag === TRUE) {
		}
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
	 * コーチ認定機能を使えるユーザーか判定
	 */
	private function check_valid_user() {
		return true;
	}

}
