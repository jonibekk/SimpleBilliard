<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * MemberGroupFixture
 */
class MemberGroupFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'group_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'グループID(belongsToでGroupモデルに関連)'),
        'index_num'       => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'グループの順序'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'  => array('column' => 'id', 'unique' => 1),
            'team_id'  => array('column' => 'team_id', 'unique' => 0),
            'user_id'  => array('column' => 'user_id', 'unique' => 0),
            'group_id' => array('column' => 'group_id', 'unique' => 0),
            'del_flg'  => array('column' => 'del_flg', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'        => '1',
            'team_id'   => '1',
            'user_id'   => '1',
            'group_id'  => '1',
            'index_num' => 0,
            'del_flg'   => false,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => '2',
            'team_id'   => '1',
            'user_id'   => '2',
            'group_id'  => '1',
            'index_num' => 1,
            'del_flg'   => false,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => '3',
            'team_id'   => '1',
            'user_id'   => '1',
            'group_id'  => '4',
            'index_num' => 0,
            'del_flg'   => false,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => '4',
            'team_id'   => '1',
            'user_id'   => '2',
            'group_id'  => '4',
            'index_num' => 1,
            'del_flg'   => false,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => '5',
            'team_id'   => '1',
            'user_id'   => '1',
            'group_id'  => '5',
            'index_num' => 0,
            'del_flg'   => false,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => '6',
            'team_id'   => '1',
            'user_id'   => '3',
            'group_id'  => '5',
            'index_num' => 1,
            'del_flg'   => false,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => '7',
            'team_id'   => '1',
            'user_id'   => '14',
            'group_id'  => '6',
            'index_num' => 1,
            'del_flg'   => false,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
    );

}
