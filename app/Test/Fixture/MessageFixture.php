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
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'メッセージID', 'charset' => 'utf8'),
        'from_user_id'    => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'to_user_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'thread_id'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'スレッドID(belongsToでThreadモデルに関連)', 'charset' => 'utf8'),
        'body'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メッセージ本文', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メッセージを削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メッセージを追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メッセージを更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
            'id'           => '537ce223-8d2c-48c7-ab02-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-b11c-48d5-8928-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-cb48-47a5-af05-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-e4ac-4461-bff1-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-fdac-4938-aa04-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-1710-4a07-8da2-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-3010-47ce-9e28-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-4910-4544-b43a-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-6274-46ab-8e1c-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-7b74-4fdd-a980-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'thread_id'    => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
    );

}
