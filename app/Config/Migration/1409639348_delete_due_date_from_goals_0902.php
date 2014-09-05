<?php

class DeleteDueDateFromGoals0902 extends CakeMigration
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
                'goals' => array('due_date',),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'goals' => array(
                    'due_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '期限(unixtime)'),
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
