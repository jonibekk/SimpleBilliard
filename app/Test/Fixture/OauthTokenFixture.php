<?php

/**
 * OauthTokenFixture

 */
class OauthTokenFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'OauthトークンID'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'type'            => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'プロバイダタイプ(1:FB,2:Google)'),
        'uid'             => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'プロバイダー固有ID', 'charset' => 'utf8'),
        'token'           => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'トークン', 'charset' => 'utf8'),
        'expires'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'トークン認証期限(この期限が切れた場合は再度、トークン発行)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログイン紐付け解除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログインを登録した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログインを最後に更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'uid'     => array('column' => 'uid', 'unique' => 0),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
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
            'id'       => '',
            'user_id'  => '',
            'type'     => 1,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 1,
            'del_flg'  => 1,
            'deleted'  => 1,
            'created'  => 1,
            'modified' => 1
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 2,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 2,
            'del_flg'  => 1,
            'deleted'  => 2,
            'created'  => 2,
            'modified' => 2
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 3,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 3,
            'del_flg'  => 1,
            'deleted'  => 3,
            'created'  => 3,
            'modified' => 3
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 4,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 4,
            'del_flg'  => 1,
            'deleted'  => 4,
            'created'  => 4,
            'modified' => 4
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 5,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 5,
            'del_flg'  => 1,
            'deleted'  => 5,
            'created'  => 5,
            'modified' => 5
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 6,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 6,
            'del_flg'  => 1,
            'deleted'  => 6,
            'created'  => 6,
            'modified' => 6
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 7,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 7,
            'del_flg'  => 1,
            'deleted'  => 7,
            'created'  => 7,
            'modified' => 7
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 8,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 8,
            'del_flg'  => 1,
            'deleted'  => 8,
            'created'  => 8,
            'modified' => 8
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 9,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 9,
            'del_flg'  => 1,
            'deleted'  => 9,
            'created'  => 9,
            'modified' => 9
        ),
        array(
            'id'       => '',
            'user_id'  => '',
            'type'     => 10,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => 10,
            'del_flg'  => 1,
            'deleted'  => 10,
            'created'  => 10,
            'modified' => 10
        ),
    );

}
