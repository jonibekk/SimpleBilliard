<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * ThreadFixture
 */
class ThreadFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'スレッドID'),
        'from_user_id'    => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)'),
        'to_user_id'      => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
        'status'          => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドステータス(1:Open,2:Close)'),
        'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'スレッド名', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'スレッドの詳細', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'スレッドを削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドを追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドを更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'      => array('column' => 'id', 'unique' => 1),
            'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
            'to_user_id'   => array('column' => 'to_user_id', 'unique' => 0),
            'del_flg'      => array('column' => 'del_flg', 'unique' => 0),
            'type'         => array('column' => 'type', 'unique' => 0),
            'status'       => array('column' => 'status', 'unique' => 0),
            'created'      => array('column' => 'created', 'unique' => 0),
            'modified'     => array('column' => 'modified', 'unique' => 0)
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
