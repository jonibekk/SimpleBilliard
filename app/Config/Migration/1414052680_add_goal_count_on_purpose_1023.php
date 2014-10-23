<?php

class AddGoalCountOnPurpose1023 extends CakeMigration
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
                'purposes' => array(
                    'goal_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ゴール数', 'after' => 'team_id'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'purposes' => array('goal_count',),
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
