<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * GoalProgressDailyLog Fixture
 */
class GoalProgressDailyLogFixture extends CakeTestFixtureEx
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
            'key'      => 'primary'
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
        'progress'        => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => false,
            'comment'  => '進捗率%(0-100)'
        ],
        'target_date'     => [
            'type'    => 'date',
            'null'    => false,
            'default' => null,
            'key'     => 'primary',
            'comment' => '対象の日付'
        ],
        'created'         => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true],
        'modified'        => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true],
        'indexes'         => [
            'PRIMARY'                    => ['column' => ['id', 'target_date'], 'unique' => 1],
            'goal_id_target_date_unique' => ['column' => ['goal_id', 'target_date'], 'unique' => 1],
            'target_date'                => ['column' => 'target_date', 'unique' => 0],
            'team_id'                    => ['column' => 'team_id', 'unique' => 0],
            'goal_id'                    => ['column' => 'goal_id', 'unique' => 0]
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
