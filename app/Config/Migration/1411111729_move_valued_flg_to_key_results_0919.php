<?php

class MoveValuedFlgToKeyResults0919 extends CakeMigration
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
            'drop_field'   => array(
                'goals' => array('valued_flg',),
            ),
            'create_field' => array(
                'key_results' => array(
                    'valued_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '価値フラグ', 'after' => 'special_flg'),
                ),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'goals' => array(
                    'valued_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '価値フラグ'),
                ),
            ),
            'drop_field'   => array(
                'key_results' => array('valued_flg',),
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
