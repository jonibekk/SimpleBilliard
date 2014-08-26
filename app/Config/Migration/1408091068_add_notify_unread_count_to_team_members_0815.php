<?php

class AddNotifyUnreadCountToTeamMembers0815 extends CakeMigration
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
                'team_members' => array(
                    'notify_unread_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '通知未読件数', 'after' => 'admin_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'team_members' => array('notify_unread_count',),
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
