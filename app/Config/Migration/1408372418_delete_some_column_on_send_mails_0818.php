<?php

class DeleteSomeColumnOnSendMails0818 extends CakeMigration
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
            'drop_field' => array(
                'send_mail_to_users' => array('notification_id', 'indexes' => array('notification_id')),
                'send_mails'         => array('to_user_id', 'to_user_ids', 'indexes' => array('to_user_id')),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'send_mail_to_users' => array(
                    'notification_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知ID(belongsToでNotificationモデルに関連)'),
                    'indexes'         => array(
                        'notification_id' => array('column' => 'notification_id', 'unique' => 0),
                    ),
                ),
                'send_mails'         => array(
                    'to_user_id'  => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
                    'to_user_ids' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '送信元ユーザID(複数)jsonエンコード', 'charset' => 'utf8'),
                    'indexes'     => array(
                        'to_user_id' => array('column' => 'to_user_id', 'unique' => 0),
                    ),
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
