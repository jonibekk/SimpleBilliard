<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

class CacheUnreadCirclePostFixture extends CakeTestFixtureEx
{
    public $fields = [
        'id'              => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ],
        'team_id'         => [
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '= teams.id'
        ],
        'circle_id'       => [
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '= circles.id'
        ],
        'user_id'         => [
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '= users.id'
        ],
        'post_id'         => [
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '= posts.id'
        ],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
        'deleted'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'        => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'         => [
            'PRIMARY'     => ['column' => 'id', 'unique' => 1],
            'circle_user' => ['column' => ['circle_id', 'user_id'], 'unique' => 0],
            'circle_post' => ['column' => ['circle_id', 'post_id'], 'unique' => 0],
            'tuple'       => [
                'column' => ['team_id', 'circle_id', 'user_id', 'post_id'],
                'unique' => 1
            ],
        ],
        'tableParameters' => [
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        ]
    ];

    public $records = [];
}
