<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * ActionResultMemberFixture
 */
class ActionResultMemberFixture extends CakeTestFixtureEx
{
    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id' => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
        ],
        'action_result_id' => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
        ],
        'team_id'      => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
        ],
        'user_id'      => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
        ],
        'is_action_creator' => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
        ],
        'del_flg'  => [
            'type' => 'boolean',
            'null' => false,
            'default' => '0',
        ],
        'deleted'      => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
        ],
        'created'      => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
        ],
        'modified'     => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
        ],
        'indexes' => [
            'PRIMARY'                         => ['column' => 'id', 'unique' => 1],
            'unique_action_result_id_user_id' => ['column' => ['action_result_id', 'user_id'], 'unique' => 1],
            'index_team_id_user_id'           => ['column' => 'team_id', 'unique' => 0],
        ],
        'tableParameters'  => [
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine' => 'InnoDB',
        ]
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

}
