<?php
class AddArchiveFlgToGroups extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_archive_flg_to_groups';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up' => array(
            'create_field' => array(
                'groups' => array(
                    'archived_flg' => array(
                        'type' => 'boolean',
                        'null' => false,
                        'default' => 0,
                        'comment' => 'archive status of a group'
                    ),
                    'indexes' => array(
                        'archived_flg' => array(
                            'column' => array('archived_flg'),
                            'unique' => 0
                        ),
                    )
                )
            )
        ),
        'down' => array(
            'drop_field' => array(
                'groups' => array(
                    'archived_flg'
                ),
                'indexes' => array(
                    'archived_flg'
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
