<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');
/**
 * MstPricePlan Fixture
 */
class MstPricePlanFixture extends CakeTestFixtureEx {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
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
			'max_members' => array('column' => 'max_members', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

    public $records = [
        ['id' =>  1, 'group_id' => 1, 'code' => '1-1', 'price' =>  50000, 'max_members' =>  50,],
        ['id' =>  2, 'group_id' => 1, 'code' => '1-2', 'price' => 100000, 'max_members' => 200,],
        ['id' =>  3, 'group_id' => 1, 'code' => '1-3', 'price' => 150000, 'max_members' => 300,],
        ['id' =>  4, 'group_id' => 1, 'code' => '1-4', 'price' => 200000, 'max_members' => 400,],
        ['id' =>  5, 'group_id' => 1, 'code' => '1-5', 'price' => 250000, 'max_members' => 500,],
        ['id' =>  6, 'group_id' => 2, 'code' => '2-1', 'price' =>  500,   'max_members' =>  50,],
        ['id' =>  7, 'group_id' => 2, 'code' => '2-2', 'price' => 1000,   'max_members' => 200,],
        ['id' =>  8, 'group_id' => 2, 'code' => '2-3', 'price' => 1500,   'max_members' => 300,],
        ['id' =>  9, 'group_id' => 2, 'code' => '2-4', 'price' => 2000,   'max_members' => 400,],
        ['id' => 10, 'group_id' => 2, 'code' => '2-5', 'price' => 2500,   'max_members' => 500,],
    ];
}
