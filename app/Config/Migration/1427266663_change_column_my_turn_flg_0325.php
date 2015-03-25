<?php

class ChangeColumnMyTurnFlg0325 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_column_my_turn_flg_0325';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field' => array(
                'evaluations' => array(
                    'my_turn_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'evaluations' => array(
                    'my_turn_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
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
