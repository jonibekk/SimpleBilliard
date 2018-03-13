<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * Video PostDraft
 */
class PostDraftFixture extends CakeTestFixtureEx {

    /**
     * Fields
     *
     * @var array
     */
	public $fields = array(
        'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
        'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= teams.id'),
        'post_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '= posts.id (set if draft published)'),
        'draft_data' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Json encoded draft post data', 'charset' => 'utf8mb4'),
        'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
	);

    public $records = [
    ];

}
