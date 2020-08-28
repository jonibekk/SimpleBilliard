<?php
class AddAdminGrpFeatToggle extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_admin_grp_feat_toggle';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up' => array(
            'create_field' => array(
                'teams' => array(
                    'admin_grp_feat_toggle' => array(
                        'type' => 'boolean',
                        'null' => false,
                        'default' => 0,
                        'comment' => 'toggle whether to show a team the see_gka group feature'
                    ),
                )
            )
        ),
        'down' => array(
            'drop_field' => array(
                'teams' => array(
                    'admin_grp_feat_toggle'
                ),
            )
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
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
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
