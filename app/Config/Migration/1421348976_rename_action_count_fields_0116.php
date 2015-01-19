<?php

class RenameActionCountFields0116 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'rename_action_count_fields_0116';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'rename_field' => array(
                'goals'       => array(
                    'action_count' => 'action_result_count'
                ),
                'key_results' => array(
                    'action_count' => 'action_result_count'
                ),
            ),
        ),
        'down' => array(
            'rename_field' => array(
                'goals'       => array(
                    'action_result_count' => 'action_count'
                ),
                'key_results' => array(
                    'action_result_count' => 'action_count'
                ),
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
