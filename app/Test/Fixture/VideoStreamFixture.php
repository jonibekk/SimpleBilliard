<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * Video Fixture
 */
class VideoStreamFixture extends CakeTestFixtureEx {

    /**
     * Fields
     *
     * @var array
     */
	public $fields = array(
        'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'video_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '= videos.id'),
        'duration' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'video stream duration second'),
        'aspect_ratio' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'streams width/height ratio'),
        'storage_path' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1024, 'collate' => 'utf8mb4_general_ci', 'comment' => 'cloud storage stored key', 'charset' => 'utf8mb4'),
        'output_version' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'specific version of output type'),
        'transcode_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => '-1=error, 0=none, 1=uploading(to cloud storage), 2=upload complete, 3=queued, 4=transcoding, 5=transcode complete'),
        'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
	);

    public $records = [
    ];

}
