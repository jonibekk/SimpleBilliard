<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * Experiment Fixture
 */
class ExperimentFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'              => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ],
        'name'            => [
            'type'    => 'string',
            'null'    => false,
            'length'  => 128,
            'key'     => 'index',
            'collate' => 'utf8mb4_general_ci',
            'comment' => '実験の識別子',
            'charset' => 'utf8mb4'
        ],
        'team_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
        'deleted'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'        => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'         => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1],
            'team_id' => ['column' => 'team_id', 'unique' => 0],
            'name'    => ['column' => 'name', 'unique' => 0]
        ],
        'tableParameters' => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

}
