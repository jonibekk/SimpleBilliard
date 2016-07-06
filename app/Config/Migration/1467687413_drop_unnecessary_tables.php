<?php
class DropUnnecessaryTables extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'drop_unnecessary_tables';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_table' => array(
				'actions', 'badges', 'cake_sessions'
			),
		),
		'down' => array(
			'create_table' => array(
				'actions' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'アクションID'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'goal_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
					'key_result_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでGoalモデルに関連)'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
					'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
					'priority' => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
					'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像1', 'charset' => 'utf8'),
					'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像2', 'charset' => 'utf8'),
					'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像3', 'charset' => 'utf8'),
					'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像4', 'charset' => 'utf8'),
					'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像5', 'charset' => 'utf8'),
					'start_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日(unixtime)'),
					'end_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日(unixtime)'),
					'repeat_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '繰り返しタイプ(0:disabled,1:daily,2:weekly,4:monthly)'),
					'mon_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '月曜'),
					'tues_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '火曜'),
					'wed_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '水曜'),
					'thurs_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '木曜'),
					'fri_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '金曜'),
					'sat_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '土曜'),
					'sun_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '日曜'),
					'monthly_day' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '月次の日にち'),
					'action_result_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクションリザルトカウント'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'goal_id' => array('column' => 'goal_id', 'unique' => 0),
						'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
						'modified' => array('column' => 'modified', 'unique' => 0),
						'user_id' => array('column' => 'user_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'badges' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'バッジID'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'バッジ作成ユーザID(belongsToでUserモデルに関連)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ詳細', 'charset' => 'utf8'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像', 'charset' => 'utf8'),
					'default_badge_no' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'デフォルトバッジNo(デフォルトで用意されているバッジ)'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
					'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
					'count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '利用されたカウント数(バッジが利用されるとカウントアップ。チーム管理者がリセット可能)'),
					'max_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '利用可能数(カウント数が利用可能数に達した場合、バッジを新たに付与する事ができなくなる。)'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを削除した日付時刻'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'user_id' => array('column' => 'user_id', 'unique' => 0),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'cake_sessions' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'data' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'expires' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
