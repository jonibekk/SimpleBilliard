<?php

class AddIndexToPosts0604 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_index_to_posts_0604';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'posts' => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
                ),
            ),
            'create_field' => array(
                'posts' => array(
                    'indexes' => array(
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'posts' => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
                ),
            ),
            'drop_field'  => array(
                'posts' => array('indexes' => array('created')),
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
