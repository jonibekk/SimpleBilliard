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
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'メッセージID'),
        'from_user_id'    => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)'),
        'to_user_id'      => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
        'thread_id'       => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドID(belongsToでThreadモデルに関連)'),
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
    public $records = array(
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 1,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 2,
            'created'      => 2,
            'modified'     => 2
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 3,
            'created'      => 3,
            'modified'     => 3
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 4,
            'created'      => 4,
            'modified'     => 4
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 5,
            'created'      => 5,
            'modified'     => 5
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 6,
            'created'      => 6,
            'modified'     => 6
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 7,
            'created'      => 7,
            'modified'     => 7
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 8,
            'created'      => 8,
            'modified'     => 8
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 9,
            'created'      => 9,
            'modified'     => 9
        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'thread_id'    => '',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 10,
            'created'      => 10,
            'modified'     => 10
        ),
    );

}
