<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * GoalChangeLogFixture
 */
class GoalChangeLogFixture extends CakeTestFixtureEx
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
        'team_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ],
        'goal_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
        ],
        'data'            => [
            'type'    => 'binary',
            'null'    => false,
            'default' => null,
            'comment' => 'データ(現時点のゴールのスナップショット)MessagePackで圧縮',
        ],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
        'deleted'         => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ],
        'created'         => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '追加した日付時刻'
        ],
        'modified'        => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '更新した日付時刻'
        ],
        'indexes'         => [
            'PRIMARY'  => ['column' => 'id', 'unique' => 1],
            'team_id'  => ['column' => 'team_id', 'unique' => 0],
            'goal_id'  => ['column' => 'goal_id', 'unique' => 0],
            'modified' => ['column' => 'modified', 'unique' => 0],
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
