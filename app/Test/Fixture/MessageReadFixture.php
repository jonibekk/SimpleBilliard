<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * MessageReadFixture
 */
class MessageReadFixture extends CakeTestFixtureEx
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
            'comment'  => 'メッセ読んだID'
        ],
        'message_id'      => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'メッセID(belongsToでcommentモデルに関連)'
        ],
        'user_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '読んだしたユーザID(belongsToでUserモデルに関連)'
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
            'comment'  => 'メッセを削除した日付時刻'
        ],
        'created'         => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'メッセを追加した日付時刻'
        ],
        'modified'        => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'メッセを更新した日付時刻'
        ],
        'indexes'         => [
            'PRIMARY'             => ['column' => 'id', 'unique' => 1],
            'message_user_unique' => ['column' => ['message_id', 'user_id'], 'unique' => 1],
            'team_id'             => ['column' => 'team_id', 'unique' => 0],
            'message_id'          => ['column' => 'message_id', 'unique' => 0],
            'user_id'             => ['column' => 'user_id', 'unique' => 0]
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
