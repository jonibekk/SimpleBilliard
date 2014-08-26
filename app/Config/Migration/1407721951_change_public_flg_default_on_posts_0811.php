<?php

class ChangePublicFlgDefaultOnPosts0811 extends CakeMigration
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
            'alter_field' => array(
                'posts' => array(
                    'public_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'posts' => array(
                    'public_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index'),
                ),
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
