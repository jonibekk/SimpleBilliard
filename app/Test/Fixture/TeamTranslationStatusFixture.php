<?php

App::uses('CakeTestFixtureEx', 'Test/Fixture');

class TeamTranslationStatusFixture extends CakeTestFixtureEx
{
    public $fields = [
        'id'                        => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'team_id'                   => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'unique', 'comment' => 'Team ID'],
        'circle_post_total'         => ['type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated circle post'],
        'circle_post_comment_total' => ['type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated comment of circle post'],
        'action_post_total'         => ['type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated action post'],
        'action_post_comment_total' => ['type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated comment of action post'],
        'total_limit'               => ['type' => 'biginteger', 'null' => false, 'default' => '10000', 'unsigned' => true, 'comment' => 'Total translation limit of the team'],
        'del_flg'                   => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'                   => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'                   => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'                  => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'                   => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1],
            'team_id' => ['column' => 'team_id', 'unique' => 1]
        ],
        'tableParameters'           => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    public $records = [];
}