<?php
class CampaignPaymentOptmizations extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'campaign_payment_optmizations';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'campaign_charge_histories' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'charge_history_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'External key:charge_histories.id'),
					'campaign_team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'External key:campaign_teams.id'),
					'price_plan_purchase_team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'External key:price_plan_purchase_teams_id.id'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'charge_history_id' => array('column' => 'charge_history_id', 'unique' => 0),
						'campaign_team_id' => array('column' => 'campaign_team_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'view_price_plans' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary'),
					'group_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'External key:mst_price_plan_groups.id'),
					'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Unique price plan code. Rule {group_id}-{order} (ex. 1-1,1-2,2-1,2-2)', 'charset' => 'utf8mb4'),
					'price' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Fixed monthly charge amount'),
					'max_members' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Maximum number of members in the plan'),
					'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'Currency type(ex 1: yen, 2: US Dollar...)'),
					'indexes' => array(
					),
					'tableParameters' => array('comment' => 'VIEW'),
				),
			),
			'alter_field' => array(
				'campaign_teams' => array(
					'price' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'Set value only if campaign_type = 1(Discount amount per user)'),
					'start_date' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'Campaign contract start date(team timezone)'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
				),
				'mst_price_plan_groups' => array(
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
				),
				'mst_price_plans' => array(
					'price' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Fixed monthly charge amount'),
					'max_members' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'Maximum number of members in the plan'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
				),
				'payment_settings' => array(
					'start_date' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'paid plan start date(team timezone)'),
				),
				'price_plan_purchase_teams' => array(
					'price_plan_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
					'purchase_datetime' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'campaign_charge_histories', 'view_price_plans'
			),
			'alter_field' => array(
				'campaign_teams' => array(
					'price' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'Set value only if campaign_type = 1(Discount amount per user)'),
					'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'Campaign contract start date(team timezone)'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
				),
				'mst_price_plan_groups' => array(
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
				),
				'mst_price_plans' => array(
					'price' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'unsigned' => true, 'comment' => 'Fixed monthly charge amount'),
					'max_members' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'index', 'comment' => 'Maximum number of members in the plan'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
				),
				'payment_settings' => array(
					'start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'paid plan start date(team timezone)'),
				),
				'price_plan_purchase_teams' => array(
					'price_plan_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false),
					'purchase_datetime' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
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
