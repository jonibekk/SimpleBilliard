<?php

class AddIndexOnNotifications0819 extends CakeMigration
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
                'notifications' => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'タイプ(1:ゴール,2:投稿,3:etc ...)'),
                ),
            ),
            'create_field' => array(
                'notifications' => array(
                    'indexes' => array(
                        'type' => array('column' => 'type', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'notifications' => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'タイプ(1:ゴール,2:投稿,3:etc ...)'),
                ),
            ),
            'drop_field'  => array(
                'notifications' => array('', 'indexes' => array('type')),
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
