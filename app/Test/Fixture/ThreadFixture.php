<?php

/**
 * ThreadFixture

 */
class ThreadFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'スレッドID', 'charset' => 'utf8'),
        'from_user_id'    => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'to_user_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
        'status'          => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'スレッドステータス(1:Open,2:Close)'),
        'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'スレッド名', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'スレッドの詳細', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'スレッドを削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'スレッドを追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'スレッドを更新した日付時刻'),
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
            'id'           => '537ce224-e108-4126-948a-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 1,
            'status'       => 1,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-0fe8-4aa0-8836-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 2,
            'status'       => 2,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-2f8c-427c-a7d3-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 3,
            'status'       => 3,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-4e04-49d4-baa4-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 4,
            'status'       => 4,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-6b50-44f0-b2d9-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 5,
            'status'       => 5,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-889c-4d8f-b678-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 6,
            'status'       => 6,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-a6b0-411b-a14d-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 7,
            'status'       => 7,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-cb04-4a5f-82c7-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 8,
            'status'       => 8,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-1be0-4820-9d3f-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 9,
            'status'       => 9,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
        array(
            'id'           => '537ce224-4160-47e3-b7ae-433dac11b50b',
            'from_user_id' => 'Lorem ipsum dolor sit amet',
            'to_user_id'   => 'Lorem ipsum dolor sit amet',
            'team_id'      => 'Lorem ipsum dolor sit amet',
            'type'         => 10,
            'status'       => 10,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => '2014-05-22 02:28:04',
            'created'      => '2014-05-22 02:28:04',
            'modified'     => '2014-05-22 02:28:04'
        ),
    );

}
