<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * KrValuesDailyLog Fixture
 */
class KrValuesDailyLogFixture extends CakeTestFixtureEx
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
        'key_result_id'   => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'キーリザルトID(belongsToでGoalモデルに関連)'
        ],
        'current_value'   => [
            'type'     => 'decimal',
            'null'     => false,
            'default'  => '0.000',
            'length'   => '18,3',
            'unsigned' => false,
            'comment'  => '現在値'
        ],
        'start_value'     => [
            'type'     => 'decimal',
            'null'     => false,
            'default'  => '0.000',
            'length'   => '18,3',
            'unsigned' => false,
            'comment'  => '開始値'
        ],
        'target_value'    => [
            'type'     => 'decimal',
            'null'     => false,
            'default'  => '0.000',
            'length'   => '18,3',
            'unsigned' => false,
            'comment'  => '目標値'
        ],
        'target_date'     => [
            'type'    => 'date',
            'null'    => true,
            'default' => null,
            'key'     => 'index',
            'comment' => '対象日'
        ],
        'priority'        => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '3',
            'unsigned' => false,
            'comment'  => '重要度(1〜5)'
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
            'comment'  => '更新した日付時刻'
        ],
        'indexes'         => [
            'PRIMARY'                   => ['column' => 'id', 'unique' => 1],
            'target_date_key_result_id' => ['column' => ['target_date', 'key_result_id'], 'unique' => 1],
            'team_id'                   => ['column' => 'team_id', 'unique' => 0],
            'goal_id'                   => ['column' => 'goal_id', 'unique' => 0],
            'key_result_id'             => ['column' => 'key_result_id', 'unique' => 0]
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
