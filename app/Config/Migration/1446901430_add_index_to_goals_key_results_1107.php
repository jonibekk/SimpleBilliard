<?php

class AddIndexToGoalsKeyResults1107 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_index_to_goals_key_results_1107';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'goals'       => array(
                    'start_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '開始日(unixtime)'),
                    'end_date'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '終了日(unixtime)'),
                ),
                'key_results' => array(
                    'start_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '開始日(unixtime)'),
                    'end_date'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '終了日(unixtime)'),
                ),
            ),
            'create_field' => array(
                'goals'       => array(
                    'indexes' => array(
                        'start_date' => array('column' => 'start_date', 'unique' => 0),
                        'end_date'   => array('column' => 'end_date', 'unique' => 0),
                    ),
                ),
                'key_results' => array(
                    'indexes' => array(
                        'start_date' => array('column' => 'start_date', 'unique' => 0),
                        'end_date'   => array('column' => 'end_date', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'goals'       => array(
                    'start_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日(unixtime)'),
                    'end_date'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日(unixtime)'),
                ),
                'key_results' => array(
                    'start_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日(unixtime)'),
                    'end_date'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日(unixtime)'),
                ),
            ),
            'drop_field'  => array(
                'goals'       => array('indexes' => array('start_date', 'end_date')),
                'key_results' => array('indexes' => array('start_date', 'end_date')),
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
