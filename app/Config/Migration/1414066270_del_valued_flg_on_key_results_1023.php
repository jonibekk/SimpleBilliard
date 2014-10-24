<?php

class DelValuedFlgOnKeyResults1023 extends CakeMigration
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
            'drop_field' => array(
                'key_results' => array('valued_flg',),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'key_results' => array(
                    'valued_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '価値フラグ'),
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
