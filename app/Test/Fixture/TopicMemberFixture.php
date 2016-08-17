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
        'id'              => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'トピック共有ユーザID'
        ],
        'topic_id'        => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'トピックID(belongsToでPostモデルに関連)'
        ],
        'user_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '共有ユーザID(belongsToでUserモデルに関連)'
        ],
        'team_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
        'deleted'         => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'トピックを削除した日付時刻'
        ],
        'created'         => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'トピックを追加した日付時刻'
        ],
        'modified'        => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'トピックを更新した日付時刻'
        ],
        'indexes'         => [
            'PRIMARY'  => ['column' => 'id', 'unique' => 1],
            'topic_id' => ['column' => 'topic_id', 'unique' => 0],
            'user_id'  => ['column' => 'user_id', 'unique' => 0],
            'team_id'  => ['column' => 'team_id', 'unique' => 0],
            'modified' => ['column' => 'modified', 'unique' => 0]
        ],
        'tableParameters' => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
