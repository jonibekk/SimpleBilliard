<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * Video Fixture
 */
class VideoTranscodeLogFixture extends CakeTestFixtureEx {

    /**
     * Fields
     *
     * @var array
     */
	public $fields = array(
        'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'video_transcode_logs.id'),
        'video_streams_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= video_streams.id'),
        'log' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Json encoded transcoding log', 'charset' => 'utf8mb4'),
        'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'video_streams_id' => array('column' => 'video_streams_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
	);

    public $records = [
    ];

}
