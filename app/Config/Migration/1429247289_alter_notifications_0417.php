<?php

class AlterNotifications0417 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'alter_notifications_0417';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field' => array(
                'notifications' => array(
                    'item_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アイテム名(投稿内容、コメント内容等)', 'charset' => 'utf8'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'notifications' => array(
                    'item_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アイテム名(投稿内容、コメント内容等)', 'charset' => 'utf8'),
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
