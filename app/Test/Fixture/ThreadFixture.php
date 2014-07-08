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
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'スレッドID'),
        'from_user_id'    => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)'),
        'to_user_id'      => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
        'status'          => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドステータス(1:Open,2:Close)'),
        'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'スレッド名', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'スレッドの詳細', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'スレッドを削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドを追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドを更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'      => array('column' => 'id', 'unique' => 1),
            'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
            'to_user_id'   => array('column' => 'to_user_id', 'unique' => 0),
            'del_flg'      => array('column' => 'del_flg', 'unique' => 0),
            'type'         => array('column' => 'type', 'unique' => 0),
            'status'       => array('column' => 'status', 'unique' => 0),
            'created'      => array('column' => 'created', 'unique' => 0),
            'modified'     => array('column' => 'modified', 'unique' => 0)
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
            'team_id'      => '',
            'type'         => 1,
            'status'       => 1,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 1,
            'created'      => 1,
            'modified'     => 1,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 2,
            'status'       => 2,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 2,
            'created'      => 2,
            'modified'     => 2,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 3,
            'status'       => 3,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 3,
            'created'      => 3,
            'modified'     => 3,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 4,
            'status'       => 4,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 4,
            'created'      => 4,
            'modified'     => 4,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 5,
            'status'       => 5,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 5,
            'created'      => 5,
            'modified'     => 5,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 6,
            'status'       => 6,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 6,
            'created'      => 6,
            'modified'     => 6,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 7,
            'status'       => 7,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 7,
            'created'      => 7,
            'modified'     => 7,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 8,
            'status'       => 8,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 8,
            'created'      => 8,
            'modified'     => 8,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 9,
            'status'       => 9,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 9,
            'created'      => 9,
            'modified'     => 9,

        ),
        array(
            'id'           => '',
            'from_user_id' => '',
            'to_user_id'   => '',
            'team_id'      => '',
            'type'         => 10,
            'status'       => 10,
            'name'         => 'Lorem ipsum dolor sit amet',
            'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'del_flg'      => 1,
            'deleted'      => 10,
            'created'      => 10,
            'modified'     => 10,

        ),
    );

}
