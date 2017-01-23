<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * KeyResultFixture
 */
class KeyResultFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                  => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'キーリザルトID'
        ),
        'team_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'goal_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
        ),
        'user_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '作成者ID(belongsToでUserモデルに関連)'
        ),
        'name'                => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '名前',
            'charset' => 'utf8mb4'
        ),
        'description'         => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '説明',
            'charset' => 'utf8mb4'
        ),
        'start_date'          => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '開始日(unixtime)'
        ),
        'end_date'            => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '終了日(unixtime)'
        ),
        'current_value'       => array(
            'type'     => 'decimal',
            'null'     => false,
            'default'  => '0.000',
            'length'   => '18,3',
            'unsigned' => false,
            'comment'  => '現在値'
        ),
        'start_value'         => array(
            'type'     => 'decimal',
            'null'     => false,
            'default'  => '0.000',
            'length'   => '18,3',
            'unsigned' => false,
            'comment'  => '開始値'
        ),
        'target_value'        => array(
            'type'     => 'decimal',
            'null'     => false,
            'default'  => '0.000',
            'length'   => '18,3',
            'unsigned' => false,
            'comment'  => '目標値'
        ),
        'value_unit'          => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => '目標値の単位'
        ),
        'progress'            => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '進捗%'
        ),
        'priority'            => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '3',
            'unsigned' => false,
            'comment'  => '重要度(1〜5)'
        ),
        'completed'           => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '完了日'
        ),
        'action_result_count' => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => 'アクショントカウント'
        ),
        'latest_actioned' => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '最新アクション日時(unixtime)'
        ),
        'tkr_flg'             => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => 'TopKeyResult'
        ),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ),
        'created'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '追加した日付時刻'
        ),
        'modified'            => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '更新した日付時刻'
        ),
        'indexes'             => array(
            'PRIMARY'    => array('column' => 'id', 'unique' => 1),
            'team_id'    => array('column' => 'team_id', 'unique' => 0),
            'goal_id'    => array('column' => 'goal_id', 'unique' => 0),
            'modified'   => array('column' => 'modified', 'unique' => 0),
            'user_id'    => array('column' => 'user_id', 'unique' => 0),
            'start_date' => array('column' => 'start_date', 'unique' => 0),
            'end_date'   => array('column' => 'end_date', 'unique' => 0)
        ),
        'tableParameters'     => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'            => '1',
            'team_id'       => '1',
            'goal_id'       => '1',
            'user_id'       => '1',
            'name'          => 'Lorem ipsum dolor sit amet',
            'value_unit'    => '0',
            'start_value'   => '10',
            'target_value'  => '100',
            'current_value' => '11',
            'completed'     => null,
        ],
        [
            'id'            => '2',
            'team_id'       => '1',
            'goal_id'       => '1',
            'user_id'       => '1',
            'name'          => 'Lorem ipsum dolor sit amet',
            'value_unit'    => '1',
            'start_value'   => '100',
            'target_value'  => '0',
            'current_value' => '99',
            'completed'     => null,
        ],
        [
            'id'            => '3',
            'team_id'       => '1',
            'goal_id'       => '1',
            'user_id'       => '1',
            'name'          => 'Lorem ipsum dolor sit amet',
            'value_unit'    => '2',
            'start_value'   => '0',
            'target_value'  => '1',
            'current_value' => '0',
            'completed'     => null,
        ],
        [
            'id'            => '4',
            'team_id'       => '1',
            'goal_id'       => '1',
            'user_id'       => '1',
            'name'          => 'Completed key result',
            'completed'     => '1',
            'value_unit'    => '2',
            'start_value'   => '0',
            'target_value'  => '1',
            'current_value' => '1',
            'completed'     => 12345,
        ],
        [
            'id'            => '5',
            'team_id'       => '1',
            'goal_id'       => '1',
            'user_id'       => '1',
            'name'          => 'test',
            'value_unit'    => '3',
            'start_value'   => '0.001',
            'target_value'  => '123456789012345.999',
            'current_value' => '0.003',
            'completed'     => null,
        ],
        [
            'id'            => '6',
            'team_id'       => '1',
            'goal_id'       => '1',
            'user_id'       => '1',
            'name'          => 'test',
            'value_unit'    => '4',
            'start_value'   => '123456789012345',
            'target_value'  => '1',
            'current_value' => '123456789012343',
            'completed'     => '12345',
        ],
    ];

}
