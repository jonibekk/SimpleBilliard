<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * TopicMemberFixture
 */
class TopicMemberFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'                   => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ],
        'topic_id'             => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'TopicID(belongsTo Topic Model)'
        ],
        'user_id'              => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'UserID as Topic Member(belongsTo User Model)'
        ],
        'team_id'              => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'TeamID(belongsTo Team Model)'
        ],
        'last_read_message_id' => [
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'message_id as last read.'
        ],
        'last_message_sent'    => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'It\'s update when member send message.'
        ],
        'del_flg'              => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'              => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'              => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'             => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'key'      => 'index'
        ],
        'indexes'              => [
            'PRIMARY'              => ['column' => 'id', 'unique' => 1],
            'topic_id'             => ['column' => 'topic_id', 'unique' => 0],
            'user_id'              => ['column' => 'user_id', 'unique' => 0],
            'team_id'              => ['column' => 'team_id', 'unique' => 0],
            'modified'             => ['column' => 'modified', 'unique' => 0],
            'last_read_message_id' => ['column' => 'last_read_message_id', 'unique' => 0],
            'last_message_sent'    => ['column' => 'last_message_sent', 'unique' => 0]
        ],
        'tableParameters'      => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
