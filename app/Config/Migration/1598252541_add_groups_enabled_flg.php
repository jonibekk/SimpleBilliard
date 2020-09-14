<?php
class AddGroupsFuenabledFlg extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_groups_fuenabled_flg';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up' => array(
            'create_field' => array(
                'teams' => array(
                    'groups_enabled_flg' => array(
                        'type' => 'boolean',
                        'null' => false,
                        'default' => 0,
                        'comment' => 'status indicating if a team has groups functionality enabled'
                    )
                )
            )
        ),
        'down' => array(
            'drop_field' => array(
                'teams' => array(
                    'groups_enabled_flg'
                )
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
