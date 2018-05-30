<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * EmailFixture
 */
class EmailFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                  => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'メアドID'
        ),
        'user_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ユーザID(belongsToでUserモデルに関連)'
        ),
        'email'               => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'key'     => 'index',
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'メアド',
            'charset' => 'utf8mb4'
        ),
        'email_verified'      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
        'email_token'         => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'key'     => 'index',
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)',
            'charset' => 'utf8mb4'
        ),
        'email_token_expires' => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => false,
            'comment'  => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'
        ),
        'del_flg'             => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'key'     => 'index',
            'comment' => '削除フラグ'
        ),
        'deleted'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'メアドを削除した日付時刻'
        ),
        'created'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'メアドを登録した日付時刻'
        ),
        'modified'            => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'メアドを最後に更新した日付時刻'
        ),
        'indexes'             => array(
            'PRIMARY'     => array('column' => 'id', 'unique' => 1),
            'email'       => array('column' => 'email', 'unique' => 0),
            'user_id'     => array('column' => 'user_id', 'unique' => 0),
            'email_token' => array('column' => 'email_token', 'unique' => 0),
            'del_flg'     => array('column' => 'del_flg', 'unique' => 0)
        ),
        'tableParameters'     => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
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
            'email_verified'      => 1,
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
            'email'               => 'to_aaa@email.com',
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
            'email_verified'      => 1,
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
        array(
            'id'                  => '12',
            'user_id'             => '13',
            'email'               => 'xxxxxxx@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '13',
            'user_id'             => '9',
            'email'               => 'id_13_user_9@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '14',
            'user_id'             => '10',
            'email'               => 'id_14_user_10@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '15',
            'user_id'             => '11',
            'email'               => 'id_15_user_11@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '16',
            'user_id'             => '9001',
            'email'               => 'id_16_user_9001@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '17',
            'user_id'             => '9002',
            'email'               => 'id_17_user_9002@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '18',
            'user_id'             => '15',
            'email'               => 'id_18_user_15@email.com',
            'email_verified'      => 0,
            'email_token'         => '12345678',
            'email_token_expires' => 1495420083,
            'del_flg'             => 0,
            'deleted'             => 1400725683,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
    );

}
