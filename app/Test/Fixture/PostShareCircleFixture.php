<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * PostShareCircleFixture
 */
class PostShareCircleFixture extends CakeTestFixtureEx
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
            'comment'  => '投稿共有ユーザID'
        ),
        'post_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '投稿ID(belongsToでPostモデルに関連)'
        ),
        'circle_id'       => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '共有サークルID(belongsToでCircleモデルに関連)'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'share_type'      => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '共有タイプ(0:shared, 1:only_notify)'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '投稿を削除した日付時刻'
        ),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '投稿を追加した日付時刻'
        ),
        'modified'        => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '投稿を更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY'   => array('column' => 'id', 'unique' => 1),
            'post_id'   => array('column' => 'post_id', 'unique' => 0),
            'circle_id' => array('column' => 'circle_id', 'unique' => 0),
            'team_id'   => array('column' => 'team_id', 'unique' => 0),
            'created'   => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'        => 1,
            'post_id'   => 1,
            'circle_id' => 1,
            'team_id'   => 1,
            'del_flg'   => 0,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => 2,
            'post_id'   => 5,
            'circle_id' => 3,
            'team_id'   => 1,
            'del_flg'   => 0,
            'deleted'   => null,
            'created'   => 1388603000,
            'modified'  => 1388603000
        ),
        array(
            'id'        => 3,
            'post_id'   => 6,
            'circle_id' => 3,
            'team_id'   => 1,
            'del_flg'   => 0,
            'deleted'   => null,
            'created'   => 1388603000,
            'modified'  => 1388603000
        ),
        array(
            'id'        => 4,
            'post_id'   => 7,
            'circle_id' => 4,
            'team_id'   => 1,
            'del_flg'   => 0,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => 5,
            'post_id'   => 11,
            'circle_id' => 4,
            'team_id'   => 1,
            'del_flg'   => 0,
            'deleted'   => null,
            'created'   => 1,
            'modified'  => 1
        ),
        array(
            'id'        => 6,
            'post_id'   => 4,
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 2,
            'created'   => 2,
            'modified'  => 2
        ),
        array(
            'id'        => '7',
            'post_id'   => '',
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 3,
            'created'   => 3,
            'modified'  => 3
        ),
        array(
            'id'        => '8',
            'post_id'   => '',
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 4,
            'created'   => 4,
            'modified'  => 4
        ),
        array(
            'id'        => '9',
            'post_id'   => '',
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 5,
            'created'   => 5,
            'modified'  => 5
        ),
        array(
            'id'        => '10',
            'post_id'   => '',
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 6,
            'created'   => 6,
            'modified'  => 6
        ),
        array(
            'id'        => '11',
            'post_id'   => '',
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 7,
            'created'   => 7,
            'modified'  => 7
        ),
        array(
            'id'        => '12',
            'post_id'   => '',
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 8,
            'created'   => 8,
            'modified'  => 8
        ),
        array(
            'id'        => '13',
            'post_id'   => '',
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 9,
            'created'   => 9,
            'modified'  => 9
        ),
        array(
            'id'        => '14',
            'post_id'   => '',
            'circle_id' => '',
            'team_id'   => '',
            'del_flg'   => 1,
            'deleted'   => 10,
            'created'   => 10,
            'modified'  => 10
        ),
    );

}
