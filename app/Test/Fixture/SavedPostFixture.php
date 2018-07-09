<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * SavedPost Fixture
 */
class SavedPostFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
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
            'key'      => 'index'
        ),
        'post_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ),
        'user_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'         => array(
            'PRIMARY'            => array('column' => 'id', 'unique' => 1),
            'saved_posts_unique' => array('column' => array('post_id', 'user_id'), 'unique' => 1),
            'team_id'            => array('column' => 'team_id', 'unique' => 0),
            'post_id'            => array('column' => 'post_id', 'unique' => 0),
            'user_id'            => array('column' => 'user_id', 'unique' => 0),
            'created'            => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    public $records = [
        [
            'id'       => 100,
            'team_id'  => 1,
            'post_id'  => 11,
            'user_id'  => 1,
            'del_flg'  => false,
            'deleted'  => null,
            'created'  => 1,
            'modified' => 1
        ]
    ];
}
