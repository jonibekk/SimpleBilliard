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
        'id'                                => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ],
        'topic_id'                          => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'topic ID(belongsToでTopicモデルに関連)'
        ],
        'user_id'                           => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'メッセしたユーザID(belongsToでUserモデルに関連)'
        ],
        'team_id'                           => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ],
        'body'                              => [
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'メッセ本文',
            'charset' => 'utf8mb4'
        ],
        'type'                              => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '1',
            'length'   => 3,
            'unsigned' => true,
            'comment'  => 'メッセタイプ(1:Nomal,2:メンバー追加,3:メンバー削除,4:メンバー離脱)'
        ],
        'target_user_ids_if_member_changed' => [
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '変更したユーザのuser_idをカンマ区切りで指定',
            'charset' => 'utf8mb4'
        ],
        'del_flg'                           => [
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => '削除フラグ'
        ],
        'deleted'                           => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ],
        'created'                           => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => '追加した日付時刻'
        ],
        'modified'                          => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '更新した日付時刻'
        ],
        'indexes'                           => [
            'PRIMARY' => ['column' => ['id'], 'unique' => 1],
            'user_id' => ['column' => 'user_id', 'unique' => 0],
            'team_id' => ['column' => 'team_id', 'unique' => 0],
            'created' => ['column' => 'created', 'unique' => 0]
        ],
        'tableParameters'                   => [
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        ]
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
