<?php

class DeleteNotificationsTable0507 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'delete_notifications_table_0507';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'drop_field' => array(
                'send_mails'   => array('notification_id', 'indexes' => array('notification_id')),
                'team_members' => array('notify_unread_count'),
            ),
            'drop_table' => array(
                'notifications', 'notify_from_users', 'notify_to_users'
            ),
        ),
        'down' => array(
            'create_field' => array(
                'send_mails'   => array(
                    'notification_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知ID(belongsToでNotificationモデルに関連)'),
                    'indexes'         => array(
                        'notification_id' => array('column' => 'notification_id', 'unique' => 0),
                    ),
                ),
                'team_members' => array(
                    'notify_unread_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '通知未読件数'),
                ),
            ),
            'create_table' => array(
                'notifications'     => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '通知ID'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'タイプ(1:ゴール,2:投稿,3:etc ...)'),
                    'from_user_id'    => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知元ユーザID(belongsToでUserモデルに関連)'),
                    'body'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '通知本文', 'charset' => 'utf8'),
                    'item_name'       => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アイテム名(投稿内容、コメント内容等)', 'charset' => 'utf8'),
                    'model_id'        => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'モデルID(feedならpost_id,circleならcircle_id)'),
                    'url_data'        => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'URLデータ(json)', 'charset' => 'utf8'),
                    'count_num'       => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'メッセージ内で利用する件数'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'      => array('column' => 'id', 'unique' => 1),
                        'team_id'      => array('column' => 'team_id', 'unique' => 0),
                        'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
                        'del_flg'      => array('column' => 'del_flg', 'unique' => 0),
                        'modified'     => array('column' => 'modified', 'unique' => 0),
                        'model_id'     => array('column' => 'model_id', 'unique' => 0),
                        'type'         => array('column' => 'type', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'notify_from_users' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'notification_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知ID(belongsToでNotificationモデルに関連)'),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
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
                        'modified'        => array('column' => 'modified', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'notify_to_users'   => array(
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
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
