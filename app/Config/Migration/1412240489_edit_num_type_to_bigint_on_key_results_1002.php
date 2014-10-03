<?php

class EditNumTypeToBigintOnKeyResults1002 extends CakeMigration
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
                'key_results' => array(
                    'current_value' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '現在値'),
                    'start_value'   => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '開始値'),
                    'target_value'  => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '目標値'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'key_results' => array(
                    'current_value' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '現在値'),
                    'start_value'   => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '開始値'),
                    'target_value'  => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '目標値'),
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
