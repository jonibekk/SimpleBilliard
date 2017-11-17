<?php
class DelUnncessaryColumnRelatedCampaign extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'del_unncessary_column_related_campaign';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				'campaign_teams' => array('campaign_type', 'price'),
				'price_plan_purchase_teams' => array('price_plan_id'),
			),
		),
		'down' => array(
			'create_field' => array(
				'campaign_teams' => array(
					'campaign_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => '0:Fixed monthly charge 1:Discount amount per user'),
                    'price' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'Set value only if campaign_type = 1(Discount amount per user)'),
				),
				'price_plan_purchase_teams' => array(
					'price_plan_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
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
