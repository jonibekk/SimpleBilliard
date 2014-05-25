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
        'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'プロバイダタイプ(1:FB,2:Google)'),
        'uid'             => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'プロバイダー固有ID', 'charset' => 'utf8'),
        'token'           => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'トークン', 'charset' => 'utf8'),
        'expires'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'トークン認証期限(この期限が切れた場合は再度、トークン発行)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
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
            'id'       => '537ce224-ee54-4cbc-bade-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 1,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-10b4-4dd2-81cf-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 2,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-2a18-4209-a3d1-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 3,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-3f94-42d5-8711-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 4,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-6514-479b-9e2c-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 5,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-7e78-4061-8899-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 6,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-97dc-46ad-a937-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 7,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-b0dc-4946-b734-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 8,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-c9dc-42c7-a50d-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 9,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-e278-4a58-bbee-433dac11b50b',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'type'     => 10,
            'uid'      => 'Lorem ipsum dolor sit amet',
            'token'    => 'Lorem ipsum dolor sit amet',
            'expires'  => '2014-05-22 02:28:04',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
    );

}
