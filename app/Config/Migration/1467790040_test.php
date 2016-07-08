<?php

class Test extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'test';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field' => array(
                'devices' => array(
                    'device_token' => array(
                        'type'    => 'string',
                        'null'    => false,
                        'default' => null,
                        'key'     => 'index',
                        'collate' => 'utf8mb4_general_ci',
                        'comment' => 'nitfy cloud id',
                        'charset' => 'utf8mb4'
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'devices' => array(
                    'device_token' => array('type'    => 'string',
                                            'null'    => false,
                                            'default' => null,
                                            'key'     => 'index',
                                            'collate' => 'utf8mb4_general_ci',
                                            'comment' => 'アプリインストール毎に発行される識別子',
                                            'charset' => 'utf8mb4'
                    ),
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
