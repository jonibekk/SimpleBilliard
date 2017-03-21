<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * MessageFixture
 */
class MessageFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'                  => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'topic_id'            => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'topic ID(belongsTo Topic Model)'
        ),
        'sender_user_id'      => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'UserID as Sender(belongsTo User Model)'
        ),
        'team_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'TeamID(belongsTo Team Model)'
        ),
        'body'                => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Body of message',
            'charset' => 'utf8mb4'
        ),
        'type'                => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '1',
            'length'   => 3,
            'unsigned' => true,
            'comment'  => 'Message Type(1:Nomal,2:Add member,3:Remove member,4:Change topic name)'
        ),
        'target_user_ids'     => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'comma spalated list for target users(e.g. 1,2,3) if add or remove members.',
            'charset' => 'utf8mb4'
        ),
        'attached_file_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'             => array('type'     => 'integer',
                                       'null'     => false,
                                       'default'  => null,
                                       'unsigned' => true,
                                       'key'      => 'primary'
        ),
        'modified'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'             => array(
            'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'created' => array('column' => 'created', 'unique' => 0),
            'user_id' => array('column' => 'sender_user_id', 'unique' => 0)
        ),
        'tableParameters'     => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
