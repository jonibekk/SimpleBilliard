<?php

class AddTimezoneForTeam1011 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_timezone_for_team_1011';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'teams' => array(
                    'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームのタイムゾーン', 'after' => 'border_months'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'teams' => array('timezone'),
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
