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
        'id'               => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ゴールID'),
        'user_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'team_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'goal_category_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリ'),
        'purpose'          => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '目的', 'charset' => 'utf8'),
        'photo_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ゴール画像', 'charset' => 'utf8'),
        'valued_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '価値フラグ'),
        'evaluate_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価フラグ'),
        'status'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'ステータス(0 = 進行中, 1 = 中断, 2 = 完了)'),
        'priority'         => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
        'description'      => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
        'start_date'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日'),
        'compleated'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールを削除した日付時刻'),
        'created'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールを追加した日付時刻'),
        'modified'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールを更新した日付時刻'),
        'indexes'          => array(
            'PRIMARY'  => array('column' => 'id', 'unique' => 1),
            'modified' => array('column' => 'modified', 'unique' => 0),
            'user_id'  => array('column' => 'user_id', 'unique' => 0),
            'team_id'  => array('column' => 'team_id', 'unique' => 0),
            'del_flg'  => array('column' => 'del_flg', 'unique' => 0)
        ),
        'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
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
