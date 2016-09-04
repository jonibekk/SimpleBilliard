<?php
class ModifyTkrTables0905 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modify_tkr_tables_0905';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'goal_change_logs' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
					'data' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'データ(現時点のゴールのスナップショット)MessagePackで圧縮', 'charset' => 'utf8mb4'),
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
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'goal_clear_evaluates' => array(
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
						'goal_id' => array('column' => 'goal_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'goal_labels' => array(
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
						'label_id' => array('column' => 'label_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'labels' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'name' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'tkr_change_logs' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
					'key_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでKeyResultモデルに関連)'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
					'data' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'データ(現時点のTKRのスナップショット)MessagePackで圧縮', 'charset' => 'utf8mb4'),
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
						'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'drop_field' => array(
				'goals' => array('purpose_id', 'current_value', 'start_value', 'target_value', 'value_unit', 'indexes' => array('purpose_id')),
			),
			'create_field' => array(
				'key_results' => array(
					'tkr_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'TopKeyResult', 'after' => 'action_result_count'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'goal_change_logs', 'goal_clear_evaluates', 'goal_labels', 'labels', 'tkr_change_logs'
			),
			'create_field' => array(
				'goals' => array(
					'purpose_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '目的ID(belongsToでTeamモデルに関連)'),
					'current_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '現在値'),
					'start_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '開始値'),
					'target_value' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '18,3', 'unsigned' => false, 'comment' => '目標値'),
					'value_unit' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '目標値の単位'),
					'indexes' => array(
						'purpose_id' => array('column' => 'purpose_id', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'key_results' => array('tkr_flg'),
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
