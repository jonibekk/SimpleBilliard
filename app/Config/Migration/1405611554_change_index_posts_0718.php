<?php

class ChangeIndexPosts0718 extends CakeMigration
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
                'posts' => array(
                    'indexes' => array(
                        'team_id_modified' => array('column' => array('team_id', 'modified'), 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'posts' => array('', 'indexes' => array('team_id_modified')),
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
