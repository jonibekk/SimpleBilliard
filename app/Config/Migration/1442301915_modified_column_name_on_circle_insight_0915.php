<?php
class ModifiedColumnNameOnCircleInsight0915 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modified_column_name_on_circle_insight_0915';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'circle_insights' => array(
					'user_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true, 'after' => 'circle_id'),
				),
			),
			'drop_field' => array(
				'circle_insights' => array('member_count'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'circle_insights' => array('user_count'),
			),
			'create_field' => array(
				'circle_insights' => array(
					'member_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
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
