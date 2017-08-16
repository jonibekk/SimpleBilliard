<?php
class AddInvoceHistories0816 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_invoce_histories_0816';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'rename_field' => array(
                'charge_histories' => array(
                    'total_amount_including_tax' => 'tax'
                ),
            ),
			'alter_field' => array(
				'charge_histories' => array(
					'total_amount' => array('type' => 'decimal', 'null' => false, 'default' => '0.00', 'length' => '17,2', 'unsigned' => false, 'comment' => 'total amount excluding tax in a charge'),
                    'tax' => array('type' => 'decimal', 'null' => false, 'default' => '0.00', 'length' => '17,2', 'unsigned' => false, 'comment' => 'tax in a charge'),
				),
			),
            'create_table' => array(
				'invoice_histories' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'order_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => '注文登録時のUTC日付'),
					'system_order_code' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'comment' => '後払い.comから返される注文ID', 'charset' => 'utf8mb4'),
					'order_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '後払い.comから返される与信状況。与信OK:1、与信NG:2、与信中:0 '),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'order_date' => array('column' => 'order_date', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
            'rename_field' => array(
                'charge_histories' => array(
                    'tax' => 'total_amount_including_tax'
                ),
            ),
			'alter_field' => array(
				'charge_histories' => array(
					'total_amount' => array('type' => 'decimal', 'null' => false, 'default' => '0.00', 'length' => '17,2', 'unsigned' => false, 'comment' => 'total amount in a charge'),
                    'total_amount_including_tax' => array('type' => 'decimal', 'null' => false, 'default' => '0.00', 'length' => '17,2', 'unsigned' => false, 'comment' => 'total amount in a charge'),
				),
			),
			'drop_table' => array(
				'invoice_histories'
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
