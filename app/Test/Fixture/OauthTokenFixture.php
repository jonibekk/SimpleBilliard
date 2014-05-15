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
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'OauthトークンID', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'プロバイダタイプ(1:FB,2:Google)'),
        'uid'             => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'プロバイダー固有ID', 'charset' => 'utf8'),
        'token'           => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'トークン', 'charset' => 'utf8'),
        'expires'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'トークン認証期限(この期限が切れた場合は再度、トークン発行)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ソーシャルログイン紐付け解除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ソーシャルログインを登録した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ソーシャルログインを最後に更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'uid'     => array('column' => 'uid', 'unique' => 0)
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
            'id'       => '53746f16-3228-4a4a-b38d-0d9cac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 1,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-15 16:39:02',
            'del_flg'  => 1,
            'deleted'  => '2014-05-15 16:39:02',
            'created'  => '2014-05-15 16:39:02',
            'modified' => '2014-05-15 16:39:02'
        ),
    );

}
