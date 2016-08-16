<?php

/**
 * TopicFixture

 */
class TopicFixture extends CakeTestFixture
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
            'comment'  => 'ID'
        ),
        'user_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '投稿作成ユーザID(belongsToでUserモデルに関連)'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'title'           => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'length'  => 254,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'topic title',
            'charset' => 'utf8mb4'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '投稿を削除した日付時刻'
        ),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '投稿を追加した日付時刻'
        ),
        'modified'        => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '投稿を更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY'  => array('column' => 'id', 'unique' => 1),
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
            'user_id'  => '',
            'team_id'  => '',
            'title'    => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => 1,
            'created'  => 1,
            'modified' => 1
        ),
    );

}
