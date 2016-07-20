<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * TeamInsightFixture
 */
class TeamInsightFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ),
        'target_date'     => array('type' => 'date', 'null' => true, 'default' => null),
        'timezone'        => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
        'user_count'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'         => array(
            'PRIMARY'             => array('column' => 'id', 'unique' => 1),
            'team_id_target_date' => array('column' => array('team_id', 'target_date'), 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [];
}
