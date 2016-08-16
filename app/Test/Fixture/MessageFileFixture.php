<?php

/**
 * MessageFileFixture

 */
class MessageFileFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'topic_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'トピックID(belongsToでTopicモデルに関連)'
        ),
        'message_id'       => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'メッセージID(belongsToでMessageモデルに関連)'
        ),
        'attached_file_id' => array('type'     => 'biginteger',
                                    'null'     => false,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'key'      => 'index',
                                    'comment'  => 'ファイルID(belongsToでFileモデルに関連)'
        ),
        'team_id'          => array('type'     => 'biginteger',
                                    'null'     => false,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'key'      => 'index',
                                    'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'index_num'        => array('type'     => 'integer',
                                    'null'     => false,
                                    'default'  => '0',
                                    'unsigned' => true,
                                    'comment'  => '表示順'
        ),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'          => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'comment'  => '削除した日付時刻'
        ),
        'created'          => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'comment'  => '追加した日付時刻'
        ),
        'modified'         => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'comment'  => '更新した日付時刻'
        ),
        'indexes'          => array(
            'PRIMARY'          => array('column' => 'id', 'unique' => 1),
            'topic_id'         => array('column' => 'topic_id', 'unique' => 0),
            'message_id'       => array('column' => 'message_id', 'unique' => 0),
            'team_id'          => array('column' => 'team_id', 'unique' => 0),
            'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0)
        ),
        'tableParameters'  => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'               => '',
            'topic_id'         => '',
            'message_id'       => '',
            'attached_file_id' => '',
            'team_id'          => '',
            'index_num'        => 1,
            'del_flg'          => 1,
            'deleted'          => 1,
            'created'          => 1,
            'modified'         => 1
        ),
    );

}
