<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * MessageFileFixture
 */
class MessageFileFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'               => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ],
        'topic_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'トピックID(belongsToでTopicモデルに関連)'
        ],
        'message_id'       => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'メッセージID(belongsToでMessageモデルに関連)'
        ],
        'attached_file_id' => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ファイルID(belongsToでFileモデルに関連)'
        ],
        'team_id'          => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ],
        'index_num'        => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => '表示順'
        ],
        'del_flg'          => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
        'deleted'          => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ],
        'created'          => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '追加した日付時刻'
        ],
        'modified'         => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '更新した日付時刻'
        ],
        'indexes'          => [
            'PRIMARY'          => ['column' => 'id', 'unique' => 1],
            'topic_id'         => ['column' => 'topic_id', 'unique' => 0],
            'message_id'       => ['column' => 'message_id', 'unique' => 0],
            'team_id'          => ['column' => 'team_id', 'unique' => 0],
            'attached_file_id' => ['column' => 'attached_file_id', 'unique' => 0]
        ],
        'tableParameters'  => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
