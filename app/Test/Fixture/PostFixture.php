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
        'type'            => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
        'comment_count'   => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
        'post_like_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'いいね数(post_likesテーブルni'),
        'post_read_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '読んだ数'),
        'public_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1'),
        'important_flg'   => array('type' => 'boolean', 'null' => false, 'default' => null),
        'goal_id'         => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
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
            'id'              => '53746f17-2030-492f-a2dc-0d9cac11b50b',
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
            'deleted'         => '2014-05-15 16:39:03',
            'created'         => '2014-05-15 16:39:03',
            'modified'        => '2014-05-15 16:39:03'
        ),
    );

}
