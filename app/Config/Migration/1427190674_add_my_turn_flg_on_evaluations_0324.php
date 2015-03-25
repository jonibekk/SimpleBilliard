<?php

class AddMyTurnFlgOnEvaluations0324 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_my_turn_flg_on_evaluations_0324';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'evaluations' => array(
                    'my_turn_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'after' => 'status'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'evaluations' => array('my_turn_flg'),
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
