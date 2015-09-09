<?php

/**
 * TeamUserRankingFixture
 */
class TeamUserRankingFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
        'start_date'      => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date'        => array('type' => 'date', 'null' => true, 'default' => null, 'key' => 'index'),
        'timezone'        => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
        'post_count'      => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
        'action_count'    => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'         => array(
            'PRIMARY'            => array('column' => 'id', 'unique' => 1),
            'team_id_start_date' => array('column' => array('team_id', 'start_date'), 'unique' => 0),
            'end_date'           => array('column' => 'end_date', 'unique' => 0),
            'user_id'            => array('column' => 'user_id', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [];
}
