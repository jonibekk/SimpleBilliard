<?php
class CampaignPaymentOptimizations extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'campaign_payment_optimizations';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'create_field' => array(
                'charge_histories' => array(
                    'campaign_team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'campaign_team.id', 'after' => 'stripe_payment_code'),
                    'price_plan_purchase_team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'price_plan_purchase_teams.id', 'after' => 'campaign_team_id'),
                ),
            ),
            'alter_field' => array(
                'campaign_teams' => array(
                    'price' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'Set value only if campaign_type = 1(Discount amount per user)'),
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
			'drop_field' => array(
				'charge_histories' => array('campaign_team_id', 'price_plan_purchase_team_id'),
			),
            'alter_field' => array(
                'campaign_teams' => array(
                    'price' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'Set value only if campaign_type = 1(Discount amount per user)'),
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
