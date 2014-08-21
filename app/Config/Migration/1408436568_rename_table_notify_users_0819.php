<?php

class RenameTableNotifyUsers0819 extends CakeMigration
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
            'create_table' => array(
                'notify_to_users' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '通知ユーザID'),
                    'notification_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知ID(belongsToでNotificationモデルに関連)'),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'unread_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '未読フラグ(通知を開いたらOff)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'         => array('column' => 'id', 'unique' => 1),
                        'user_id'         => array('column' => 'user_id', 'unique' => 0),
                        'team_id'         => array('column' => 'team_id', 'unique' => 0),
                        'notification_id' => array('column' => 'notification_id', 'unique' => 0),
                        'del_flg'         => array('column' => 'del_flg', 'unique' => 0),
                        'unread_flg'      => array('column' => 'unread_flg', 'unique' => 0),
                        'modified'        => array('column' => 'modified', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
            'drop_table'   => array(
                'notify_users'
            ),
        ),
        'down' => array(
            'drop_table'   => array(
                'notify_to_users'
            ),
            'create_table' => array(
                'notify_users' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '通知ユーザID'),
                    'notification_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知ID(belongsToでNotificationモデルに関連)'),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'unread_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '未読フラグ(通知を開いたらOff)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'         => array('column' => 'id', 'unique' => 1),
                        'user_id'         => array('column' => 'user_id', 'unique' => 0),
                        'team_id'         => array('column' => 'team_id', 'unique' => 0),
                        'notification_id' => array('column' => 'notification_id', 'unique' => 0),
                        'del_flg'         => array('column' => 'del_flg', 'unique' => 0),
                        'unread_flg'      => array('column' => 'unread_flg', 'unique' => 0),
                        'modified'        => array('column' => 'modified', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
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
