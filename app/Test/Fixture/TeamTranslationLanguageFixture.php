<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

class TeamTranslationLanguageFixture extends CakeTestFixtureEx
{
    public $fields = [
        'id'              => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'team_id'         => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'Team ID'],
        'language'        => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ISO 639-1 Language code', 'charset' => 'utf8mb4'],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'        => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'         => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1],
            'team_id' => ['column' => ['team_id', 'language'], 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    public $records = [];
}