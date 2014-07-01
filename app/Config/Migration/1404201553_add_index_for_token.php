<?php

class AddIndexForToken extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'invites'    => array(
                    'email'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
                    'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
                ),
                'send_mails' => array(
                    'to_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                ),
                'users'      => array(
                    'password_token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'パスワードトークン(パスワード失念時の認証用)', 'charset' => 'utf8'),
                ),
            ),
            'create_field' => array(
                'invites' => array(
                    'indexes' => array(
                        'email'       => array('column' => 'email', 'unique' => 0),
                        'email_token' => array('column' => 'email_token', 'unique' => 0),
                    ),
                ),
                'users'   => array(
                    'indexes' => array(
                        'password_token' => array('column' => 'password_token', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'invites'    => array(
                    'email'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
                    'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
                ),
                'send_mails' => array(
                    'to_user_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                ),
                'users'      => array(
                    'password_token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'パスワードトークン(パスワード失念時の認証用)', 'charset' => 'utf8'),
                ),
            ),
            'drop_field'  => array(
                'invites' => array('', 'indexes' => array('email', 'email_token')),
                'users'   => array('', 'indexes' => array('password_token')),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
