<?php

/**
 * GroupInsightFixture
 */
class GroupInsightFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                   => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'team_id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
        'group_id'             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
        'target_date'          => array('type' => 'date', 'null' => true, 'default' => null),
        'timezone'             => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
        'user_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'access_user_count'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'message_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'action_count'         => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'action_user_count'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'post_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'post_user_count'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'like_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'comment_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'collabo_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'collabo_action_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
        'del_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'              => array(
            'PRIMARY'              => array('column' => 'id', 'unique' => 1),
            'group_id_target_date' => array('column' => array('group_id', 'target_date'), 'unique' => 0),
            'team_id'              => array('column' => 'team_id', 'unique' => 0)
        ),
        'tableParameters'      => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

}
