<?php

/**
 * AccessUserFixture

 */
class AccessUserFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'              => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'team_id'         => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'],
        'user_id'         => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true],
        'access_date'     => ['type' => 'date', 'null' => true, 'default' => null],
        'timezone'        => ['type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'        => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'         => [
            'PRIMARY'             => ['column' => 'id', 'unique' => 1],
            'team_id_access_date' => ['column' => ['team_id', 'access_date'], 'unique' => 0]
        ],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

}
