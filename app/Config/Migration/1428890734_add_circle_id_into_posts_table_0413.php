<?php

class AddCircleIdIntoPostsTable0413 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_circle_id_into_posts_table_0413';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'posts' => array(
                    'circle_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'サークルID', 'after' => 'goal_id'),
                    'indexes'   => array(
                        'circle_id' => array('column' => 'circle_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'posts' => array('circle_id', 'indexes' => array('circle_id')),
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
