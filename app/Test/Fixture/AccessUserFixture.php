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
    public $records = [
        [
            'id'          => 1,
            'team_id'     => 1,
            'user_id'     => 1,
            'access_date' => '2015-01-01',
            'timezone'    => 9,
        ],
        [
            'id'          => 2,
            'team_id'     => 2,
            'user_id'     => 1,
            'access_date' => '2015-01-01',
            'timezone'    => 9,
        ],
        [
            'id'          => 3,
            'team_id'     => 1,
            'user_id'     => 2,
            'access_date' => '2015-01-01',
            'timezone'    => 9,
        ],
        [
            'id'          => 4,
            'team_id'     => 1,
            'user_id'     => 1,
            'access_date' => '2015-01-02',
            'timezone'    => 9,
        ],
        [
            'id'          => 5,
            'team_id'     => 1,
            'user_id'     => 3,
            'access_date' => '2015-01-01',
            'timezone'    => 0,
        ],
        [
            'id'          => 6,
            'team_id'     => 1,
            'user_id'     => 3,
            'access_date' => '2015-01-03',
            'timezone'    => 9,
        ],
    ];

}
