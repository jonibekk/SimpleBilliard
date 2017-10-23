<?php
class CampaignPricePlan extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'campaign_price_plan';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'campaign_teams' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'campaign_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => '0:Fixed monthly charge 1:Discount amount per user'),
					'price_plan_group_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'External key:mst_price_plan_groups.id. Set value only if campaign_type = 0(Fixed monthly charge)'),
					'price' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'Set value only if campaign_type = 1(Discount amount per user)'),
					'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'Campaign contract start date(team timezone)'),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'mst_price_plan_groups' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'Currency type(ex 1: yen, 2: US Dollar...)'),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'mst_price_plans' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'group_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'External key:mst_price_plan_groups.id'),
					'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Unique price plan code. Rule {group_id}-{order} (ex. 1-1,1-2,2-1,2-2)', 'charset' => 'utf8mb4'),
					'price' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'unsigned' => true, 'comment' => 'Fixed monthly charge amount'),
					'max_members' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'index', 'comment' => 'Maximum number of members in the plan'),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'max_members' => array('column' => 'max_members', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'price_plan_purchase_teams' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'price_plan_code' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'External key: mst_price_plans.code', 'charset' => 'utf8mb4'),
					'purchase_datetime' => array('type' => 'datetime', 'null' => false, 'default' => null),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'price_plan_code' => array('column' => 'price_plan_code', 'unique' => 0, 'length' => array('price_plan_code' => '191')),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'payment_settings' => array(
					'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'paid plan start date(team timezone)', 'after' => 'amount_per_user'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'campaign_teams', 'mst_price_plan_groups', 'mst_price_plans', 'price_plan_purchase_teams'
			),
			'drop_field' => array(
				'payment_settings' => array('start_date'),
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
