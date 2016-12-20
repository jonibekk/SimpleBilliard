<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * GoalFixture
 */
class GoalFixture extends CakeTestFixtureEx
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
            'comment'  => 'ゴールID'
        ),
        'user_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ユーザID(belongsToでUserモデルに関連)'
        ),
        'team_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'goal_category_id'    => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'ゴールカテゴリ'
        ),
        'name'                => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '名前',
            'charset' => 'utf8mb4'
        ),
        'photo_file_name'     => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'ゴール画像',
            'charset' => 'utf8mb4'
        ),
        'evaluate_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価フラグ'),
        'status'              => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => 'ステータス(0 = 進行中, 1 = 中断, 2 = 完了)'
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
        'progress'            => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '進捗%'
        ),
        'completed'           => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'length'   => 10,
            'unsigned' => true
        ),
        'action_result_count' => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => 'アクショントカウント'
        ),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'ゴールを削除した日付時刻'
        ),
        'created'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'ゴールを追加した日付時刻'
        ),
        'modified'            => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ゴールを更新した日付時刻'
        ),
        'indexes'             => array(
            'PRIMARY'    => array('column' => 'id', 'unique' => 1),
            'modified'   => array('column' => 'modified', 'unique' => 0),
            'user_id'    => array('column' => 'user_id', 'unique' => 0),
            'team_id'    => array('column' => 'team_id', 'unique' => 0),
            'end_date'   => array('column' => 'end_date', 'unique' => 0),
            'start_date' => array('column' => 'start_date', 'unique' => 0)
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
            'id'      => '1',
            'user_id' => '1',
            'team_id' => '1',

            'name' => 'ゴール1',
            'start_date' => 10000,
            'end_date'   => 19999,
        ],
        [
            'id'      => '2',
            'user_id' => '1',
            'team_id' => '1',

            'name' => 'ゴール2',
            'start_date' => 10000,
            'end_date'   => 19999,
        ],
        [
            'id'      => '3',
            'user_id' => '1',
            'team_id' => '1',

            'name' => 'ゴール3',
            'start_date' => 10000,
            'end_date'   => 19999,
        ],
        [
            'id'      => '4',
            'user_id' => '1',
            'team_id' => '1',

            'name' => 'ゴール4',
            'start_date' => 10000,
            'end_date'   => 19999,
        ],
        [
            'id'      => '5',
            'user_id' => '14',

            'team_id' => '1',
            'start_date' => 10000,
            'end_date'   => 19999,
        ],
        [
            'id'      => '6',
            'user_id' => '1',

            'team_id' => '1',
            'start_date' => 10000,
            'end_date'   => 19999,
        ],
        [
            'id'      => '7',
            'user_id' => '2',
            'team_id' => '1',

            'name' => 'その他ゴール1',
            'start_date' => 10000,
            'end_date'   => 19999,
        ],
        [
            'id'      => '8',
            'user_id' => '1',
            'team_id' => '1',

            'start_date' => '25000',
            'end_date'   => '28000',
        ],
        [
            'id'      => '9',
            'user_id' => '2',
            'team_id' => '1',

            'start_date' => '15000',
            'end_date'   => '18000',
        ],
        [
            'id'      => '100',
            'user_id' => '100',
            'team_id' => '1',

        ],
    ];

}
