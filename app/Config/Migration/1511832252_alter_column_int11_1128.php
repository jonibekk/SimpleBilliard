<?php
class AlterColumnInt11_1128 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_column_int11_1128';

    /**
     * @var array $migration
     */
    public $migration = array(
        'up'   => [
            'alter_field' => array(
                'campaign_teams' => array(
                    'deleted'  => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'  => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified' => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                ),
                'mst_price_plan_groups' => array(
                    'deleted'  => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'  => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified' => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                ),
                'mst_price_plans' => array(
                    'price'       => array('type' => 'integer', 'length' => 11, 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Fixed monthly charge amount'),
                    'max_members' => array('type' => 'integer', 'length' => 11, 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'Maximum number of members in the plan'),
                    'deleted'     => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'     => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'    => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                ),
                'price_plan_purchase_teams' => array(
                    'deleted'           => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'           => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'          => array('type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'unsigned' => true),
                ),
            ),
        ],
        'down' => [
            'alter_field' => array(
                'campaign_teams' => array(
                    'deleted'  => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'  => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified' => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                ),
                'mst_price_plan_groups' => array(
                    'deleted'  => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'  => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified' => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                ),
                'mst_price_plans' => array(
                    'price'       => array('type' => 'integer', 'length' => 10, 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Fixed monthly charge amount'),
                    'max_members' => array('type' => 'integer', 'length' => 10, 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'Maximum number of members in the plan'),
                    'deleted'     => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'     => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'    => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                ),
                'price_plan_purchase_teams' => array(
                    'deleted'           => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'           => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'          => array('type' => 'integer', 'length' => 10, 'null' => true, 'default' => null, 'unsigned' => true),
                ),
            ),
        ],
    );

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
