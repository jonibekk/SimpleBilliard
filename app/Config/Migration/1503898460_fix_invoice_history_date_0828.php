<?php
class FixInvoiceHistoryDate0828 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'fix_invoice_history_date_0828';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'invoice_histories' => array(
					'order_datetime' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'order date (unixtimestamp)', 'after' => 'team_id'),
					'indexes' => array(
						'order_date' => array('column' => 'order_datetime', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'invoice_histories' => array('order_date', 'indexes' => array('order_date')),
			),
		),
		'down' => array(
			'drop_field' => array(
				'invoice_histories' => array('order_datetime', 'indexes' => array('order_date')),
			),
			'create_field' => array(
				'invoice_histories' => array(
					'order_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => '注文登録時のローカル日付'),
					'indexes' => array(
						'order_date' => array(),
					),
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
