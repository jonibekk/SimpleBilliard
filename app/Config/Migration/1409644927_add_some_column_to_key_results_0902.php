<?php

class AddSomeColumnToKeyResults0902 extends CakeMigration
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
            'create_field' => array(
                'key_results' => array(
                    'current_value' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '現在値', 'after' => 'due_date'),
                    'desired_value' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '目標値', 'after' => 'current_value'),
                ),
            ),
            'drop_field'   => array(
                'key_results' => array('value',),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'key_results' => array('current_value', 'desired_value',),
            ),
            'create_field' => array(
                'key_results' => array(
                    'value' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '目標値'),
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
