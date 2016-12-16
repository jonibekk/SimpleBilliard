<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * ActionResultFixture
 */
class ActionResultFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'アクションリザルトID'
        ),
        'team_id'          => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'goal_id'          => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
        ),
        'key_result_id'    => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'キーリザルトID(belongsToでGoalモデルに関連)'
        ),
        'key_result_before_value'         => array(
            'type'     => 'decimal',
            'null'     => true,
            'default'  => null,
            'length'   => '18,3',
            'unsigned' => true,
            'comment'  => 'KR進捗値(更新前)'
        ),
        'key_result_change_value'         => array(
            'type'     => 'decimal',
            'null'     => true,
            'default'  => null,
            'length'   => '18,3',
            'unsigned' => false,
            'comment'  => 'KR進捗増減値'
        ),
        'key_result_target_value'         => array(
            'type'     => 'decimal',
            'null'     => true,
            'default'  => null,
            'length'   => '18,3',
            'unsigned' => true,
            'comment'  => 'KR進捗目標値'
        ),
        'user_id'          => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '作成者ID(belongsToでUserモデルに関連)'
        ),
        'name'             => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '名前',
            'charset' => 'utf8mb4'
        ),
        'type'             => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => 'タイプ(0:user,1:goal,2:kr)'
        ),
        'completed'        => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '完了日'
        ),
        'photo1_file_name' => array('type'    => 'string',
                                    'null'    => true,
                                    'default' => null,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'アクションリザルト画像1',
                                    'charset' => 'utf8mb4'
        ),
        'photo2_file_name' => array('type'    => 'string',
                                    'null'    => true,
                                    'default' => null,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'アクションリザルト画像2',
                                    'charset' => 'utf8mb4'
        ),
        'photo3_file_name' => array('type'    => 'string',
                                    'null'    => true,
                                    'default' => null,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'アクションリザルト画像3',
                                    'charset' => 'utf8mb4'
        ),
        'photo4_file_name' => array('type'    => 'string',
                                    'null'    => true,
                                    'default' => null,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'アクションリザルト画像4',
                                    'charset' => 'utf8mb4'
        ),
        'photo5_file_name' => array('type'    => 'string',
                                    'null'    => true,
                                    'default' => null,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'アクションリザルト画像5',
                                    'charset' => 'utf8mb4'
        ),
        'note'             => array('type'    => 'text',
                                    'null'    => true,
                                    'default' => null,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'ノート',
                                    'charset' => 'utf8mb4'
        ),
        'del_flg'          => array('type'    => 'boolean',
                                    'null'    => false,
                                    'default' => '0',
                                    'key'     => 'index',
                                    'comment' => '削除フラグ'
        ),
        'deleted'          => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'comment'  => '削除した日付時刻'
        ),
        'created'          => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'comment'  => '追加した日付時刻'
        ),
        'modified'         => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'key'      => 'index',
                                    'comment'  => '更新した日付時刻'
        ),
        'indexes'          => array(
            'PRIMARY'       => array('column' => 'id', 'unique' => 1),
            'team_id'       => array('column' => 'team_id', 'unique' => 0),
            'del_flg'       => array('column' => 'del_flg', 'unique' => 0),
            'modified'      => array('column' => 'modified', 'unique' => 0),
            'goal_id'       => array('column' => 'goal_id', 'unique' => 0),
            'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
            'user_id'       => array('column' => 'user_id', 'unique' => 0)
        ),
        'tableParameters'  => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
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
            'goal_id' => '1',
            'name'    => 'test',
        ],
        [
            'id'      => '2',
            'user_id' => '101',
            'team_id' => '1',
            'goal_id' => '1',
            'name'    => 'test2',
        ],
        [
            'id'      => '3',
            'user_id' => '101',
            'team_id' => '1',
            'goal_id' => '1',
            'name'    => 'test3',
        ],
        [
            'id'      => '4',
            'user_id' => '102',
            'team_id' => '1',
            'goal_id' => '1',
            'name'    => 'test4',
        ],
        [
            'id'      => '5',
            'user_id' => '1',
            'team_id' => '1',
            'goal_id' => '6',
            'name'    => 'test5',
        ],
    ];
}
