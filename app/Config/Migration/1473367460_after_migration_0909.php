<?php
class AfterMigration0909 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'after_migration_0909';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'collaborators' => array(
					'approval_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '価値フラグ(0 = 処理前,1 = 承認, 2 = 保留,3 = 修正依頼, 4 = 差し戻し)'),
				),
				'labels' => array(
					'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'collaborators' => array(
					'approval_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '認定ステータス(0 = 処理前,1 = 承認, 2 = 保留,3 = 修正依頼, 4 = 差し戻し)'),
				),
				'labels' => array(
					'name' => array('type' => 'string', 'null' => true, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
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
