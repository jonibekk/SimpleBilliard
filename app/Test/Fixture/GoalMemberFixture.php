<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * GoalMemberFixture
 */
class GoalMemberFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                   => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'コラボレータID'
        ),
        'team_id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'goal_id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
        ),
        'user_id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ユーザID(belongsToでUserモデルに関連)'
        ),
        'type'                 => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => 'タイプ(0 = コラボレータ,1 = リーダー)'
        ),
        'role'                 => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '役割',
            'charset' => 'utf8mb4'
        ),
        'description'          => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '説明',
            'charset' => 'utf8mb4'
        ),
        'priority'             => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '3',
            'unsigned' => false,
            'comment'  => '重要度(1〜5)'
        ),
        'approval_status'      => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '認定ステータス(0: 新規,1: 再認定依頼中,2: コーチが認定処理済み,3: コーチーが取り下げた)'
        ),
        'is_wish_approval'     => array('type'    => 'boolean',
                                        'null'    => false,
                                        'default' => '1',
                                        'comment' => '認定対象希望フラグ'
        ),
        'is_target_evaluation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価対象フラグ'),
        'del_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'              => array('type'     => 'integer',
                                        'null'     => true,
                                        'default'  => null,
                                        'unsigned' => true,
                                        'comment'  => '削除した日付時刻'
        ),
        'created'              => array('type'     => 'integer',
                                        'null'     => true,
                                        'default'  => null,
                                        'unsigned' => true,
                                        'key'      => 'index',
                                        'comment'  => '追加した日付時刻'
        ),
        'modified'             => array('type'     => 'integer',
                                        'null'     => true,
                                        'default'  => null,
                                        'unsigned' => true,
                                        'comment'  => '更新した日付時刻'
        ),
        'indexes'              => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
            'goal_id' => array('column' => 'goal_id', 'unique' => 0),
            'created' => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters'      => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'          => 1,
            'team_id'     => 1,
            'goal_id'     => 1,
            'user_id'     => 1,
            'type'        => 1,
            'role'        => 'test',
            'description' => 'test',
        ],
        [
            'id'          => 2,
            'team_id'     => 1,
            'goal_id'     => 1,
            'user_id'     => 2,
            'type'        => 0,
            'role'        => 'test',
            'description' => 'test',
        ],
        [
            'id'          => 3,
            'team_id'     => 1,
            'goal_id'     => 7,
            'user_id'     => 1,
            'type'        => 0,
            'role'        => 'test',
            'description' => 'test',
        ],
        [
            'id'          => 4,
            'team_id'     => 1,
            'goal_id'     => 9,
            'user_id'     => 2,
            'type'        => 1,
            'role'        => 'test',
            'description' => 'test',
        ],
    ];

}
