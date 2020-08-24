<?php
class AppSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $access_users = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
		'access_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id_access_date' => array('column' => array('team_id', 'access_date'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $action_result_files = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'action_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'アクションID(belongsToでActionResultモデルに関連)'),
		'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'action_result_id' => array('column' => 'action_result_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $action_results = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'アクションリザルトID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'key_result_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでGoalモデルに関連)'),
		'key_result_before_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => true, 'comment' => 'KR進捗値(更新前)'),
		'key_result_change_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => false, 'comment' => 'KR進捗増減値'),
		'key_result_target_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => true, 'comment' => 'KR進捗目標値'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
		'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '名前', 'charset' => 'utf8mb4'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'タイプ(0:user,1:goal,2:kr)'),
		'completed' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
		'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像1', 'charset' => 'utf8mb4'),
		'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像2', 'charset' => 'utf8mb4'),
		'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像3', 'charset' => 'utf8mb4'),
		'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像4', 'charset' => 'utf8mb4'),
		'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像5', 'charset' => 'utf8mb4'),
		'note' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ノート', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $admin_activity_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'admin_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'data' => array('type' => 'binary', 'null' => false, 'default' => null),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'admin_user_id' => array('column' => 'admin_user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $admin_users = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user email for signin', 'charset' => 'utf8mb4'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user password', 'charset' => 'utf8mb4'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user name', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $app_metas = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'メタID'),
		'key_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => 'キーの名前', 'charset' => 'utf8mb4'),
		'value' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '値', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $approval_histories = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'goal_member_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールメンバーID(hasManyでcollaboratorモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザーID(belongsToでUserモデルに関連)'),
		'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント', 'charset' => 'utf8mb4'),
		'action_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => ' 状態(0 = アクションなし,1 =コメントのみ, 2 = 評価対象にする, 3 = 評価対象にしない)'),
		'select_clear_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:no select, 1:is clear, 2:is not clear'),
		'select_important_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:no select, 1:is important, 2:not important'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'collaborator_id' => array('column' => 'goal_member_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $attached_files = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'attached_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8mb4'),
		'file_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ファイルタイプ(0:画像,1:ビデオ,2:ドキュメント)'),
		'file_ext' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ファイル拡張子', 'charset' => 'utf8mb4'),
		'file_size' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ファイルのバイト数'),
		'model_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'モデルタイプ(0:Post,1:Comment)'),
		'display_file_list_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ファイル一覧に表示するフラグ'),
		'removable_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '削除可能フラグ'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'post_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $cache_unread_circle_posts = array(
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '= teams.id'
        ),
        'circle_id'       => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '= circles.id'
        ),
        'user_id'         => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '= users.id'
        ),
        'post_id'         => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '= posts.id'
        ),
        'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'         => array(
            'PRIMARY'          => array('column' => 'id', 'unique' => 1),
            'circle_user'      => array('column' => array('circle_id', 'user_id'), 'unique' => 0),
            'circle_post'      => array('column' => array('circle_id', 'post_id'), 'unique' => 0),
            'paging_index'     => array('column' => array('team_id', 'user_id', 'id'), 'unique' => 0),
            'tuple' => array(
                'column' => array('team_id', 'circle_id', 'user_id', 'post_id'),
                'unique' => 1
            ),
        ),
        'tableParameters' => array(
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        ));

	public $campaign_teams = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'price_plan_group_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'External key:mst_price_plan_groups.id. Set value only if campaign_type = 0(Fixed monthly charge)'),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'Campaign contract start date(team timezone)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $charge_histories = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '請求操作実行ユーザー'),
		'payment_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '支払いタイプ(0: 請求書,1: クレジットカード)'),
		'charge_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '請求タイプ(0: 毎月支払い,1: ユーザー追加,2: ユーザーアクティブ化)'),
		'amount_per_user' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Service use amount per user in a charge'),
		'total_amount' => array('type' => 'decimal', 'null' => false, 'default' => '0.00', 'length' => '17,2', 'unsigned' => false, 'comment' => 'total amount excluding tax in a charge'),
		'tax' => array('type' => 'decimal', 'null' => false, 'default' => '0.00', 'length' => '17,2', 'unsigned' => false, 'comment' => 'tax in a charge'),
		'charge_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Charge user number'),
		'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Team country currency'),
		'charge_datetime' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Charge datetime unix timestamp'),
		'result_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Result type(0: Success, 1,2,3...: Failuer each type)'),
		'max_charge_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'チャージした結果のmax支払い人数'),
		'stripe_payment_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'stripe payment id. if invoice, it will be null', 'charset' => 'utf8mb4'),
		'reorder_charge_history_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'charge_histories.id that is target to be reordered'),
		'campaign_team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'campaign_team.id'),
		'price_plan_purchase_team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'price_plan_purchase_teams.id'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $circle_insights = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'target_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
		'circle_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'user_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id_target_date' => array('column' => array('team_id', 'target_date'), 'unique' => 0),
			'circle_id' => array('column' => 'circle_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $circle_members = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'サークルメンバーID'),
		'circle_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'サークルID(belongsToでCircleモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'admin_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '管理者フラグ'),
		'unread_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '未読数'),
		'show_for_all_feed_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'オールフィード表示フラグ'),
		'get_notification_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '通知設定'),
		'last_posted' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => true),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'circle_id' => array('column' => 'circle_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $circle_pins = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'circle_orders' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'del_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $circles = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'サークルID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'サークル名', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サークルの説明', 'charset' => 'utf8mb4'),
		'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サークルロゴ画像', 'charset' => 'utf8mb4'),
		'public_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開フラグ(公開の場合はチームメンバー全員にサークルの存在が閲覧可能)'),
		'team_all_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'チーム全体フラグ(各チームに必須で１つ存在する)'),
		'circle_member_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'メンバー数'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '部署を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
		'latest_post_created' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'name' => array('column' => 'name', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $comment_files = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'comment_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コメントID(belongsToでCommentモデルに関連)'),
		'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'comment_id' => array('column' => 'comment_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $comment_likes = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'コメントいいねID'),
		'comment_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コメントID(belongsToでcommentモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'いいねしたユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コメントを追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'comment_user_unique' => array('column' => array('comment_id', 'user_id'), 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'comment_id' => array('column' => 'comment_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $comment_mentions = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'コメントメンションID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メンションユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $comment_reads = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'コメント読んだID'),
		'comment_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コメントID(belongsToでcommentモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'comment_user_unique' => array('column' => array('comment_id', 'user_id'), 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'comment_id' => array('column' => 'comment_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $comments = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'コメントID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コメントしたユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント本文', 'charset' => 'utf8mb4'),
		'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Detected language of the comment body', 'charset' => 'utf8mb4'),
		'comment_like_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメントいいね数(comment_likesテーブルにレコードが追加されたらカウントアップされる)'),
		'comment_read_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメント読んだ数(comment_readsテーブルにレコードが追加されたらカウントアップされる)'),
		'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像1', 'charset' => 'utf8mb4'),
		'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像2', 'charset' => 'utf8mb4'),
		'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像3', 'charset' => 'utf8mb4'),
		'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像4', 'charset' => 'utf8mb4'),
		'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像5', 'charset' => 'utf8mb4'),
		'site_info' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サイト情報', 'charset' => 'utf8mb4'),
		'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $credit_cards = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
		'payment_setting_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
		'customer_code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Customer id by stripe', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $devices = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserID(belongsToでUserモデルに関連)'),
		'device_token' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'nitfy cloud id', 'charset' => 'utf8mb4'),
		'installation_id' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'アプリインストール毎に発行される識別子', 'charset' => 'utf8mb4'),
		'version' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アプリバージョン', 'charset' => 'utf8mb4'),
		'os_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:ios 1:android 99:other'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '登録した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '最後に更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'device_token' => array('column' => 'device_token', 'unique' => 0, 'length' => array('device_token' => '191')),
			'installation_id' => array('column' => 'installation_id', 'unique' => 0, 'length' => array('installation_id' => '191'))
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $emails = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'メアドID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアド', 'charset' => 'utf8mb4'),
		'email_verified' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
		'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8mb4'),
		'email_token_expires' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを登録した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを最後に更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'email' => array('column' => 'email', 'unique' => 0, 'length' => array('email' => '191')),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'email_token' => array('column' => 'email_token', 'unique' => 0, 'length' => array('email_token' => '191'))
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $evaluate_scores = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '評価スコア名', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '評価スコアの説明', 'charset' => 'utf8mb4'),
		'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価スコア表示順'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $evaluation_settings = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'enable_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価 on/off'),
		'self_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 on/off'),
		'self_goal_score_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価ゴールスコア on/off'),
		'self_goal_score_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価ゴールスコア必須 on/off'),
		'self_goal_comment_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 ゴール コメント on/off'),
		'self_goal_comment_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 ゴール コメント必須 on/off'),
		'self_score_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル スコア on/off'),
		'self_score_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル スコア 必須 on/off'),
		'self_comment_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル コメント on/off'),
		'self_comment_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル コメント 必須 on/off'),
		'evaluator_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 on/off'),
		'evaluator_goal_score_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール スコア on/off'),
		'evaluator_goal_score_reuqired_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール スコア必須 on/off'),
		'evaluator_goal_comment_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール コメント on/off'),
		'evaluator_goal_comment_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール コメント必須 on/off'),
		'evaluator_score_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル スコア on/off'),
		'evaluator_score_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル スコア 必須 on/off'),
		'evaluator_comment_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル コメント on/off'),
		'evaluator_comment_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル コメント 必須 on/off'),
		'final_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 on/off'),
		'final_score_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル スコア on/off'),
		'final_score_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル スコア 必須 on/off'),
		'final_comment_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル コメント on/off'),
		'final_comment_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル コメント 必須 on/off'),
		'leader_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 on/off'),
		'leader_goal_score_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール スコア on/off'),
		'leader_goal_score_reuqired_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール スコア必須 on/off'),
		'leader_goal_comment_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール コメント on/off'),
		'leader_goal_comment_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール コメント必須 on/off'),
		'fixed_evaluation_order_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価者による評価順序固定 on/off'),
		'show_all_evaluation_before_freeze_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価凍結前に他の評価者の評価を閲覧可能 on/off'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $evaluations = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
		'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
		'term_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価対象期間ID'),
		'evaluate_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '評価タイプ(0:自己評価,1:評価者評価,2:リーダー評価,3:最終者評価)'),
		'goal_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '評価コメント', 'charset' => 'utf8mb4'),
		'evaluate_score_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'スコアID(belongsToでEvaluateScoreモデルに関連)'),
		'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価順'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価ステータス(0:未入力,1:下書き,2:評価済)'),
		'my_turn_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0),
			'evaluatee_user_id' => array('column' => 'evaluatee_user_id', 'unique' => 0),
			'evaluator_user_id' => array('column' => 'evaluator_user_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'term_id' => array('column' => 'term_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $evaluator_change_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
		'last_update_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
		'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'evaluator_user_ids' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1000, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'evaluators_history_user_id_team_id_index' => array('column' => array('evaluatee_user_id', 'team_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB', 'comment' => 'Store recent changes to a person\'s evaluators')
	);

	public $evaluators = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
		'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価者の順序'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0),
			'evaluator_user_id' => array('column' => 'evaluator_user_id', 'unique' => 0),
			'evaluatee_user_id' => array('column' => 'evaluatee_user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $experiments = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '実験の識別子', 'charset' => 'utf8mb4'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'name' => array('column' => 'name', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $followers = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'フォロワーID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $given_badges = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '所有バッジID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'バッジ所有ユーザID(belongsToでUserモデルに関連)'),
		'grant_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'バッジあげたユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(hasOneでPostモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '所有バッジを追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'grant_user_id' => array('column' => 'grant_user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $goal_categories = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ゴールカテゴリID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '名前', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '説明', 'charset' => 'utf8mb4'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '有効フラグ'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $goal_change_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'data' => array('type' => 'binary', 'null' => false, 'default' => null, 'comment' => 'データ(現時点のゴールのスナップショット)MessagePackで圧縮'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $goal_clear_evaluates = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'フォロワーID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'cleared_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'Clear: 1, Not Clear:0'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $goal_labels = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'アクションリザルトID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'label_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ラベルID(belongsToでLabelモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0),
			'label_id' => array('column' => 'label_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $goal_members = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ゴールメンバーID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'タイプ(0 = コラボレータ,1 = リーダー)'),
		'role' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '役割', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '説明', 'charset' => 'utf8mb4'),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
		'approval_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '認定ステータス(0: 新規,1: 再認定依頼中,2: コーチが認定処理済み,3: コーチーが取り下げた)'),
		'is_wish_approval' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '認定対象希望フラグ'),
		'is_target_evaluation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価対象フラグ'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $goals = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ゴールID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_category_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリ'),
		'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '名前', 'charset' => 'utf8mb4'),
		'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ゴール画像', 'charset' => 'utf8mb4'),
		'evaluate_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価フラグ'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'ステータス(0 = 進行中, 1 = 中断, 2 = 完了)'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '説明', 'charset' => 'utf8mb4'),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => '開始日'),
		'end_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => '終了日'),
		'progress' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '進捗%'),
		'completed' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
		'action_result_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクショントカウント'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールを削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールを追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールを更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'start_date' => array('column' => 'start_date', 'unique' => 0),
			'end_date' => array('column' => 'end_date', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $group_insights = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'group_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'target_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
		'user_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'group_id_target_date' => array('column' => array('group_id', 'target_date'), 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $group_visions = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'グループビジョン名', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'グループビジョンの説明', 'charset' => 'utf8mb4'),
		'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '画像', 'charset' => 'utf8mb4'),
		'create_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ユーザID(belongsToでUserモデルに関連)'),
		'modify_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '最終編集者ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'group_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'グループID(belongsToでGroupモデルに関連)'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(0の場合はアーカイブ)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'create_user_id' => array('column' => 'create_user_id', 'unique' => 0),
			'modify_user_id' => array('column' => 'modify_user_id', 'unique' => 0),
			'group_id' => array('column' => 'group_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $groups = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '部署ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '部署名', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '部署の説明', 'charset' => 'utf8mb4'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $invites = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '招待ID'),
		'from_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '招待元ユーザID(belongsToでUserモデルに関連)'),
		'to_user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '招待先ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアド', 'charset' => 'utf8mb4'),
		'message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '招待メッセージ', 'charset' => 'utf8mb4'),
		'email_verified' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
		'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8mb4'),
		'email_token_expires' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3, 'unsigned' => true, 'comment' => '招待タイプ(0:通常招待,1:一括登録)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
			'to_user_id' => array('column' => 'to_user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'email' => array('column' => 'email', 'unique' => 0),
			'email_token' => array('column' => 'email_token', 'unique' => 0, 'length' => array('email_token' => '191'))
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $invoice_histories = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'order_datetime' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'order date (unixtimestamp)'),
		'system_order_code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '後払い.comから返される注文ID', 'charset' => 'utf8mb4'),
		'order_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '後払い.comから返される与信状況。与信OK:1、与信NG:2、与信中:0 '),
		'reorder_target_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '再注文対象の後払い.com注文ID', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'order_date' => array('column' => 'order_datetime', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $invoice_histories_charge_histories = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'invoice_history_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'belongsTo InvoiceHistory'),
		'charge_history_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'belongsTo ChargeHistory'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'invoice_history_id_2' => array('column' => array('invoice_history_id', 'charge_history_id'), 'unique' => 1),
			'created' => array('column' => 'created', 'unique' => 0),
			'invoice_history_id' => array('column' => 'invoice_history_id', 'unique' => 0),
			'charge_history_id' => array('column' => 'charge_history_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $invoices = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'payment_setting_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
		'credit_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '与信審査ステータス(0: 審査待ち,1: 与信OK,2: 与信NG)'),
		'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company name', 'charset' => 'utf8mb4'),
		'company_post_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 16, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(post_code)', 'charset' => 'utf8mb4'),
		'company_region' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(region)', 'charset' => 'utf8mb4'),
		'company_city' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(city)', 'charset' => 'utf8mb4'),
		'company_street' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(street)', 'charset' => 'utf8mb4'),
		'contact_person_first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.first name', 'charset' => 'utf8mb4'),
		'contact_person_first_name_kana' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.first name kana', 'charset' => 'utf8mb4'),
		'contact_person_last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.last name', 'charset' => 'utf8mb4'),
		'contact_person_last_name_kana' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.last name kana', 'charset' => 'utf8mb4'),
		'contact_person_tel' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.tel number', 'charset' => 'utf8mb4'),
		'contact_person_email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.email address', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $job_categories = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '職種ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '職種名', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '職種の説明', 'charset' => 'utf8mb4'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $key_results = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'キーリザルトID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
		'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '名前', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '説明', 'charset' => 'utf8mb4'),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => '開始日'),
		'end_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => '終了日'),
		'current_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '現在値'),
		'start_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '開始値'),
		'target_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '目標値'),
		'value_unit' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '目標値の単位'),
		'progress' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '進捗%'),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
		'completed' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
		'action_result_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクショントカウント'),
		'latest_actioned' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '最新アクション日時(unixtime)'),
		'tkr_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'TopKeyResult'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'latest_actioned' => array('column' => 'latest_actioned', 'unique' => 0),
			'start_date' => array('column' => 'start_date', 'unique' => 0),
			'end_date' => array('column' => 'end_date', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $kr_change_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザーID'),
		'key_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '種別(0:KR編集時ログ, 1:コーチ認定時ログ)'),
		'data' => array('type' => 'binary', 'null' => false, 'default' => null, 'comment' => 'KRのスナップショット(MessagePackで圧縮)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'key_result_id' => array('column' => 'key_result_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $kr_progress_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザーID'),
		'key_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID'),
		'action_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'unique', 'comment' => 'アクションID'),
		'value_unit' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '進捗単位'),
		'before_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => true, 'comment' => '更新前進捗値'),
		'change_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => false, 'comment' => '進捗増減値'),
		'target_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => true, 'comment' => '進捗目標値'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'action_result_id' => array('column' => 'action_result_id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $kr_values_daily_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'key_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでGoalモデルに関連)'),
		'current_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '現在値'),
		'start_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '開始値'),
		'target_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '目標値'),
		'target_date' => array('type' => 'date', 'null' => true, 'default' => null, 'key' => 'index', 'comment' => '対象日'),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'target_date_key_result_id' => array('column' => array('target_date', 'key_result_id'), 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'key_result_id' => array('column' => 'key_result_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $labels = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_bin', 'comment' => 'ラベル', 'charset' => 'utf8'),
		'goal_label_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ゴールラベルのカウンタキャッシュ'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_name_team_id' => array('column' => array('name', 'team_id'), 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $local_names = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ローカル名ID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'language' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 3, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8mb4'),
		'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '名', 'charset' => 'utf8mb4'),
		'last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '姓', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを登録した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを最後に更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'language' => array('column' => 'language', 'unique' => 0),
			'first_name' => array('column' => 'first_name', 'unique' => 0),
			'last_name' => array('column' => 'last_name', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $member_groups = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'group_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'グループID(belongsToでGroupモデルに関連)'),
		'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'グループの順序'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'group_id' => array('column' => 'group_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $member_types = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '部署ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'タイプ名(正社員等', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'タイプの説明(正規雇用で企業に雇われた労働者等', 'charset' => 'utf8mb4'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $message_files = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TopicID(belongsTo Topic Model)'),
		'message_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'MessageID(belongsTo Message Model)'),
		'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'AttachedFileID(belongsTo AttachedFile Model)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
		'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'display order'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'topic_id' => array('column' => 'topic_id', 'unique' => 0),
			'message_id' => array('column' => 'message_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $messages = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'topic ID(belongsTo Topic Model)'),
		'sender_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserID as Sender(belongsTo User Model)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Body of message', 'charset' => 'utf8mb4'),
        'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Message body\'s detected language', 'charset' => 'utf8mb4'),
        'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'Message Type(1:Nomal,2:Add member,3:Remove member,4:Change topic name)'),
		'meta_data' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Json data for not normal message(add/leave member ids, updated topic title, etc)', 'charset' => 'utf8mb4'),
		'attached_file_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0),
			'user_id' => array('column' => 'sender_user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $mst_price_plan_groups = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'Currency type(ex 1: yen, 2: US Dollar...)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $mst_price_plans = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'group_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'External key:mst_price_plan_groups.id'),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Unique price plan code. Rule {group_id}-{order} (ex. 1-1,1-2,2-1,2-2)', 'charset' => 'utf8mb4'),
		'price' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Fixed monthly charge amount'),
		'max_members' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'Maximum number of members in the plan'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'max_members' => array('column' => 'max_members', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $mst_translation_languages = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'language' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'unique', 'collate' => 'utf8mb4_general_ci', 'comment' => 'ISO 639-1 Language code', 'charset' => 'utf8mb4'),
		'importance' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => 'Language importance'),
		'intl_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8mb4_general_ci', 'comment' => 'International name of the language. e.g. Japanese', 'charset' => 'utf8mb4'),
		'local_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Local name of the language. e.g. 日本語', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'language' => array('column' => 'language', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $notify_settings = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'email_status' => array('type' => 'string', 'null' => false, 'default' => 'all', 'length' => 16, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Email notification setting'),
		'mobile_status' => array('type' => 'string', 'null' => false, 'default' => 'all', 'length' => 16, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Mobile notification setting'),
		'feed_post_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '閲覧可能な投稿があった際のアプリ通知'),
		'feed_post_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '閲覧可能な投稿があった際のメール通知'),
		'feed_post_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '閲覧可能な投稿があった際のモバイル通知'),
		'feed_commented_on_my_post_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の投稿にコメントがあった際のアプリ通知'),
		'feed_commented_on_my_post_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の投稿にコメントがあった際のメール通知'),
		'feed_commented_on_my_post_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の投稿にコメントがあった際のモバイル通知'),
		'feed_commented_on_my_commented_post_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がコメントした投稿にコメントがあった際のアプリ通知'),
		'feed_commented_on_my_commented_post_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がコメントした投稿にコメントがあった際のメール通知'),
		'feed_commented_on_my_commented_post_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がコメントした投稿にコメントがあった際のモバイル通知'),
		'circle_user_join_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のアプリ通知'),
		'circle_user_join_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のメール通知'),
		'circle_user_join_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のモバイル通知'),
		'circle_changed_privacy_setting_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のアプリ通知'),
		'circle_changed_privacy_setting_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のメール通知'),
		'circle_changed_privacy_setting_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のモバイル通知'),
		'circle_add_user_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '誰かが自分をサークルに追加した際のアプリ通知'),
		'circle_add_user_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '誰かが自分をサークルに追加した際のメール通知'),
		'circle_add_user_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '誰かが自分をサークルに追加した際のモバイル通知'),
		'my_goal_follow_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがフォローされたときのアプリ通知'),
		'my_goal_follow_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがフォローされたときのメール通知'),
		'my_goal_follow_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがフォローされたときのモバイル通知'),
		'my_goal_collaborate_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがコラボレートされたときのアプリ通知'),
		'my_goal_collaborate_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがコラボレートされたときのメール通知'),
		'my_goal_collaborate_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがコラボレートされたときのモバイル通知'),
		'my_goal_changed_by_leader_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのアプリ通知'),
		'my_goal_changed_by_leader_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのメール通知'),
		'my_goal_changed_by_leader_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのモバイル通知'),
		'my_goal_target_for_evaluation_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象となったときのアプリ通知'),
		'my_goal_target_for_evaluation_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象となったときのメール通知'),
		'my_goal_target_for_evaluation_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象となったときのモバイル通知'),
		'my_goal_as_leader_request_to_change_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がリーダーのゴールが修正依頼を受けたときのアプリ通知'),
		'my_goal_as_leader_request_to_change_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がリーダーのゴールが修正依頼を受けたときのメール通知'),
		'my_goal_as_leader_request_to_change_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がリーダーのゴールが修正依頼を受けたときのモバイル通知'),
		'my_goal_not_target_for_evaluation_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象外となったときのアプリ通知'),
		'my_goal_not_target_for_evaluation_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象外となったときのメール通知'),
		'my_goal_not_target_for_evaluation_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象外となったときのモバイル通知'),
		'my_member_create_goal_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのアプリ通知'),
		'my_member_create_goal_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのメール通知'),
		'my_member_create_goal_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのモバイル通知'),
		'my_member_collaborate_goal_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのアプリ通知'),
		'my_member_collaborate_goal_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのメール通知'),
		'my_member_collaborate_goal_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのモバイル通知'),
		'my_member_change_goal_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのアプリ通知'),
		'my_member_change_goal_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのメール通知'),
		'my_member_change_goal_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのモバイル通知'),
		'start_evaluation_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価開始となったときのアプリ通知'),
		'start_evaluation_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価開始となったときのメール通知'),
		'start_evaluation_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価開始となったときのモバイル通知'),
		'fleeze_evaluation_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価凍結となったときのアプリ通知'),
		'fleeze_evaluation_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価凍結となったときのメール通知'),
		'fleeze_evaluation_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価凍結となったときのモバイル通知'),
		'start_can_oneself_evaluation_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が自己評価できる状態になったときのアプリ通知'),
		'start_can_oneself_evaluation_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が自己評価できる状態になったときのメール通知'),
		'start_can_oneself_evaluation_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が自己評価できる状態になったときのモバイル通知'),
		'start_can_evaluate_as_evaluator_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者としての自分が評価できる状態になったときのアプリ通知'),
		'start_can_evaluate_as_evaluator_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者としての自分が評価できる状態になったときのメール通知'),
		'start_can_evaluate_as_evaluator_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者としての自分が評価できる状態になったときのモバイル通知'),
		'my_evaluator_evaluated_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者が自分の評価をしたときのアプリ通知'),
		'my_evaluator_evaluated_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者が自分の評価をしたときのメール通知'),
		'my_evaluator_evaluated_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者が自分の評価をしたときのモバイル通知'),
		'final_evaluation_is_done_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのアプリ通知'),
		'final_evaluation_is_done_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのメール通知'),
		'final_evaluation_is_done_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのモバイル通知'),
		'feed_commented_on_my_action_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のアクションに「コメント」されたときのアプリ通知'),
		'feed_commented_on_my_action_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のアクションに「コメント」されたときのメール通知'),
		'feed_commented_on_my_action_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のアクションに「コメント」されたときのモバイル通知'),
		'feed_commented_on_my_commented_action_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のコメントしたアクションに「コメント」されたときのアプリ通知'),
		'feed_commented_on_my_commented_action_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のコメントしたアクションに「コメント」されたときのメール通知'),
		'feed_commented_on_my_commented_action_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のコメントしたアクションに「コメント」されたときのモバイル通知'),
		'feed_action_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なアクションがあったときのアプリ通知'),
		'feed_action_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なアクションがあったときのメール通知'),
		'feed_action_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なアクションがあったときのモバイル通知'),
		'user_joined_to_invited_team_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのアプリ通知'),
		'user_joined_to_invited_team_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのメール通知'),
		'user_joined_to_invited_team_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのモバイル通知'),
		'feed_message_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なメッセージがあったときのアプリ通知'),
		'feed_message_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なメッセージがあったときのメール通知'),
		'feed_message_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なメッセージがあったときのモバイル通知'),
		'setup_guide_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'セットアップガイドからのアプリ通知'),
		'setup_guide_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'セットアップガイドからのメール通知'),
		'setup_guide_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'セットアップガイドからのモバイル通知'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '登録した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'feed_mentioned_in_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'feed_mentioned_in_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'feed_mentioned_in_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $oauth_tokens = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'OauthトークンID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'プロバイダタイプ(1:FB,2:Google)'),
		'uid' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'プロバイダー固有ID', 'charset' => 'utf8mb4'),
		'token' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'トークン', 'charset' => 'utf8mb4'),
		'expires' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'トークン認証期限(この期限が切れた場合は再度、トークン発行)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログイン紐付け解除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログインを登録した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログインを最後に更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'uid' => array('column' => 'uid', 'unique' => 0, 'length' => array('uid' => '191')),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $payment_setting_change_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'payment_setting_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
		'data' => array('type' => 'binary', 'null' => false, 'default' => null, 'comment' => '変更後のスナップショット'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $payment_settings = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'charge type(0: Invoice, 1: Credit card)'),
		'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'currencty type(ex 1: yen, 2: US Doller...)'),
		'amount_per_user' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'unsigned' => true, 'comment' => 'Service use amount per user'),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'paid plan start date(team timezone)'),
		'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company name', 'charset' => 'utf8mb4'),
		'company_country' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(country)', 'charset' => 'utf8mb4'),
		'company_post_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 16, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(post_code)', 'charset' => 'utf8mb4'),
		'company_region' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(region)', 'charset' => 'utf8mb4'),
		'company_city' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(city)', 'charset' => 'utf8mb4'),
		'company_street' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(street)', 'charset' => 'utf8mb4'),
		'company_tel' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company tel number', 'charset' => 'utf8mb4'),
		'contact_person_first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.first name', 'charset' => 'utf8mb4'),
		'contact_person_first_name_kana' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.first name kana', 'charset' => 'utf8mb4'),
		'contact_person_last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.last name', 'charset' => 'utf8mb4'),
		'contact_person_last_name_kana' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.last name kana', 'charset' => 'utf8mb4'),
		'contact_person_tel' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.tel number', 'charset' => 'utf8mb4'),
		'contact_person_email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.email address', 'charset' => 'utf8mb4'),
		'payment_base_day' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Payment base day(1 - 31)'),
		'payment_skip_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'Skip monthly charge'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_drafts = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= teams.id'),
		'post_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '= posts.id (set if draft published)'),
		'draft_data' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Json encoded draft post data', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_files = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_likes = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿いいねID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'いいねしたユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'post_user_unique' => array('column' => array('post_id', 'user_id'), 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_mentions = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿メンションID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メンションユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_reads = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿読んだID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'post_user_unique' => array('column' => array('post_id', 'user_id'), 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_resources = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'post_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= posts.id'),
		'post_draft_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= post_drafts.id'),
		'resource_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3, 'unsigned' => true, 'comment' => 'type of resource e.g. image, video, ...'),
		'resource_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'resource table\'s primary key id'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'post_draft_id' => array('column' => 'post_draft_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_share_circles = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿共有ユーザID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'circle_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '共有サークルID(belongsToでCircleモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'share_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '共有タイプ(0:shared, 1:only_notify)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'circle_id' => array('column' => 'circle_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_share_users = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿共有ユーザID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '共有ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'share_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '共有タイプ(0:shared, 1:only_notify)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $post_shared_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '共有ユーザを追加した人のID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'shared_list' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '共有ログjson', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $posts = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿ID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿作成ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿本文', 'charset' => 'utf8mb4'),
        'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Detected language of the post body', 'charset' => 'utf8mb4'),
        'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
		'comment_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
		'post_like_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'いいね数(post_likesテーブルni'),
		'post_read_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '読んだ数'),
		'important_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'goal_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'circle_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'サークルID'),
		'action_result_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'アクション結果ID'),
		'key_result_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'KR ID'),
		'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像1', 'charset' => 'utf8mb4'),
		'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像2', 'charset' => 'utf8mb4'),
		'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像3', 'charset' => 'utf8mb4'),
		'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像4', 'charset' => 'utf8mb4'),
		'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像5', 'charset' => 'utf8mb4'),
		'site_info' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サイト情報', 'charset' => 'utf8mb4'),
		'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'team_id_modified' => array('column' => array('team_id', 'modified'), 'unique' => 0),
			'action_result_id' => array('column' => 'action_result_id', 'unique' => 0),
			'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
			'circle_id' => array('column' => 'circle_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $price_plan_purchase_teams = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'price_plan_code' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'External key: mst_price_plans.code', 'charset' => 'utf8mb4'),
		'purchase_datetime' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'price_plan_code' => array('column' => 'price_plan_code', 'unique' => 0, 'length' => array('price_plan_code' => '191')),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $recovery_codes = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コード持ち主のuser_id'),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アプリ側で暗号化済のコード', 'charset' => 'utf8mb4'),
		'used' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コードを利用した日時'),
		'available_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'コード利用可能フラグ'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $saved_posts = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'post_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'saved_posts_unique' => array('column' => array('post_id', 'user_id'), 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'post_id' => array('column' => 'post_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'created' => array('column' => 'created', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $schema_migrations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'class' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $send_mail_to_users = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'send_mail_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メール送信ID(belongsToでSendMailモデルに関連)'),
		'user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メール送信を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'send_mail_id' => array('column' => 'send_mail_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $send_mails = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'メール送信ID'),
		'from_user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'template_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'メールテンプレタイプ'),
		'item' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アイテム(JSONエンコード)', 'charset' => 'utf8mb4'),
		'sent_datetime' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を実行した日付時刻'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $subscribe_emails = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアド', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '登録した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '最後に更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'email' => array('column' => 'email', 'unique' => 0, 'length' => array('email' => '191'))
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    public $team_configs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'config' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Json encoded team config', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $team_insights = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'target_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
		'user_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id_target_date' => array('column' => array('team_id', 'target_date'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

    public $team_login_methods = array(
        'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'Team ID'),
        'method' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Login method of the team', 'charset' => 'utf8mb4'),
        'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => array('team_id', 'method'), 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    public $team_members = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'チームメンバーID'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'coach_user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コーチのユーザID(belongsToでUserモデルに関連)'),
		'member_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メンバーナンバー(組織内でメンバーを識別する為のナンバー。exp社員番号)', 'charset' => 'utf8mb4'),
		'member_type_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メンバータイプID(belongsToでmember_typesモデルに関連)'),
		'job_category_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '職種ID(belongsToでJobCategoryモデルに関連)'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '有効フラグ(Offの場合はチームにログイン不可。チームメンバーによる当該メンバーのチーム内のコンテンツへのアクセスは可能。当該メンバーへの如何なる発信は不可)'),
		'invitation_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '招待中フラグ(招待済みで非アクティブユーザの管理用途)'),
		'evaluation_enable_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価対象フラグ(Offの場合は評価が不可能。対象ページへのアクセスおよび、一切の評価のアクションが行えない。)'),
		'admin_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
		'evaluable_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '要評価件数'),
		'last_login' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チーム最終ログイン日時'),
		'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント', 'charset' => 'utf8mb4'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクティブステータス(0: 招待中,1: アクティブ,2: インアクティブ)'),
        'default_translation_language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Default translation language for the user in a team', 'charset' => 'utf8mb4'),
        'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームから外れた日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームに参加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームメンバー設定を更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'coach_user_id' => array('column' => 'coach_user_id', 'unique' => 0),
			'job_category_id' => array('column' => 'job_category_id', 'unique' => 0),
			'member_type_id' => array('column' => 'member_type_id', 'unique' => 0),
			'member_no' => array('column' => 'member_no', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

    public $team_sso_settings = array(
        'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'unique', 'comment' => 'Team ID'),
        'endpoint' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2000, 'collate' => 'utf8mb4_general_ci', 'comment' => 'SAML2.0 Endpoint URL', 'charset' => 'utf8mb4'),
        'idp_issuer' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2000, 'collate' => 'utf8mb4_general_ci', 'comment' => 'IdP Entity ID', 'charset' => 'utf8mb4'),
        'public_cert' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2000, 'collate' => 'utf8mb4_general_ci', 'comment' => 'x.509 Public certificate of IdP', 'charset' => 'utf8mb4'),
        'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

	public $team_translation_languages = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'Team ID'),
		'language' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ISO 639-1 Language code', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => array('team_id', 'language'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $team_translation_statuses = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'unique', 'comment' => 'Team ID'),
		'circle_post_total' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated circle post'),
		'circle_post_comment_total' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated comment of circle post'),
		'action_post_total' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated action post'),
		'action_post_comment_total' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated comment of action post'),
        'message_total' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated message'),
        'total_limit' => array('type' => 'biginteger', 'null' => false, 'default' => '10000', 'unsigned' => true, 'comment' => 'Total translation limit of the team'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $team_translation_usage_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Team ID'),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => null),
		'end_date' => array('type' => 'date', 'null' => false, 'default' => null),
		'translation_log' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation log, in JSON format', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $team_visions = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'チームビジョン名', 'charset' => 'utf8mb4'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'チームビジョンの説明', 'charset' => 'utf8mb4'),
		'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '画像', 'charset' => 'utf8mb4'),
		'create_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ユーザID(belongsToでUserモデルに関連)'),
		'modify_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '最終編集者ユーザID(belongsToでUserモデルに関連)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(0の場合はアーカイブ)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'create_user_id' => array('column' => 'create_user_id', 'unique' => 0),
			'modify_user_id' => array('column' => 'modify_user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $teams = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'チームID'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'チーム名', 'charset' => 'utf8mb4'),
		'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'チームロゴ画像', 'charset' => 'utf8mb4'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'),
		'domain_limited_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'ドメイン限定フラグ(ONの場合は、指定されたドメイン名のメアドを所有していないとチームにログインできない)'),
		'domain_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ドメイン名', 'charset' => 'utf8mb4'),
		'start_term_month' => array('type' => 'integer', 'null' => false, 'default' => '4', 'length' => 3, 'unsigned' => true, 'comment' => '期間の開始月(入力可能な値は1〜12)'),
		'border_months' => array('type' => 'integer', 'null' => false, 'default' => '6', 'length' => 3, 'unsigned' => true, 'comment' => '期間の月数(４半期なら3,半年なら6, 0を認めない)'),
		'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームのタイムゾーン'),
		'service_use_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => 'サービス利用ステータス(0: free trial,1: payed,2: read only,3: service expired,4: manual delete,5: auto delete)'),
		'country' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8mb4_general_ci', 'comment' => '国コード', 'charset' => 'utf8mb4'),
		'default_translation_language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Default translation language for the team', 'charset' => 'utf8mb4'),
		'service_use_state_start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => '各ステートの開始日'),
		'service_use_state_end_date' => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => '各ステートの終了日'),
		'pre_register_amount_per_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'Amount per user before registering payment plan'),
		'ope_user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チーム情報変更者'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'service_use_status' => array('column' => 'service_use_status', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $terms = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID'),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => '期開始日'),
		'end_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => '期終了日'),
		'evaluate_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価ステータス(0 = 評価開始前, 1 = 評価中,2 = 評価凍結中, 3 = 最終評価終了)'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'start_date' => array('column' => 'start_date', 'unique' => 0),
			'end_date' => array('column' => 'end_date', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $terms_of_services = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'text_ja' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'body of terms of service(ja)', 'charset' => 'utf8mb4'),
		'text_en' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'body of terms of service(en)', 'charset' => 'utf8mb4'),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'application start date of terms of service'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'start_date' => array('column' => 'start_date', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $tkr_change_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
		'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
		'key_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでKeyResultモデルに関連)'),
		'data' => array('type' => 'binary', 'null' => false, 'default' => null, 'comment' => 'データ(現時点のTKRのスナップショット)MessagePackで圧縮'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'goal_id' => array('column' => 'goal_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'key_result_id' => array('column' => 'key_result_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $topic_members = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TopicID(belongsTo Topic Model)'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserID as Topic Member(belongsTo User Model)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
		'last_read_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'message_id as last read.'),
		'last_read_message_datetime' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'It\'s update when read message.'),
		'last_message_sent' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'It\'s update when member send message.'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'topic_id' => array('column' => 'topic_id', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'last_read_message_id' => array('column' => 'last_read_message_id', 'unique' => 0),
			'last_message_sent' => array('column' => 'last_message_sent', 'unique' => 0),
			'last_read_message_datetime' => array('column' => 'last_read_message_datetime', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $topic_search_keywords = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'unique', 'comment' => 'TopicID'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
		'keywords' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Keywords ', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_topic_id' => array('column' => 'topic_id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $topics = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
		'creator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserId as topic creator(belongsTo User Model)'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 254, 'collate' => 'utf8mb4_general_ci', 'comment' => 'topic title', 'charset' => 'utf8mb4'),
		'latest_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => 11, 'unsigned' => true, 'key' => 'index', 'comment' => 'latest message id associated with topic'),
		'latest_message_datetime' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'latest messaged datetime associated with topic'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0),
			'modified' => array('column' => 'modified', 'unique' => 0),
			'user_id' => array('column' => 'creator_user_id', 'unique' => 0),
			'latest_message_id' => array('column' => 'latest_message_id', 'unique' => 0),
			'latest_message_datetime' => array('column' => 'latest_message_datetime', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $translations = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'content_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'key' => 'index', 'comment' => 'Translation content type'),
		'content_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Translation content ID'),
		'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation content', 'charset' => 'utf8mb4'),
		'language' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation language', 'charset' => 'utf8mb4'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'Translation status'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'content_type' => array('column' => array('content_type', 'content_id', 'language'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $users = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ユーザID'),
		'password' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'パスワード(暗号化)', 'charset' => 'utf8mb4'),
		'password_token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'パスワードトークン(パスワード失念時の認証用)', 'charset' => 'utf8mb4'),
		'2fa_secret' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '２要素認証シークレットキー', 'charset' => 'utf8mb4'),
		'password_modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'パスワード最終更新日'),
		'no_pass_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'パスワード未使用フラグ(ソーシャルログインのみ利用時)'),
		'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'プロフィール画像', 'charset' => 'utf8mb4'),
		'cover_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4'),
		'primary_email_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'プライマリメールアドレスID(hasOneでEmailモデルに関連)'),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'アクティブフラグ(ユーザ認証済みの場合On)'),
		'last_login' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '最終ログイン日時'),
		'admin_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '管理者フラグ(管理画面が開ける人)'),
		'default_team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'デフォルトチーム(belongsToでTeamモデルに関連)'),
		'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'タイムゾーン(UTCを起点とした時差)'),
		'auto_timezone_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自動タイムゾーンフラグ(Onの場合はOSからタイムゾーンを取得する)'),
		'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8mb4'),
		'auto_language_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自動言語設定フラグ(Onの場合はブラウザから言語を取得する)'),
		'romanize_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'ローマ字表記フラグ(Onの場合は自分の名前がアプリ内で英語表記になる)。local_first_name,local_last_nameが入力されていても、first_name,last_nameがつかわれる。'),
		'update_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '更新情報メールフラグ(Onの場合はアプリから更新情報がメールで届く)'),
		'agreed_terms_of_service_id' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '同意した最新の利用規約ID'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザが退会した日付時刻'),
		'gender_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => '性別(1:男,2:女)'),
		'birth_day' => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => '誕生日'),
		'hide_year_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '誕生日の年を隠すフラグ'),
		'phone_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => '電話番号', 'charset' => 'utf8mb4'),
		'hometown' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '出身地', 'charset' => 'utf8mb4'),
		'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント', 'charset' => 'utf8mb4'),
		'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '英名', 'charset' => 'utf8mb4'),
		'last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '英姓', 'charset' => 'utf8mb4'),
		'middle_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '英ミドルネーム', 'charset' => 'utf8mb4'),
		'setup_complete_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザーデータを登録した日付時刻'),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザーデータを最後に更新した日付時刻'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'primary_email_id' => array('column' => 'primary_email_id', 'unique' => 0),
			'default_team_id' => array('column' => 'default_team_id', 'unique' => 0),
			'password_token' => array('column' => 'password_token', 'unique' => 0),
			'first_name' => array('column' => 'first_name', 'unique' => 0),
			'last_name' => array('column' => 'last_name', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $video_streams = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'video_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '= videos.id'),
		'duration' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video stream duration second'),
		'aspect_ratio' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'streams width/height ratio'),
		'storage_path' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'utf8mb4_general_ci', 'comment' => 'cloud storage stored key', 'charset' => 'utf8mb4'),
		'output_version' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'specific version of output type'),
		'transcode_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => '-1=error, 0=none, 1=uploading(to cloud storage), 2=upload complete, 3=queued, 4=transcoding, 5=transcode complete'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $video_transcode_logs = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'video_transcode_logs.id'),
		'video_stream_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= video_streams.id'),
		'log' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Json encoded transcoding log', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'video_stream_id' => array('column' => 'video_stream_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $videos = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= teams.id'),
		'duration' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video duration second'),
		'width' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video width(px)'),
		'height' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video height(px)'),
		'hash' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 256, 'collate' => 'utf8mb4_general_ci', 'comment' => 'video file hash sha256', 'charset' => 'utf8mb4'),
		'file_size' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video file byte size'),
		'file_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'filename of original uploaded file', 'charset' => 'utf8mb4'),
		'resource_path' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'utf8mb4_general_ci', 'comment' => 'cloud storage stored key', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	public $view_price_plans = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary'),
		'group_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'External key:mst_price_plan_groups.id'),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Unique price plan code. Rule {group_id}-{order} (ex. 1-1,1-2,2-1,2-2)', 'charset' => 'utf8mb4'),
		'price' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Fixed monthly charge amount'),
		'max_members' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Maximum number of members in the plan'),
		'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'Currency type(ex 1: yen, 2: US Dollar...)'),
		'indexes' => array(

		),
		'tableParameters' => array('comment' => 'VIEW')
	);

}
