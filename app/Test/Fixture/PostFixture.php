<?php

/**
 * PostFixture

 */
class PostFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                   => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿ID'),
        'user_id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿作成ユーザID(belongsToでUserモデルに関連)'),
        'team_id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'body'                 => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿本文', 'charset' => 'utf8'),
        'type'                 => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
        'comment_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
        'post_like_count'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'いいね数(post_likesテーブルni'),
        'post_read_count'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '読んだ数'),
        'public_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
        'important_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
        'goal_id'              => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index'),
        'circle_id'            => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'サークルID'),
        'action_result_id'     => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'アクションID'),
        'key_result_id'        => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'KR ID'),
        'photo1_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像1', 'charset' => 'utf8'),
        'photo2_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像2', 'charset' => 'utf8'),
        'photo3_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像3', 'charset' => 'utf8'),
        'photo4_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像4', 'charset' => 'utf8'),
        'photo5_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像5', 'charset' => 'utf8'),
        'site_info'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト情報', 'charset' => 'utf8'),
        'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8'),
        'del_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
        'created'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
        'modified'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
        'indexes'              => array(
            'PRIMARY'          => array('column' => 'id', 'unique' => 1),
            'user_id'          => array('column' => 'user_id', 'unique' => 0),
            'team_id'          => array('column' => 'team_id', 'unique' => 0),
            'goal_id'          => array('column' => 'goal_id', 'unique' => 0),
            'modified'         => array('column' => 'modified', 'unique' => 0),
            'del_flg'          => array('column' => 'del_flg', 'unique' => 0),
            'type'             => array('column' => 'type', 'unique' => 0),
            'public_flg'       => array('column' => 'public_flg', 'unique' => 0),
            'important_flg'    => array('column' => 'important_flg', 'unique' => 0),
            'action_result_id' => array('column' => 'action_result_id', 'unique' => 0),
            'key_result_id'    => array('column' => 'key_result_id', 'unique' => 0)
        ),
        'tableParameters'      => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
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
            'public_flg'      => 0,
            'important_flg'   => 0,
            'goal_id'         => null,
            'del_flg'         => 0,
            'deleted'         => null,
            'created'         => 1,
            'modified'        => 1,
        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 2,
            'comment_count'   => 2,
            'post_like_count' => 2,
            'post_read_count' => 2,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 2,
            'created'         => 2,
            'modified'        => 2,

        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 3,
            'comment_count'   => 3,
            'post_like_count' => 3,
            'post_read_count' => 3,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 3,
            'created'         => 3,
            'modified'        => 3,

        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 4,
            'comment_count'   => 4,
            'post_like_count' => 4,
            'post_read_count' => 4,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 4,
            'created'         => 4,
            'modified'        => 4,

        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 5,
            'comment_count'   => 5,
            'post_like_count' => 5,
            'post_read_count' => 5,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 5,
            'created'         => 5,
            'modified'        => 5,

        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 6,
            'comment_count'   => 6,
            'post_like_count' => 6,
            'post_read_count' => 6,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 6,
            'created'         => 6,
            'modified'        => 6,

        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 7,
            'comment_count'   => 7,
            'post_like_count' => 7,
            'post_read_count' => 7,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 7,
            'created'         => 7,
            'modified'        => 7,

        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 8,
            'comment_count'   => 8,
            'post_like_count' => 8,
            'post_read_count' => 8,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 8,
            'created'         => 8,
            'modified'        => 8,

        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 9,
            'comment_count'   => 9,
            'post_like_count' => 9,
            'post_read_count' => 9,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 9,
            'created'         => 9,
            'modified'        => 9,

        ),
        array(
            'id'              => '',
            'user_id'         => '',
            'team_id'         => '',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 10,
            'comment_count'   => 10,
            'post_like_count' => 10,
            'post_read_count' => 10,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => '',
            'del_flg'         => 1,
            'deleted'         => 10,
            'created'         => 10,
            'modified'        => 10,

        ),
    );

}
