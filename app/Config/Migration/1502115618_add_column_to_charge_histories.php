<?php
class AddColumnToChargeHistories extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_to_charge_histories';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'charge_histories' => array(
				    // we need information on who went through the operation that caused the billing
					'user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '請求操作実行ユーザー', 'after' => 'team_id'),
					'total_amount_including_tax' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '17,2', 'unsigned' => false, 'comment' => 'total amount in a charge', 'after' => 'total_amount'),
				),
			),
			'alter_field' => array(
				'charge_histories' => array(
                    // type int→decimal
					'total_amount' => array('type' => 'decimal', 'null' => false, 'default' => '0.000', 'length' => '17,2', 'unsigned' => false, 'comment' => 'total amount in a charge'),
				),
				'teams' => array(
				    // Fix comment (add description 「4: manual delete,5: auto delete」
					'service_use_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => 'サービス利用ステータス(0: free trial,1: payed,2: read only,3: service expired,4: manual delete,5: auto delete)'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'charge_histories' => array('user_id', 'total_amount_including_tax'),
			),
			'alter_field' => array(
				'charge_histories' => array(
					'total_amount' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'total amount in a charge'),
				),
				'teams' => array(
					'service_use_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => 'サービス利用ステータス(0: free trial,1: payed,2: read only,3: service expired)'),
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
