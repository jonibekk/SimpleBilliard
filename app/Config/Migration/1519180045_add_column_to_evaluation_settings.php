<?php
class AddColumnToEvaluationSettings extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_to_evaluation_settings';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'evaluation_settings' => array(
					'fixed_evaluation_order_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価者による評価順序固定 on/off', 'after' => 'leader_goal_comment_required_flg'),
					'show_all_evaluation_before_freeze_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価凍結前に他の評価者の評価を閲覧可能 on/off', 'after' => 'fixed_evaluation_order_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'evaluation_settings' => array('fixed_evaluation_order_flg', 'show_all_evaluation_before_freeze_flg'),
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
