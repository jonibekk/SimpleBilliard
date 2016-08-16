<?php

/**
 * TopicMemberFixture

 */
class TopicMemberFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'トピック共有ユーザID'
        ),
        'topic_id'        => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'トピックID(belongsToでPostモデルに関連)'
        ),
        'user_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '共有ユーザID(belongsToでUserモデルに関連)'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'トピックを削除した日付時刻'
        ),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'トピックを追加した日付時刻'
        ),
        'modified'        => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'トピックを更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY'  => array('column' => 'id', 'unique' => 1),
            'topic_id' => array('column' => 'topic_id', 'unique' => 0),
            'user_id'  => array('column' => 'user_id', 'unique' => 0),
            'team_id'  => array('column' => 'team_id', 'unique' => 0),
            'modified' => array('column' => 'modified', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'       => '',
            'topic_id' => '',
            'user_id'  => '',
            'team_id'  => '',
            'del_flg'  => 1,
            'deleted'  => 1,
            'created'  => 1,
            'modified' => 1
        ),
    );

}
