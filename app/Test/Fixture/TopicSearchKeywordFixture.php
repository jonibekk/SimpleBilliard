<?php

/**
 * TopicSearchKeyword Fixture
 */
class TopicSearchKeywordFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'              => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ],
        'topic_id'        => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'unique',
            'comment'  => 'TopicID'
        ],
        'team_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'TeamID(belongsTo Team Model)'
        ],
        'keywords'        => [
            'type'    => 'text',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Keywords ',
            'charset' => 'utf8mb4'
        ],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'        => ['type'     => 'integer',
                              'null'     => false,
                              'default'  => '0',
                              'unsigned' => true,
                              'key'      => 'index'
        ],
        'indexes'         => [
            'PRIMARY'         => ['column' => 'id', 'unique' => 1],
            'unique_topic_id' => ['column' => 'topic_id', 'unique' => 1],
            'team_id'         => ['column' => 'team_id', 'unique' => 0],
            'modified'        => ['column' => 'modified', 'unique' => 0]
        ],
        'tableParameters' => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

}
