<?php

/**
 * EmailFixture

 */
class EmailFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'メアドID', 'charset' => 'utf8'),
        'user_id'             => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'email'               => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
        'email_verified'      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
        'email_token'         => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
        'email_token_expires' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'             => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを削除した日付時刻'),
        'created'             => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを登録した日付時刻'),
        'modified'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを最後に更新した日付時刻'),
        'indexes'             => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'email'   => array('column' => 'email', 'unique' => 0)
        ),
        'tableParameters'     => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                  => '537ce223-6738-4b91-a06e-433dac11b50b',
            'user_id'             => '537ce224-8c0c-4c99-be76-433dac11b50b',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 0,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-8eac-4f79-80e9-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 0,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-aa68-4ddf-9468-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 0,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-c494-4105-a131-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-df24-41b5-a02c-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-f8ec-4533-b839-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-12b4-4661-b9ad-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-2ce0-42e8-83e2-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-34b0-4119-a4d2-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-3514-47e1-893b-433dac11b50b',
            'user_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
    );

}
