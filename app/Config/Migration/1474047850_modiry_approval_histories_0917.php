<?php
class ModiryApprovalHistories0917 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modiry_approval_histories_0917';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'approval_histories' => array(
					'select_clear_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:no select, 1:is clear, 2:is not clear', 'after' => 'action_status'),
					'select_important_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:no select, 1:is important, 2:not important', 'after' => 'select_clear_status'),
				),
			),
			'drop_field' => array(
				'approval_histories' => array('is_clear_or_not', 'is_important_or_not'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'approval_histories' => array('select_clear_status', 'select_important_status'),
			),
			'create_field' => array(
				'approval_histories' => array(
					'is_clear_or_not' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:no select, 1:is clear, 2:is not clear'),
					'is_important_or_not' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:no select, 1:is important, 2:not important'),
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
