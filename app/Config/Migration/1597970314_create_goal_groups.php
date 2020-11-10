<?php
class CreateGoalGroups extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'create_goal_groups';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up' => array(
            'create_table' => array(
                'goal_groups' => array(
                    'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'group_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'del_flg' => array('type' => 'boolean', 'null' => true, 'default' => false),
                    'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes' => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'goal_id' => array('column' => 'goal_id', 'unique' => 0),
                        'group_id' => array('column' => 'group_id', 'unique' => 0),
                    ),
                    'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                )
            )
        ),
        'down' => array(
            'drop_table' => array(
                'goal_groups'
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
