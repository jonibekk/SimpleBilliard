<?php

/**
 * CommentFixture

 */
class CommentFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                   => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'コメントID'),
        'post_id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
        'user_id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コメントしたユーザID(belongsToでUserモデルに関連)'),
        'team_id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'body'                 => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント本文', 'charset' => 'utf8'),
        'comment_like_count'   => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメントいいね数(comment_likesテーブルにレコードが追加されたらカウントアップされる)'),
        'comment_read_count'   => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメント読んだ数(comment_readsテーブルにレコードが追加されたらカウントアップされる)'),
        'photo1_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像1', 'charset' => 'utf8'),
        'photo2_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像2', 'charset' => 'utf8'),
        'photo3_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像3', 'charset' => 'utf8'),
        'photo4_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像4', 'charset' => 'utf8'),
        'photo5_file_name'     => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像5', 'charset' => 'utf8'),
        'site_info'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト情報', 'charset' => 'utf8'),
        'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8'),
        'del_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
        'created'              => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
        'modified'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
        'indexes'              => array(
            'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
            'post_id' => array('column' => 'post_id', 'unique' => 0),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'del_flg' => array('column' => 'del_flg', 'unique' => 0),
            'created' => array('column' => 'created', 'unique' => 0)
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
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 1,
            'comment_read_count' => 1,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 2,
            'comment_read_count' => 2,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 3,
            'comment_read_count' => 3,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 4,
            'comment_read_count' => 4,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 5,
            'comment_read_count' => 5,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 6,
            'comment_read_count' => 6,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 7,
            'comment_read_count' => 7,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 8,
            'comment_read_count' => 8,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 9,
            'comment_read_count' => 9,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'user_id'  => '',
            'team_id'  => '',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 10,
            'comment_read_count' => 10,
            'del_flg'            => 1,
            'deleted'  => 1400725683,
            'created'  => 1400725683,
            'modified' => 1400725683
        ),
    );

}
