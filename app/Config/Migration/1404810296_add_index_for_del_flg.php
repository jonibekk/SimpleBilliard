<?php

class AddIndexForDelFlg extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'badges'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'comment_likes'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'comment_mentions' => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'comment_reads'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'comments'         => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'emails'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'given_badges'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'groups'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'images'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'images_posts'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'invites'          => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'job_categories'   => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'local_names'      => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'messages'         => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'notifications'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'oauth_tokens'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'post_likes'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'post_mentions'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'post_reads'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'posts'            => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'send_mails'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'team_members'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'teams'            => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'threads'          => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
                'users'            => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                ),
            ),
            'create_field' => array(
                'badges'           => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'comment_likes'    => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'comment_mentions' => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'comment_reads'    => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'comments'         => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'emails'           => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'given_badges'     => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'groups'           => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'images'           => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'images_posts'     => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'invites'          => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'job_categories'   => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'local_names'      => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'messages'         => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'notifications'    => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'oauth_tokens'     => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'post_likes'       => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'post_mentions'    => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'post_reads'       => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'posts'            => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'send_mails'       => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'team_members'     => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'teams'            => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'threads'          => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'users'            => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'badges'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'comment_likes'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'comment_mentions' => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'comment_reads'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'comments'         => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'emails'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'given_badges'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'groups'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'images'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'images_posts'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'invites'          => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'job_categories'   => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'local_names'      => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'messages'         => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'notifications'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'oauth_tokens'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'post_likes'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'post_mentions'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'post_reads'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'posts'            => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'send_mails'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'team_members'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'teams'            => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'threads'          => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'users'            => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
            ),
            'drop_field'  => array(
                'badges'           => array('', 'indexes' => array('del_flg')),
                'comment_likes'    => array('', 'indexes' => array('del_flg')),
                'comment_mentions' => array('', 'indexes' => array('del_flg')),
                'comment_reads'    => array('', 'indexes' => array('del_flg')),
                'comments'         => array('', 'indexes' => array('del_flg')),
                'emails'           => array('', 'indexes' => array('del_flg')),
                'given_badges'     => array('', 'indexes' => array('del_flg')),
                'groups'           => array('', 'indexes' => array('del_flg')),
                'images'           => array('', 'indexes' => array('del_flg')),
                'images_posts'     => array('', 'indexes' => array('del_flg')),
                'invites'          => array('', 'indexes' => array('del_flg')),
                'job_categories'   => array('', 'indexes' => array('del_flg')),
                'local_names'      => array('', 'indexes' => array('del_flg')),
                'messages'         => array('', 'indexes' => array('del_flg')),
                'notifications'    => array('', 'indexes' => array('del_flg')),
                'oauth_tokens'     => array('', 'indexes' => array('del_flg')),
                'post_likes'       => array('', 'indexes' => array('del_flg')),
                'post_mentions'    => array('', 'indexes' => array('del_flg')),
                'post_reads'       => array('', 'indexes' => array('del_flg')),
                'posts'            => array('', 'indexes' => array('del_flg')),
                'send_mails'       => array('', 'indexes' => array('del_flg')),
                'team_members'     => array('', 'indexes' => array('del_flg')),
                'teams'            => array('', 'indexes' => array('del_flg')),
                'threads'          => array('', 'indexes' => array('del_flg')),
                'users'            => array('', 'indexes' => array('del_flg')),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
