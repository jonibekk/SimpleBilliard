<?php
class ImproveTimezoneAndTerm1 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'improve_timezone_and_term_1';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'rename_field' => array(
                'goals'       => array(
                    'start_date' => 'old_start_date',
                    'end_date' => 'old_end_date'
                ),
                'key_results' => array(
                    'start_date' => 'old_start_date',
                    'end_date' => 'old_end_date'
                ),
            ),
			'create_table' => array(
				'terms' => array(
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
						'end_date' => array('column' => 'end_date', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
            'rename_field' => array(
                'goals'       => array(
                    'old_start_date' => 'start_date',
                    'old_end_date' => 'end_date'
                ),
                'key_results' => array(
                    'old_start_date' => 'start_date',
                    'old_end_date' => 'end_date'
                ),
            ),
            'drop_table' => array(
				'terms'
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
