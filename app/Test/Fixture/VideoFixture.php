<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * Video Fixture
 */
class VideoFixture extends CakeTestFixtureEx {

    /**
     * Fields
     *
     * @var array
     */
	public $fields = array(
        'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
        'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= teams.id'),
        'duration' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video duration second'),
        'width' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video width(px)'),
        'height' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video height(px)'),
        'hash' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 256, 'collate' => 'utf8mb4_general_ci', 'comment' => 'video file hash sha256', 'charset' => 'utf8mb4'),
        'file_size' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video file byte size'),
        'file_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'filename of original uploaded file', 'charset' => 'utf8mb4'),
        'resource_path' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'utf8mb4_general_ci', 'comment' => 'cloud storage stored key', 'charset' => 'utf8mb4'),
        'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
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
