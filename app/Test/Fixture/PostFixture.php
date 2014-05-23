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
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿ID', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿作成ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'body'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿本文', 'charset' => 'utf8'),
        'type'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
        'comment_count'   => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
        'post_like_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'いいね数(post_likesテーブルni'),
        'post_read_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '読んだ数'),
        'public_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1'),
        'important_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'goal_id'         => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
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
            'id'              => '537ce224-5900-4bf0-b394-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 1,
            'comment_count'   => 1,
            'post_like_count' => 1,
            'post_read_count' => 1,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-8074-4d05-a75c-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 2,
            'comment_count'   => 2,
            'post_like_count' => 2,
            'post_read_count' => 2,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-9d5c-4e8e-b136-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 3,
            'comment_count'   => 3,
            'post_like_count' => 3,
            'post_read_count' => 3,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-b97c-445d-aef2-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 4,
            'comment_count'   => 4,
            'post_like_count' => 4,
            'post_read_count' => 4,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-d664-45ea-afbb-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 5,
            'comment_count'   => 5,
            'post_like_count' => 5,
            'post_read_count' => 5,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-f34c-4101-be5a-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 6,
            'comment_count'   => 6,
            'post_like_count' => 6,
            'post_read_count' => 6,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-1034-4395-8490-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 7,
            'comment_count'   => 7,
            'post_like_count' => 7,
            'post_read_count' => 7,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-2d1c-4808-b24d-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 8,
            'comment_count'   => 8,
            'post_like_count' => 8,
            'post_read_count' => 8,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-4a04-4070-9f1c-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 9,
            'comment_count'   => 9,
            'post_like_count' => 9,
            'post_read_count' => 9,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
        array(
            'id'              => '537ce224-66ec-4434-892e-433dac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'body'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type'            => 10,
            'comment_count'   => 10,
            'post_like_count' => 10,
            'post_read_count' => 10,
            'public_flg'      => 1,
            'important_flg'   => 1,
            'goal_id'         => 'Lorem ipsum dolor sit amet',
            'del_flg'         => 1,
            'deleted'         => '2014-05-22 02:28:04',
            'created'         => '2014-05-22 02:28:04',
            'modified'        => '2014-05-22 02:28:04'
        ),
    );

}
