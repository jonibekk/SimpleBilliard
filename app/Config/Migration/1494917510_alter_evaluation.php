<?php
class AlterEvaluation extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_evaluation';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'evaluations' => array(
					'term_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '評価対象期間ID', 'after' => 'evaluator_user_id'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'evaluations' => array('term_id'),
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
