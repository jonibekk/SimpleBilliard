<?php

class EditNotifications0819 extends CakeMigration
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
                'notifications' => array(
                    'count_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'メッセージ内で利用する件数'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'notifications' => array(
                    'count_num' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メッセージ内で利用する件数'),
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
