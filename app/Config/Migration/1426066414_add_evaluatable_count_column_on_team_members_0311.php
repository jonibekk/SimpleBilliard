<?php

class AddEvaluatableCountColumnOnTeamMembers0311 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_evaluatable_count_column_on_team_members_0311';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'team_members' => array(
                    'evaluable_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '要評価件数', 'after' => 'notify_unread_count'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'team_members' => array('evaluable_count'),
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
