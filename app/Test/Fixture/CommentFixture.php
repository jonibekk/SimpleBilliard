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
        'id'                 => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメントID', 'charset' => 'utf8'),
        'post_id'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
        'user_id'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コメントしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'body'               => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント本文', 'charset' => 'utf8'),
        'comment_like_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメントいいね数(comment_likesテーブルにレコードが追加されたらカウントアップされる)'),
        'comment_read_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメント読んだ数(comment_readsテーブルにレコードが追加されたらカウントアップされる)'),
        'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
        'created'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
        'modified'           => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
        'indexes'            => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters'    => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                 => '537ce223-97c8-4539-90d0-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 1,
            'comment_read_count' => 1,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-bbb8-47b7-9405-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 2,
            'comment_read_count' => 2,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-d648-4747-b1c6-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 3,
            'comment_read_count' => 3,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-f074-4955-a1e0-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 4,
            'comment_read_count' => 4,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-0aa0-4400-87c3-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 5,
            'comment_read_count' => 5,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-24cc-4bf2-9804-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 6,
            'comment_read_count' => 6,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-3e94-4b0d-b3e2-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 7,
            'comment_read_count' => 7,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-585c-4e7f-8b60-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 8,
            'comment_read_count' => 8,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-7288-44cf-bd20-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 9,
            'comment_read_count' => 9,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
        array(
            'id'                 => '537ce223-82f0-494a-9640-433dac11b50b',
            'post_id'            => 'Lorem ipsum dolor sit amet',
            'user_id'            => 'Lorem ipsum dolor sit amet',
            'team_id'            => 'Lorem ipsum dolor sit amet',
            'body'               => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'comment_like_count' => 10,
            'comment_read_count' => 10,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:03',
            'created'            => '2014-05-22 02:28:03',
            'modified'           => '2014-05-22 02:28:03'
        ),
    );

}
