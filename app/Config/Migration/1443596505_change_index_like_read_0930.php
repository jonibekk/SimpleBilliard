<?php

class ChangeIndexLikeRead0930 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_index_like_read_0930';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'drop_field'   => array(
                'comment_likes' => array('indexes' => array('comment_id', 'user_id')),
                'comment_reads' => array('indexes' => array('comment_id', 'user_id')),
                'post_likes'    => array('indexes' => array('post_id', 'user_id')),
                'post_reads'    => array('indexes' => array('post_id', 'user_id')),
            ),
            'create_field' => array(
                'comment_likes' => array(
                    'indexes' => array(
                        'comment_user_unique' => array('column' => array('comment_id', 'user_id'), 'unique' => 1),
                    ),
                ),
                'comment_reads' => array(
                    'indexes' => array(
                        'comment_user_unique' => array('column' => array('comment_id', 'user_id'), 'unique' => 1),
                    ),
                ),
                'post_likes'    => array(
                    'indexes' => array(
                        'post_user_unique' => array('column' => array('post_id', 'user_id'), 'unique' => 1),
                    ),
                ),
                'post_reads'    => array(
                    'indexes' => array(
                        'post_user_unique' => array('column' => array('post_id', 'user_id'), 'unique' => 1),
                    ),
                ),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'comment_likes' => array(
                    'indexes' => array(
                        'comment_id' => array('column' => 'comment_id', 'unique' => 0),
                        'user_id'    => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
                'comment_reads' => array(
                    'indexes' => array(
                        'comment_id' => array('column' => 'comment_id', 'unique' => 0),
                        'user_id'    => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
                'post_likes'    => array(
                    'indexes' => array(
                        'post_id' => array('column' => 'post_id', 'unique' => 0),
                        'user_id' => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
                'post_reads'    => array(
                    'indexes' => array(
                        'post_id' => array('column' => 'post_id', 'unique' => 0),
                        'user_id' => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
            ),
            'drop_field'   => array(
                'comment_likes' => array('indexes' => array('comment_user_unique')),
                'comment_reads' => array('indexes' => array('comment_user_unique')),
                'post_likes'    => array('indexes' => array('post_user_unique')),
                'post_reads'    => array('indexes' => array('post_user_unique')),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        if ($direction == 'down') {
            return true;
        }
        //データ削除
        /**
         * @var Post $Post
         */
        $Post = ClassRegistry::init('Post');
        //今後、これらのテーブルはハードデリートするのでdel_flgがonのレコードを削除
        $Post->query('DELETE FROM post_likes WHERE del_flg = 1');
        $Post->query('DELETE FROM post_reads WHERE del_flg = 1');
        $Post->query('DELETE FROM comment_likes WHERE del_flg = 1');
        $Post->query('DELETE FROM comment_reads WHERE del_flg = 1');
        //2カラムの複合uniqueキーに変更する為、事前にダブってるレコードを１件のみ残し他を削除
        $Post->query('DELETE FROM post_likes WHERE id NOT IN (SELECT min_id FROM (SELECT min(t1.id) AS min_id FROM post_likes AS t1 GROUP BY t1.post_id, t1.user_id) AS t2)');
        $Post->query('DELETE FROM post_reads WHERE id NOT IN (SELECT min_id FROM (SELECT min(t1.id) AS min_id FROM post_reads AS t1 GROUP BY t1.post_id, t1.user_id) AS t2)');
        $Post->query('DELETE FROM comment_likes WHERE id NOT IN (SELECT min_id FROM (SELECT min(t1.id) AS min_id FROM comment_likes AS t1 GROUP BY t1.comment_id, t1.user_id) AS t2)');
        $Post->query('DELETE FROM comment_reads WHERE id NOT IN (SELECT min_id FROM (SELECT min(t1.id) AS min_id FROM comment_reads AS t1 GROUP BY t1.comment_id, t1.user_id) AS t2)');
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
