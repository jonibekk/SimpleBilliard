<?php

/**
 * NotificationFixture

 */
class NotificationFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '通知ID', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'タイプ(1:ゴール,2:投稿,3:etc ...)'),
        'from_user_id'    => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '通知元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'body'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '通知本文', 'charset' => 'utf8'),
        'unread_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '未読フラグ(通知を開いたらOff)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '通知を削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '通知を追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '通知を更新した日付時刻'),
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
            'id'           => '537ce223-1d98-4034-9d51-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 1,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-4188-4199-b4f7-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 2,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-5c18-44ef-b629-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 3,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-7644-4ac2-bcff-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 4,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-900c-461e-b400-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 5,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-a970-4305-839b-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 6,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-c39c-4406-adc7-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 7,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-e214-4b01-a38f-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 8,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-0028-40a0-b421-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 9,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
        array(
            'id'           => '537ce223-198c-42a9-9349-433dac11b50b',
            'user_id'      => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 10,
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'body'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'unread_flg'   => 1,
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:03',
            'created'      => '2014-05-22 02:28:03',
            'modified'     => '2014-05-22 02:28:03'
        ),
    );

}
