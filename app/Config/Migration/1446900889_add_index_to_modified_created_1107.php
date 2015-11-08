<?php

class AddIndexToModifiedCreated1107 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_index_to_modified_created_1107';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'action_results'     => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                ),
                'circles'            => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '部署を追加した日付時刻'),
                ),
                'collaborators'      => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                ),
                'comment_likes'      => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コメントを追加した日付時刻'),
                ),
                'comments'           => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
                ),
                'post_likes'         => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
                ),
                'post_share_circles' => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
                ),
                'post_share_users'   => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
                ),
                'purposes'           => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                ),
            ),
            'create_field' => array(
                'action_results'     => array(
                    'indexes' => array(
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                ),
                'circles'            => array(
                    'indexes' => array(
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                ),
                'collaborators'      => array(
                    'indexes' => array(
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                ),
                'comment_likes'      => array(
                    'indexes' => array(
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                ),
                'comments'           => array(
                    'indexes' => array(
                        'modified' => array('column' => 'modified', 'unique' => 0),
                    ),
                ),
                'post_likes'         => array(
                    'indexes' => array(
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                ),
                'post_share_circles' => array(
                    'indexes' => array(
                        'modified' => array('column' => 'modified', 'unique' => 0),
                    ),
                ),
                'post_share_users'   => array(
                    'indexes' => array(
                        'modified' => array('column' => 'modified', 'unique' => 0),
                    ),
                ),
                'purposes'           => array(
                    'indexes' => array(
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'action_results'     => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                ),
                'circles'            => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
                ),
                'collaborators'      => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                ),
                'comment_likes'      => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを追加した日付時刻'),
                ),
                'comments'           => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
                ),
                'post_likes'         => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
                ),
                'post_share_circles' => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
                ),
                'post_share_users'   => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
                ),
                'purposes'           => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                ),
            ),
            'drop_field'  => array(
                'action_results'     => array('indexes' => array('created')),
                'circles'            => array('indexes' => array('created')),
                'collaborators'      => array('indexes' => array('created')),
                'comment_likes'      => array('indexes' => array('created')),
                'comments'           => array('indexes' => array('modified')),
                'post_likes'         => array('indexes' => array('created')),
                'post_share_circles' => array('indexes' => array('modified')),
                'post_share_users'   => array('indexes' => array('modified')),
                'purposes'           => array('indexes' => array('created')),
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
