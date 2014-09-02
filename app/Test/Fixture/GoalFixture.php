<?php

/**
 * GoalFixture

 */
class GoalFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'キーリザルトID'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'goal_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
        'name'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
        'start_date'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日(unixtime)'),
        'end_date'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日(unixtime)'),
        'current_value'   => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '現在値'),
        'start_value'     => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '開始値'),
        'target_value'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '目標値'),
        'value_unit'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '目標値の単位'),
        'progress'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '進捗%'),
        'priority'        => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
        'completed'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
        'special_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '特別フラグ'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールカテゴリを更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'  => array('column' => 'id', 'unique' => 1),
            'team_id'  => array('column' => 'team_id', 'unique' => 0),
            'del_flg'  => array('column' => 'del_flg', 'unique' => 0),
            'goal_id'  => array('column' => 'goal_id', 'unique' => 0),
            'modified' => array('column' => 'modified', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'      => '1',
            'user_id' => '1',
            'team_id' => '1',
        ],
        [
            'id'      => '2',
            'user_id' => '1',
            'team_id' => '1',
        ],
        [
            'id'      => '3',
            'user_id' => '1',
            'team_id' => '1',
        ],
        [
            'id'      => '4',
            'user_id' => '1',
            'team_id' => '1',
        ],
    ];

}
