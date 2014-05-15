<?php

class AddInvitationFlgForTeamMember extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     * @access public
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     * @access public
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'team_members' => array(
                    'invitation_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '招待中フラグ(招待済みで非アクティブユーザの管理用途)', 'after' => 'active_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'team_members' => array('invitation_flg',),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     * @access public
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
     * @access public
     */
    public function after($direction)
    {
        return true;
    }
}
