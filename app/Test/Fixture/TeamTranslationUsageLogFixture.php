<?php

App::uses('CakeTestFixtureEx', 'Test/Fixture');

class TeamTranslationUsageLogFixture extends CakeTestFixtureEx
{
    public $fields = [
        'id'              => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'team_id'         => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Team ID'],
        'start_date'      => ['type' => 'date', 'null' => false, 'default' => null],
        'end_date'        => ['type' => 'date', 'null' => false, 'default' => null],
        'translation_log' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation log, in JSON format', 'charset' => 'utf8mb4'],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'        => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'         => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    public $records = [];
}