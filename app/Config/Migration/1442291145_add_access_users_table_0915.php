<?php

class AddAccessUsersTable0915 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_access_users_table_0915';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'access_users' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
                    'access_date'     => array('type' => 'date', 'null' => true, 'default' => null),
                    'timezone'        => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'         => array(
                        'PRIMARY'             => array('column' => 'id', 'unique' => 1),
                        'team_id_access_date' => array('column' => array('team_id', 'access_date'), 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'access_users'
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
