<?php

class DeleteSomeColumnFromGoalous0828 extends CakeMigration
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
                'goals' => array('goal', 'goal_value', 'goal_value_unit',),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'goals' => array(
                    'goal'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '目標', 'charset' => 'utf8'),
                    'goal_value'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '目標値'),
                    'goal_value_unit' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '目標値の単位'),
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
