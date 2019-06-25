<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

class TranslationFixture extends CakeTestFixtureEx
{
    public $fields = [
        'id'              => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'content_type'    => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'key' => 'index', 'comment' => 'Translation content type'],
        'content_id'      => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Translation content ID'],
        'body'            => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation content', 'charset' => 'utf8mb4'],
        'language'        => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation language', 'charset' => 'utf8mb4'],
        'status'          => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'Translation status'],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'        => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'         => [
            'PRIMARY'      => ['column' => 'id', 'unique' => 1],
            'content_type' => ['column' => ['content_type', 'content_id', 'language'], 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    public $records = [];
}