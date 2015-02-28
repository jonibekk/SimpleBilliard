<?php

/**
 * CircleFixture

 */
class CircleFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'サークルID'),
        'team_id'             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'name'                => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'サークル名', 'charset' => 'utf8'),
        'description'         => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サークルの説明', 'charset' => 'utf8'),
        'photo_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サークルロゴ画像', 'charset' => 'utf8'),
        'public_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '公開フラグ(公開の場合はチームメンバー全員にサークルの存在が閲覧可能)'),
        'circle_member_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'メンバー数'),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
        'created'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
        'modified'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
        'indexes'             => array(
            'PRIMARY'    => array('column' => 'id', 'unique' => 1),
            'team_id'    => array('column' => 'team_id', 'unique' => 0),
            'name'       => array('column' => 'name', 'unique' => 0),
            'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
            'public_flg' => array('column' => 'public_flg', 'unique' => 0)
        ),
        'tableParameters'     => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                  => 1,
            'team_id'             => 1,
            'name'                => 'test',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => null,
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1,
            'modified'            => 1
        ),
        array(
            'id'                  => 2,
            'team_id'             => 1,
            'name'                => 'firstname',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => null,
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1,
            'modified'            => 1
        ),
        array(
            'id'                  => '',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 3,
            'created'             => 3,
            'modified'            => 3
        ),
        array(
            'id'                  => '',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 4,
            'created'             => 4,
            'modified'            => 4
        ),
        array(
            'id'                  => '',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 5,
            'created'             => 5,
            'modified'            => 5
        ),
        array(
            'id'                  => '',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 6,
            'created'             => 6,
            'modified'            => 6
        ),
        array(
            'id'                  => '',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 7,
            'created'             => 7,
            'modified'            => 7
        ),
        array(
            'id'                  => '',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 8,
            'created'             => 8,
            'modified'            => 8
        ),
        array(
            'id'                  => '',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 9,
            'created'             => 9,
            'modified'            => 9
        ),
        array(
            'id'                  => '',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 10,
            'created'             => 10,
            'modified'            => 10
        ),
    );
}
