<?php

App::uses('CakeTestFixtureEx', 'Test/Fixture');

class TeamLoginMethodFixture extends CakeTestFixtureEx
{
    public $fields = [
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'Team ID'
        ),
        'method'          => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 50,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Login method of the team',
            'charset' => 'utf8mb4'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => array('team_id', 'method'), 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    ];

    public $records = [];
}
