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
        'id'                  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'メアドID'),
        'user_id'             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'email'               => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
        'email_verified'      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
        'email_token'         => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
        'email_token_expires' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを削除した日付時刻'),
        'created'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを登録した日付時刻'),
        'modified'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを最後に更新した日付時刻'),
        'indexes'             => array(
            'PRIMARY'     => array('column' => 'id', 'unique' => 1),
            'email'       => array('column' => 'email', 'unique' => 0),
            'user_id'     => array('column' => 'user_id', 'unique' => 0),
            'email_token' => array('column' => 'email_token', 'unique' => 0),
            'del_flg'     => array('column' => 'del_flg', 'unique' => 0)
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
            'id'                  => '1',
            'user_id'             => '1',
            'email'               => 'from@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '2',
            'user_id'             => '2',
            'email'               => 'test@aaa.com',
            'email_verified'      => 0,
            'email_token'         => '12345678',
            'email_token_expires' => 1495420083,
            'del_flg'             => 0,
            'deleted'             => '',
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '3',
            'user_id'             => '4',
            'email'               => 'test@abc.com',
            'email_verified'      => 0,
            'email_token'         => '12345',
            'email_token_expires' => 1495420083,
            'del_flg'             => 0,
            'deleted'             => '',
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '4',
            'user_id'             => '11',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 0,
            'email_token'         => '1234567890',
            'email_token_expires' => 1526956083,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '5',
            'user_id'             => '',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 1,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '6',
            'user_id'             => '',
            'email'               => 'Lorem ipsum dolor sit amet',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 1,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '7',
            'user_id'             => '',
            'email'               => 'no_verified@email.com',
            'email_verified'      => 0,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '8',
            'user_id'             => '',
            'email'               => 'standalone@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '9',
            'user_id'             => '10',
            'email'               => 'to@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => '',
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '10',
            'user_id'             => '12',
            'email'               => 'to@email.com',
            'email_verified'      => 0,
            'email_token'         => 'token_test0123456789',
            'email_token_expires' => 4083100083,
            'del_flg'             => 0,
            'deleted'             => '',
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '11',
            'user_id'             => '14',
            'email'               => 'csv_test@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
    );

}
