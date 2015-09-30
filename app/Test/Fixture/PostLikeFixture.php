<?php

/**
 * PostLikeFixture

 */
class PostLikeFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿いいねID'),
        'post_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'いいねしたユーザID(belongsToでUserモデルに関連)'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'post_id' => array('column' => 'post_id', 'unique' => 0),
            'post_id_2' => array('column' => ['post_id', 'user_id'], 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'del_flg' => array('column' => 'del_flg', 'unique' => 0)
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
            'id'       => '1',
            'post_id'  => '100',
            'user_id'  => '1',
            'team_id'  => '1',
            'del_flg'  => 0,
            'deleted'  => 1,
            'created'  => 1,
            'modified' => 1
        ),
        array(
            'id'       => '2',
            'post_id'  => '100',
            'user_id'  => '2',
            'team_id'  => '1',
            'del_flg'  => 0,
            'deleted'  => 2,
            'created'  => 2,
            'modified' => 2
        ),
        array(
            'id'       => '3',
            'post_id'  => '300',
            'user_id'  => '3',
            'team_id'  => '1',
            'del_flg'  => 0,
            'deleted'  => 3,
            'created'  => 3,
            'modified' => 3
        ),
    );

}
