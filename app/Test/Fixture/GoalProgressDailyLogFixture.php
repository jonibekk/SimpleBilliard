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
    public $fields = array(
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'goal_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
        ),
        'progress'        => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => false,
            'comment'  => '0-100の数字'
        ),
        'target_date'     => array(
            'type'    => 'date',
            'null'    => false,
            'default' => null,
            'key'     => 'primary',
            'comment' => '対象の日付'
        ),
        'created'         => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
        'modified'        => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
        'indexes'         => array(
            'PRIMARY' => array('column' => array('id', 'target_date'), 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'goal_id' => array('column' => 'goal_id', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
