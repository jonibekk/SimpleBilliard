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
        'id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'バッジID', 'charset' => 'utf8'),
        'user_id'          => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ作成ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'          => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'name'             => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ名', 'charset' => 'utf8'),
        'description'      => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ詳細', 'charset' => 'utf8'),
        'image_id'         => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
        'default_badge_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'デフォルトバッジID(デフォルトで用意されているバッジ)'),
        'type'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
        'active_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
        'count'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '利用されたカウント数(バッジが利用されるとカウントアップ。チーム管理者がリセット可能)'),
        'max_count'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '利用可能数(カウント数が利用可能数に達した場合、バッジを新たに付与する事ができなくなる。)'),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'          => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを削除した日付時刻'),
        'created'          => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを追加した日付時刻'),
        'modified'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを更新した日付時刻'),
        'indexes'          => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
            'id'       => '537ce222-f5bc-4eeb-a798-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 1,
            'type'             => 1,
            'active_flg'       => 1,
            'count'            => 1,
            'max_count'        => 1,
            'del_flg'          => 1,
            'deleted'  => '2014-05-22 02:28:02',
            'created'  => '2014-05-22 02:28:02',
            'modified' => '2014-05-22 02:28:02'
        ),
        array(
            'id'               => '537ce222-20b4-4f70-9499-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 2,
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
            'id'               => '537ce222-3cd4-48bf-8b08-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 3,
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
            'id'               => '537ce222-5bb0-4977-8eeb-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 4,
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
            'id'               => '537ce222-78fc-4a33-83fd-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 5,
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
            'id'               => '537ce222-9454-47cc-b28d-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 6,
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
            'id'               => '537ce222-b13c-41cf-9999-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 7,
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
            'id'               => '537ce222-ce24-4401-87b7-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 8,
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
            'id'               => '537ce222-f78c-4262-8d17-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 9,
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
            'id'               => '537ce222-1604-4d7e-abeb-433dac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 10,
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
