<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * PostSharedLogFixture
 */
class PostSharedLogFixture extends CakeTestFixtureEx
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
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'post_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '投稿ID(belongsToでPostモデルに関連)'
        ),
        'user_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '共有ユーザを追加した人のID(belongsToでUserモデルに関連)'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'shared_list'     => array(
            'type'    => 'text',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '共有ログjson',
            'charset' => 'utf8mb4'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '追加した日付時刻'
        ),
        'modified'        => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'post_id' => array('column' => 'post_id', 'unique' => 0),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'created' => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

}
