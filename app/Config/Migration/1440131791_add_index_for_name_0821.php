<?php

class AddIndexForName0821 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_index_for_name_0821';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'local_names' => array(
                    'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '名', 'charset' => 'utf8'),
                    'last_name'  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '姓', 'charset' => 'utf8'),
                ),
                'users'       => array(
                    'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '英名', 'charset' => 'utf8'),
                    'last_name'  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '英姓', 'charset' => 'utf8'),
                ),
            ),
            'create_field' => array(
                'local_names' => array(
                    'indexes' => array(
                        'first_name' => array('column' => 'first_name', 'unique' => 0),
                        'last_name'  => array('column' => 'last_name', 'unique' => 0),
                    ),
                ),
                'users'       => array(
                    'indexes' => array(
                        'first_name' => array('column' => 'first_name', 'unique' => 0),
                        'last_name'  => array('column' => 'last_name', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'local_names' => array(
                    'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '名', 'charset' => 'utf8'),
                    'last_name'  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '姓', 'charset' => 'utf8'),
                ),
                'users'       => array(
                    'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英名', 'charset' => 'utf8'),
                    'last_name'  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英姓', 'charset' => 'utf8'),
                ),
            ),
            'drop_field'  => array(
                'local_names' => array('indexes' => array('first_name', 'last_name')),
                'users'       => array('indexes' => array('first_name', 'last_name')),
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
