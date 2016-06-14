<?php

/**
 * MessageFixture
 */
class MessageFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'primary_key', 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'メッセージID'),
        'from_user_id'    => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)'),
        'to_user_id'      => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
        'thread_id'       => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドID(belongsToでThreadモデルに関連)'),
        'body'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メッセージ本文', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メッセージを削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メッセージを追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メッセージを更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'      => array('column' => 'id', 'unique' => 1),
            'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
            'to_user_id'   => array('column' => 'to_user_id', 'unique' => 0),
            'thread_id'    => array('column' => 'thread_id', 'unique' => 0),
            'del_flg'      => array('column' => 'del_flg', 'unique' => 0),
            'created'      => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array();

}
