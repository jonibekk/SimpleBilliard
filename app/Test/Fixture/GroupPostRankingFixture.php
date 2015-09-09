<?php

/**
 * GroupPostRankingFixture
 */
class GroupPostRankingFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
        'group_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
        'start_date'      => array('type' => 'date', 'null' => true, 'default' => null),
        'end_date'        => array('type' => 'date', 'null' => true, 'default' => null, 'key' => 'index'),
        'timezone'        => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
        'post_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
        'post_type'       => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true),
        'like_count'      => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
        'comment_count'   => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'         => array(
            'PRIMARY'             => array('column' => 'id', 'unique' => 1),
            'group_id_start_date' => array('column' => array('group_id', 'start_date'), 'unique' => 0),
            'team_id'             => array('column' => 'team_id', 'unique' => 0),
            'end_date'            => array('column' => 'end_date', 'unique' => 0),
            'post_id'             => array('column' => 'post_id', 'unique' => 0)
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
