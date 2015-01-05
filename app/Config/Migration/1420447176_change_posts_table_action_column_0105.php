<?php

class ChangePostsTableActionColumn0105 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_posts_table_action_column_0105';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'posts' => array(
                    'action_result_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'アクション結果ID', 'after' => 'goal_id'),
                    'indexes'          => array(
                        'action_result_id' => array('column' => 'action_result_id', 'unique' => 0),
                    ),
                ),
            ),
            'drop_field'   => array(
                'posts' => array('action_id'),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'posts' => array('action_result_id', 'indexes' => array('action_result_id')),
            ),
            'create_field' => array(
                'posts' => array(
                    'action_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'アクションID'),
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
