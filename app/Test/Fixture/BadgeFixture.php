<?php

/**
 * BadgeFixture

 */
class BadgeFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'バッジID'),
        'user_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'バッジ作成ユーザID(belongsToでUserモデルに関連)'),
        'team_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'name'             => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ名', 'charset' => 'utf8'),
        'description'      => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ詳細', 'charset' => 'utf8'),
        'photo_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像', 'charset' => 'utf8'),
        'default_badge_no' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'デフォルトバッジNo(デフォルトで用意されているバッジ)'),
        'type'             => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
        'active_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
        'count'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '利用されたカウント数(バッジが利用されるとカウントアップ。チーム管理者がリセット可能)'),
        'max_count'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '利用可能数(カウント数が利用可能数に達した場合、バッジを新たに付与する事ができなくなる。)'),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを削除した日付時刻'),
        'created'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを追加した日付時刻'),
        'modified'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを更新した日付時刻'),
        'indexes'          => array(
            'PRIMARY'    => array('column' => 'id', 'unique' => 1),
            'user_id'    => array('column' => 'user_id', 'unique' => 0),
            'team_id'    => array('column' => 'team_id', 'unique' => 0),
            'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
            'active_flg' => array('column' => 'active_flg', 'unique' => 0),
            'type'       => array('column' => 'type', 'unique' => 0)
        ),
        'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 1,
            'type'             => 1,
            'active_flg'       => 1,
            'count'            => 1,
            'max_count'        => 1,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 2,
            'type'             => 2,
            'active_flg'       => 1,
            'count'            => 2,
            'max_count'        => 2,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 3,
            'type'             => 3,
            'active_flg'       => 1,
            'count'            => 3,
            'max_count'        => 3,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 4,
            'type'             => 4,
            'active_flg'       => 1,
            'count'            => 4,
            'max_count'        => 4,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 5,
            'type'             => 5,
            'active_flg'       => 1,
            'count'            => 5,
            'max_count'        => 5,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 6,
            'type'             => 6,
            'active_flg'       => 1,
            'count'            => 6,
            'max_count'        => 6,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 7,
            'type'             => 7,
            'active_flg'       => 1,
            'count'            => 7,
            'max_count'        => 7,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 8,
            'type'             => 8,
            'active_flg'       => 1,
            'count'            => 8,
            'max_count'        => 8,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 9,
            'type'             => 9,
            'active_flg'       => 1,
            'count'            => 9,
            'max_count'        => 9,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '',
            'user_id'          => '',
            'team_id'          => '',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'  => 'Lorem ipsum dolor sit amet',
            'default_badge_no' => 10,
            'type'             => 10,
            'active_flg'       => 1,
            'count'            => 10,
            'max_count'        => 10,
            'del_flg'          => 1,
            'deleted'          => '2014-05-22 02:28:02',
            'created'          => '2014-05-22 02:28:02',
            'modified'         => '2014-05-22 02:28:02'
        ),
    );

}
