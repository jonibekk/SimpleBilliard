<?php

class DelUnnecessaryFieldsForInsights0915 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'del_unnecessary_fields_for_insights_0915';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'drop_field' => array(
                'circle_insights' => array('post_count', 'post_read_count', 'post_like_count', 'comment_count'),
                'group_insights'  => array('access_user_count', 'message_count', 'action_count', 'action_user_count', 'post_count', 'post_user_count', 'like_count', 'comment_count', 'collabo_count', 'collabo_action_count'),
                'team_insights'   => array('access_user_count', 'message_count', 'action_count', 'action_user_count', 'post_count', 'post_user_count', 'like_count', 'comment_count', 'collabo_count', 'collabo_action_count'),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'circle_insights' => array(
                    'post_count'      => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                    'post_read_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                    'post_like_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                    'comment_count'   => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                ),
                'group_insights'  => array(
                    'access_user_count'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'message_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'action_count'         => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'action_user_count'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'post_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'post_user_count'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'like_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'comment_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'collabo_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'collabo_action_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                ),
                'team_insights'   => array(
                    'access_user_count'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'message_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'action_count'         => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'action_user_count'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'post_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'post_user_count'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'like_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'comment_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'collabo_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'collabo_action_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
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
