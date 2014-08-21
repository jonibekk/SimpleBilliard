<?php

/**
 * NotifyToUserFixture

 */
class NotifyToUserFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
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
            'modified'        => array('column' => 'modified', 'unique' => 0)
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
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 1,
            'created'         => 1,
            'modified'        => 1
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 2,
            'created'         => 2,
            'modified'        => 2
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 3,
            'created'         => 3,
            'modified'        => 3
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 4,
            'created'         => 4,
            'modified'        => 4
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 5,
            'created'         => 5,
            'modified'        => 5
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 6,
            'created'         => 6,
            'modified'        => 6
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 7,
            'created'         => 7,
            'modified'        => 7
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 8,
            'created'         => 8,
            'modified'        => 8
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 9,
            'created'         => 9,
            'modified'        => 9
        ),
        array(
            'id'              => '',
            'notification_id' => '',
            'user_id'         => '',
            'team_id'         => '',
            'unread_flg'      => 1,
            'del_flg'         => 1,
            'deleted'         => 10,
            'created'         => 10,
            'modified'        => 10
        ),
    );

}
