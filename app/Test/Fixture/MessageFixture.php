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
        'id'                  => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ],
        'topic_id'            => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'topic ID(belongsTo Topic Model)'
        ],
        'sender_user_id'      => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'UserID as Sender(belongsTo User Model)'
        ],
        'team_id'             => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'TeamID(belongsTo Team Model)'
        ],
        'body'                => [
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Body of message',
            'charset' => 'utf8mb4'
        ],
        'type'                => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '1',
            'length'   => 3,
            'unsigned' => true,
            'comment'  => 'Message Type(1:Nomal,2:Add member,3:Remove member,4:Change topic name)'
        ],
        'target_user_ids'     => [
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'comma spalated list for target users(e.g. 1,2,3) if add or remove members.',
            'charset' => 'utf8mb4'
        ],
        'attached_file_count' => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true
        ],
        'del_flg'             => [
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0'
        ],
        'deleted'             => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ],
        'created'             => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ],
        'modified'            => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ],
        'indexes'             => [
            'PRIMARY' => ['column' => ['id', 'created'], 'unique' => 1],
            'team_id' => ['column' => 'team_id', 'unique' => 0],
            'created' => ['column' => 'created', 'unique' => 0],
            'user_id' => ['column' => 'sender_user_id', 'unique' => 0]
        ],
        'tableParameters'     => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
