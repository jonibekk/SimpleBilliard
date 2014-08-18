<?php

/**
 * SendMailToUserFixture

 */
class SendMailToUserFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'send_mail_id'    => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メール送信ID(belongsToでSendMailモデルに関連)'),
        'user_id'         => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
        'team_id'         => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'notification_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知ID(belongsToでNotificationモデルに関連)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メール送信を更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'         => array('column' => 'id', 'unique' => 1),
            'send_mail_id'    => array('column' => 'send_mail_id', 'unique' => 0),
            'user_id'         => array('column' => 'user_id', 'unique' => 0),
            'team_id'         => array('column' => 'team_id', 'unique' => 0),
            'del_flg'         => array('column' => 'del_flg', 'unique' => 0),
            'notification_id' => array('column' => 'notification_id', 'unique' => 0),
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
            'send_mail_id'    => '',
            'user_id'         => '',
            'team_id'         => '',
            'notification_id' => '',
            'del_flg'         => 1,
            'deleted'         => 1,
            'created'         => 1,
            'modified'        => 1
        ),
    );

}
