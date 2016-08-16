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
        'id'                 => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'topic_id'           => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'topic ID(belongsToでTopicモデルに関連)'
        ),
        'user_id'            => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'メッセしたユーザID(belongsToでUserモデルに関連)'
        ),
        'team_id'            => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'body'               => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'メッセ本文',
            'charset' => 'utf8mb4'
        ),
        'type'               => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '1',
            'length'   => 3,
            'unsigned' => true,
            'comment'  => 'メッセタイプ(1:Nomal,2:メンバー追加,3:メンバー削除,4:メンバー離脱)'
        ),
        'message_read_count' => array('type'     => 'integer',
                                      'null'     => false,
                                      'default'  => '0',
                                      'unsigned' => false,
                                      'comment'  => 'メッセージ読んだ数(message_readsテーブルにレコードが追加されたらカウントアップされる)'
        ),
        'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'            => array('type'     => 'integer',
                                      'null'     => true,
                                      'default'  => null,
                                      'unsigned' => true,
                                      'comment'  => '削除した日付時刻'
        ),
        'created'            => array('type'     => 'integer',
                                      'null'     => false,
                                      'default'  => '0',
                                      'unsigned' => true,
                                      'key'      => 'primary',
                                      'comment'  => '追加した日付時刻'
        ),
        'modified'           => array('type'     => 'integer',
                                      'null'     => true,
                                      'default'  => null,
                                      'unsigned' => true,
                                      'comment'  => '更新した日付時刻'
        ),
        'indexes'            => array(
            'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'created' => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters'    => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                 => '',
            'topic_id'           => '',
            'user_id'            => '',
            'team_id'            => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'               => 1,
            'message_read_count' => 1,
            'del_flg'            => 1,
            'deleted'            => 1,
            'created'            => 1,
            'modified'           => 1
        ),
    );

}
