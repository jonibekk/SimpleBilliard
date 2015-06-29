<?php

class RemovePublicFlgFromPosts0625 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'remove_public_flg_from_posts_0625';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'drop_field' => array(
                'posts' => array('public_flg'),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'posts' => array(
                    'public_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
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
