<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * GivenBadgeFixture
 */
class GivenBadgeFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '所有バッジID'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'バッジ所有ユーザID(belongsToでUserモデルに関連)'),
        'grant_user_id'   => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'バッジあげたユーザID(belongsToでUserモデルに関連)'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'post_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(hasOneでPostモデルに関連)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '所有バッジを追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'       => array('column' => 'id', 'unique' => 1),
            'user_id'       => array('column' => 'user_id', 'unique' => 0),
            'grant_user_id' => array('column' => 'grant_user_id', 'unique' => 0),
            'team_id'       => array('column' => 'team_id', 'unique' => 0),
            'post_id'       => array('column' => 'post_id', 'unique' => 0),
            'del_flg'       => array('column' => 'del_flg', 'unique' => 0),
            'created'       => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array();

}
