<?php
class FixedPkErrorOnDelFlg20180327 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'fixed_pk_error_on_del_flg_20180327';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'circle_pins' => array(
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
				),
			),
			'drop_field' => array(
				'evaluation_settings' => array('fixed_evaluation_order_flg', 'show_all_evaluation_before_freeze_flg'),
			),
		),
		'down' => array(
			'alter_field' => array(
				'circle_pins' => array(
					'del_flg' => array('type' => 'boolean', 'null' => true, 'default' => null),
				),
			),
			'create_field' => array(
				'evaluation_settings' => array(
					'fixed_evaluation_order_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価者による評価順序固定 on/off'),
					'show_all_evaluation_before_freeze_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価凍結前に他の評価者の評価を閲覧可能 on/off'),
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
