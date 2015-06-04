<?php

class RemoveAllFlgTypeAndStatus0604 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'remove_all_flg_type_and_status_0604';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'drop_field' => array(
                'action_results'      => array('indexes' => array('del_flg')),
                'actions'             => array('indexes' => array('del_flg')),
                'approval_histories'  => array('indexes' => array('del_flg')),
                'badges'              => array('indexes' => array('del_flg', 'active_flg', 'type')),
                'circle_members'      => array('indexes' => array('del_flg', 'admin_flg')),
                'circles'             => array('indexes' => array('del_flg', 'public_flg')),
                'collaborators'       => array('indexes' => array('del_flg')),
                'comment_likes'       => array('indexes' => array('del_flg')),
                'comment_mentions'    => array('indexes' => array('del_flg')),
                'comment_reads'       => array('indexes' => array('del_flg')),
                'comments'            => array('indexes' => array('del_flg')),
                'emails'              => array('indexes' => array('del_flg')),
                'evaluate_scores'     => array('indexes' => array('del_flg')),
                'evaluate_terms'      => array('indexes' => array('del_flg')),
                'evaluation_settings' => array('indexes' => array('del_flg')),
                'evaluations'         => array('indexes' => array('del_flg')),
                'evaluators'          => array('indexes' => array('del_flg')),
                'followers'           => array('indexes' => array('del_flg')),
                'given_badges'        => array('indexes' => array('del_flg')),
                'goal_categories'     => array('indexes' => array('del_flg')),
                'goals'               => array('indexes' => array('del_flg')),
                'groups'              => array('indexes' => array('del_flg', 'active_flg')),
                'invites'             => array('indexes' => array('del_flg')),
                'job_categories'      => array('indexes' => array('del_flg', 'active_flg')),
                'key_results'         => array('indexes' => array('del_flg')),
                'local_names'         => array('indexes' => array('del_flg')),
                'member_groups'       => array('indexes' => array('del_flg')),
                'member_types'        => array('indexes' => array('del_flg', 'active_flg')),
                'messages'            => array('indexes' => array('del_flg')),
                'notify_settings'     => array('indexes' => array('del_flg')),
                'oauth_tokens'        => array('indexes' => array('del_flg')),
                'post_likes'          => array('indexes' => array('del_flg')),
                'post_mentions'       => array('indexes' => array('del_flg')),
                'post_reads'          => array('indexes' => array('del_flg')),
                'post_share_circles'  => array('indexes' => array('del_flg')),
                'post_share_users'    => array('indexes' => array('del_flg')),
                'posts'               => array('indexes' => array('del_flg', 'type', 'public_flg', 'important_flg')),
                'purposes'            => array('indexes' => array('del_flg')),
                'send_mail_to_users'  => array('indexes' => array('del_flg')),
                'send_mails'          => array('indexes' => array('del_flg')),
                'team_members'        => array('indexes' => array('del_flg', 'active_flg', 'admin_flg')),
                'teams'               => array('indexes' => array('del_flg')),
                'threads'             => array('indexes' => array('del_flg', 'type', 'status')),
                'users'               => array('indexes' => array('del_flg', 'active_flg')),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'action_results'      => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'actions'             => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'approval_histories'  => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'badges'              => array(
                    'indexes' => array(
                        'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                        'type'       => array('column' => 'type', 'unique' => 0),
                    ),
                ),
                'circle_members'      => array(
                    'indexes' => array(
                        'del_flg'   => array('column' => 'del_flg', 'unique' => 0),
                        'admin_flg' => array('column' => 'admin_flg', 'unique' => 0),
                    ),
                ),
                'circles'             => array(
                    'indexes' => array(
                        'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
                        'public_flg' => array('column' => 'public_flg', 'unique' => 0),
                    ),
                ),
                'collaborators'       => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'comment_likes'       => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'comment_mentions'    => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'comment_reads'       => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'comments'            => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'emails'              => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'evaluate_scores'     => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'evaluate_terms'      => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'evaluation_settings' => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'evaluations'         => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'evaluators'          => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'followers'           => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'given_badges'        => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'goal_categories'     => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'goals'               => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'groups'              => array(
                    'indexes' => array(
                        'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                    ),
                ),
                'invites'             => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'job_categories'      => array(
                    'indexes' => array(
                        'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                    ),
                ),
                'key_results'         => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'local_names'         => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'member_groups'       => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'member_types'        => array(
                    'indexes' => array(
                        'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                    ),
                ),
                'messages'            => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'notify_settings'     => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'oauth_tokens'        => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'post_likes'          => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'post_mentions'       => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'post_reads'          => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'post_share_circles'  => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'post_share_users'    => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'posts'               => array(
                    'indexes' => array(
                        'del_flg'       => array('column' => 'del_flg', 'unique' => 0),
                        'type'          => array('column' => 'type', 'unique' => 0),
                        'public_flg'    => array('column' => 'public_flg', 'unique' => 0),
                        'important_flg' => array('column' => 'important_flg', 'unique' => 0),
                    ),
                ),
                'purposes'            => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'send_mail_to_users'  => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'send_mails'          => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'team_members'        => array(
                    'indexes' => array(
                        'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                        'admin_flg'  => array('column' => 'admin_flg', 'unique' => 0),
                    ),
                ),
                'teams'               => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                ),
                'threads'             => array(
                    'indexes' => array(
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                        'type'    => array('column' => 'type', 'unique' => 0),
                        'status'  => array('column' => 'status', 'unique' => 0),
                    ),
                ),
                'users'               => array(
                    'indexes' => array(
                        'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                    ),
                ),
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
