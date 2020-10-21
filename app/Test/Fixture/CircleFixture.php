<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * CircleFixture
 */
class CircleFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                  => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'サークルID'
        ),
        'team_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'name'                => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 128,
            'key'     => 'index',
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'サークル名',
            'charset' => 'utf8mb4'
        ),
        'description'         => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'サークルの説明',
            'charset' => 'utf8mb4'
        ),
        'photo_file_name'     => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'サークルロゴ画像',
            'charset' => 'utf8mb4'
        ),
        'public_flg'          => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => '公開フラグ(公開の場合はチームメンバー全員にサークルの存在が閲覧可能)'
        ),
        'team_all_flg'        => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => 'チーム全体フラグ(各チームに必須で１つ存在する)'
        ),
        'circle_member_count' => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => 'メンバー数'
        ),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を削除した日付時刻'
        ),
        'created'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を追加した日付時刻'
        ),
        'modified'            => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を更新した日付時刻'
        ),
        'latest_post_created' => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ),
        'indexes'             => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'name'    => array('column' => 'name', 'unique' => 0)
        ),
        'tableParameters'     => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
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
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1,
            'modified'            => 1,
            'latest_post_created' => 100
        ),
        array(
            'id'                  => 2,
            'team_id'             => 1,
            'name'                => 'firstname',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => null,
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1000,
            'modified'            => 1000,
            'latest_post_created' => 101
        ),
        array(
            'id'                  => 3,
            'team_id'             => 1,
            'name'                => 'チーム全体',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => null,
            'public_flg'          => 1,
            'team_all_flg'        => 1,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1,
            'modified'            => 1,
            'latest_post_created' => 102
        ),
        array(
            'id'                  => 4,
            'team_id'             => 1,
            'name'                => '秘密サークル',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => null,
            'public_flg'          => 0,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1,
            'modified'            => 1,
            'latest_post_created' => 103
        ),
        array(
            'id'                  => 5,
            'team_id'             => 1,
            'name'                => '公開サークル１',
            'description'         => 'user_id == 1 のユーザーが所属していない',
            'photo_file_name'     => null,
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1,
            'modified'            => 1,
            'latest_post_created' => 104
        ),
        array(
            'id'                  => 6,
            'team_id'             => 1,
            'name'                => '秘密サークル２',
            'description'         => 'user_id == 1 のユーザーが所属していない',
            'photo_file_name'     => null,
            'public_flg'          => 0,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1,
            'modified'            => 1,
            'latest_post_created' => 105
        ),
        array(
            'id'                  => '7',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 3,
            'created'             => 3,
            'modified'            => 3,
            'latest_post_created' => 106
        ),
        array(
            'id'                  => '8',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 4,
            'created'             => 4,
            'modified'            => 4,
            'latest_post_created' => 107
        ),
        array(
            'id'                  => '9',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 5,
            'created'             => 5,
            'modified'            => 5,
            'latest_post_created' => 108
        ),
        array(
            'id'                  => '10',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 6,
            'created'             => 6,
            'modified'            => 6,
            'latest_post_created' => 109
        ),
        array(
            'id'                  => '11',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 7,
            'created'             => 7,
            'modified'            => 7,
            'latest_post_created' => 110
        ),
        array(
            'id'                  => '12',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 8,
            'created'             => 8,
            'modified'            => 8,
            'latest_post_created' => 111
        ),
        array(
            'id'                  => '13',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 9,
            'created'             => 9,
            'modified'            => 9,
            'latest_post_created' => 112
        ),
        array(
            'id'                  => '14',
            'team_id'             => '',
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 1,
            'deleted'             => 10,
            'created'             => 10,
            'modified'            => 10,
            'latest_post_created' => 113
        ),
        array(
            'id'                  => 15,
            'team_id'             => 1,
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => 10,
            'created'             => 10,
            'modified'            => 10,
            'latest_post_created' => 113
        ),
        array(
            'id'                  => 16,
            'team_id'             => 1,
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => 0,
            'created'             => 10,
            'modified'            => 10,
            'latest_post_created' => 114
        ),
        array(
            'id'                  => 17,
            'team_id'             => 1,
            'name'                => 'Lorem ipsum dolor sit amet',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 0,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => 0,
            'created'             => 10,
            'modified'            => 10,
            'latest_post_created' => 113
        ),
        array(
            'id'                  => 18,
            'team_id'             => 2,
            'name'                => 'Team 2 default circle',
            'description'         => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'photo_file_name'     => 'Lorem ipsum dolor sit amet',
            'public_flg'          => 1,
            'team_all_flg'        => 1,
            'circle_member_count' => 0,
            'del_flg'             => 0,
            'deleted'             => 0,
            'created'             => 10,
            'modified'            => 10,
            'latest_post_created' => 113
        ),
    );
}
