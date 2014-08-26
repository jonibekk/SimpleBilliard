<?php

class AddIndexes extends CakeMigration
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
            'create_field' => array(
                'badges'     => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像', 'charset' => 'utf8', 'after' => 'description'),
                ),
                'emails'     => array(
                    'indexes' => array(
                        'user_id'     => array('column' => 'user_id', 'unique' => 0),
                        'email_token' => array('column' => 'email_token', 'unique' => 0),
                    ),
                ),
                'send_mails' => array(
                    'indexes' => array(
                        'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
                        'to_user_id'   => array('column' => 'to_user_id', 'unique' => 0),
                    ),
                ),
                'teams'      => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームロゴ画像', 'charset' => 'utf8', 'after' => 'name'),
                ),
                'users'      => array(
                    'indexes' => array(
                        'primary_email_id' => array('column' => 'primary_email_id', 'unique' => 0),
                        'default_team_id'  => array('column' => 'default_team_id', 'unique' => 0),
                    ),
                ),
            ),
            'drop_field'   => array(
                'badges' => array('photo',),
                'teams'  => array('photo',),
            ),
            'alter_field'  => array(
                'emails'     => array(
                    'user_id'     => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
                ),
                'send_mails' => array(
                    'from_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'to_user_id'   => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                ),
                'users'      => array(
                    'primary_email_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'プライマリメールアドレスID(hasOneでEmailモデルに関連)', 'charset' => 'utf8'),
                    'default_team_id'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'デフォルトチーム(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'badges'     => array('photo_file_name',),
                'emails'     => array('', 'indexes' => array('user_id', 'email_token')),
                'send_mails' => array('', 'indexes' => array('from_user_id', 'to_user_id')),
                'teams'      => array('photo_file_name',),
                'users'      => array('', 'indexes' => array('primary_email_id', 'default_team_id')),
            ),
            'create_field' => array(
                'badges' => array(
                    'photo' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像', 'charset' => 'utf8'),
                ),
                'teams'  => array(
                    'photo' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームロゴ画像', 'charset' => 'utf8'),
                ),
            ),
            'alter_field'  => array(
                'emails'     => array(
                    'user_id'     => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
                ),
                'send_mails' => array(
                    'from_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'to_user_id'   => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                ),
                'users'      => array(
                    'primary_email_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'プライマリメールアドレスID(hasOneでEmailモデルに関連)', 'charset' => 'utf8'),
                    'default_team_id'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'デフォルトチーム(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                ),
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
