<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * PostFixture
 */
class PostFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                   => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => '投稿ID'
        ),
        'user_id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '投稿作成ユーザID(belongsToでUserモデルに関連)'
        ),
        'team_id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'body'                 => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '投稿本文',
            'charset' => 'utf8mb4'
        ),
        'language'             => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'length'  => 10,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Detected language of the post body',
            'charset' => 'utf8mb4'
        ),
        'type'                 => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '1',
            'length'   => 3,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'
        ),
        'comment_count'        => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'
        ),
        'post_like_count'      => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => 'いいね数(post_likesテーブルni'
        ),
        'post_read_count'      => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '読んだ数'
        ),
        'important_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
        'goal_id'              => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ),
        'circle_id'            => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'サークルID'
        ),
        'action_result_id'     => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'アクションID'
        ),
        'key_result_id'        => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'KR ID'
        ),
        'photo1_file_name'     => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '投稿画像1',
            'charset' => 'utf8mb4'
        ),
        'photo2_file_name'     => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '投稿画像2',
            'charset' => 'utf8mb4'
        ),
        'photo3_file_name'     => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '投稿画像3',
            'charset' => 'utf8mb4'
        ),
        'photo4_file_name'     => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '投稿画像4',
            'charset' => 'utf8mb4'
        ),
        'photo5_file_name'     => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '投稿画像5',
            'charset' => 'utf8mb4'
        ),
        'site_info'            => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'サイト情報',
            'charset' => 'utf8mb4'
        ),
        'site_photo_file_name' => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'サイト画像',
            'charset' => 'utf8mb4'
        ),
        'del_flg'              => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'key'     => 'index',
            'comment' => '削除フラグ'
        ),
        'deleted'              => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '投稿を削除した日付時刻'
        ),
        'created'              => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '投稿を追加した日付時刻'
        ),
        'modified'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '投稿を更新した日付時刻'
        ),
        'indexes'              => array(
            'PRIMARY'          => array('column' => 'id', 'unique' => 1),
            'user_id'          => array('column' => 'user_id', 'unique' => 0),
            'team_id'          => array('column' => 'team_id', 'unique' => 0),
            'goal_id'          => array('column' => 'goal_id', 'unique' => 0),
            'modified'         => array('column' => 'modified', 'unique' => 0),
            'del_flg'          => array('column' => 'del_flg', 'unique' => 0),
            'type'             => array('column' => 'type', 'unique' => 0),
            'important_flg'    => array('column' => 'important_flg', 'unique' => 0),
            'action_result_id' => array('column' => 'action_result_id', 'unique' => 0),
            'key_result_id'    => array('column' => 'key_result_id', 'unique' => 0)
        ),
        'tableParameters'      => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'              => 1,
            'user_id'         => 2,
            'team_id'         => 1,
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 1,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 1,
            'modified'        => 1,
            'circle_id'       => 1
        ),
        array(
            'id'              => 2,
            'user_id'         => 101,
            'team_id'         => 1,
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 1,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 100,
            'modified'        => 200,
        ),
        array(
            'id'              => 3,
            'user_id'         => 101,
            'team_id'         => 1,
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 1,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 200,
            'modified'        => 200,
        ),
        array(
            'id'              => 4,
            'user_id'         => 102,
            'team_id'         => 1,
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 1,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 1,
            'modified'        => 1,
        ),
        array(
            'id'              => 5,
            'user_id'         => 103,
            'team_id'         => 1,
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 1,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 1388603000,
            'modified'        => 1388603000,
        ),
        array(
            'id'              => 6,
            'user_id'         => 104,
            'team_id'         => 1,
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 7,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 1388603001,
            'modified'        => 1388603001,
        ),
        array(
            'id'              => 7,
            'user_id'         => 1,
            'team_id'         => 1,
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 1,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 1,
            'modified'        => 1,
        ),
        array(
            'id'               => 8,
            'user_id'          => 1,
            'team_id'          => 1,
            'body'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'             => 3,
            'comment_count'    => 1,
            'post_like_count'  => 1,
            'post_read_count'  => 1,
            'important_flg'    => 0,
            'action_result_id' => 1,
            'goal_id'          => 1,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ),
        array(
            'id'               => 9,
            'user_id'          => 1,
            'team_id'          => 1,
            'body'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'             => 1,
            'comment_count'    => 1,
            'post_like_count'  => 1,
            'post_read_count'  => 1,
            'important_flg'    => 0,
            'action_result_id' => 1,
            'goal_id'          => null,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ),
        array(
            'id'               => 10,
            'user_id'          => 2,
            'team_id'          => 1,
            'body'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'             => 1,
            'comment_count'    => 1,
            'post_like_count'  => 1,
            'post_read_count'  => 1,
            'important_flg'    => 0,
            'action_result_id' => 1,
            'goal_id'          => null,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ),
        array(
            'id'               => 11,
            'user_id'          => 2,
            'team_id'          => 1,
            'body'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'             => 1,
            'comment_count'    => 1,
            'post_like_count'  => 1,
            'post_read_count'  => 1,
            'important_flg'    => 0,
            'action_result_id' => 1,
            'goal_id'          => null,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ),
        array(
            'id'               => 12,
            'user_id'          => 12,
            'team_id'          => 1,
            'body'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'             => 3,
            'comment_count'    => 11,
            'post_like_count'  => 12,
            'post_read_count'  => 13,
            'important_flg'    => 0,
            'action_result_id' => 1,
            'goal_id'          => 1,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ),
        array(
            'id'               => 13,
            'user_id'          => 11,
            'team_id'          => 3,
            'body'             => 'メッセージ',
            'type'             => 8,
            'comment_count'    => 0,
            'post_like_count'  => 0,
            'post_read_count'  => 0,
            'important_flg'    => 0,
            'action_result_id' => null,
            'goal_id'          => null,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ),
        array(
            'id'               => 14,
            'user_id'          => 1,
            'team_id'          => 1,
            'body'             => 'メッセージ',
            'type'             => 8,
            'comment_count'    => 0,
            'post_like_count'  => 0,
            'post_read_count'  => 0,
            'important_flg'    => 0,
            'action_result_id' => null,
            'goal_id'          => null,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ),
        array(
            'id'              => 15,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 2,
            'comment_count'   => 2,
            'post_like_count' => 2,
            'post_read_count' => 2,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 2,
            'created'       => 2,
            'modified'      => 2,

        ),
        array(
            'id'              => 16,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 3,
            'comment_count'   => 3,
            'post_like_count' => 3,
            'post_read_count' => 3,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 3,
            'created'       => 3,
            'modified'      => 3,

        ),
        array(
            'id'              => 17,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 4,
            'comment_count'   => 4,
            'post_like_count' => 4,
            'post_read_count' => 4,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 4,
            'created'       => 4,
            'modified'      => 4,

        ),
        array(
            'id'              => 18,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 5,
            'comment_count'   => 5,
            'post_like_count' => 5,
            'post_read_count' => 5,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 5,
            'created'       => 5,
            'modified'      => 5,

        ),
        array(
            'id'              => 19,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 6,
            'comment_count'   => 6,
            'post_like_count' => 6,
            'post_read_count' => 6,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 6,
            'created'       => 6,
            'modified'      => 6,

        ),
        array(
            'id'              => 20,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 7,
            'comment_count'   => 7,
            'post_like_count' => 7,
            'post_read_count' => 7,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 7,
            'created'       => 7,
            'modified'      => 7,

        ),
        array(
            'id'              => 21,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 8,
            'comment_count'   => 8,
            'post_like_count' => 8,
            'post_read_count' => 8,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 8,
            'created'       => 8,
            'modified'      => 8,

        ),
        array(
            'id'              => 22,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 9,
            'comment_count'   => 9,
            'post_like_count' => 9,
            'post_read_count' => 9,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 9,
            'created'       => 9,
            'modified'      => 9,

        ),
        array(
            'id'              => 23,
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 10,
            'comment_count'   => 10,
            'post_like_count' => 10,
            'post_read_count' => 10,

            'important_flg' => 1,
            'goal_id'       => '',
            'del_flg'       => 1,
            'deleted'       => 10,
            'created'       => 10,
            'modified'      => 10,

        ),
        array(
            'id'              => 24,
            'user_id'         => 103,
            'team_id'         => 1,
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 1,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 1388603001,
            'modified'        => 1388603001,
        ),
    );

}
