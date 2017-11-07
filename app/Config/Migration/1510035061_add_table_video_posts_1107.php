<?php
class AddTableVideoPosts1107 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_table_video_posts_1107';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'post_drafts' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= teams.id'),
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
				),
				'post_resources' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'post_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= posts.id'),
					'post_draft_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= post_drafts.id'),
					'resource_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'type of resource e.g. image, video, ...'),
					'resource_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'resource table\'s primary key id'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'post_id' => array('column' => 'post_id', 'unique' => 0),
						'post_draft_id' => array('column' => 'post_draft_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'video_streams' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'video_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'unique', 'comment' => '= videos.id'),
					'duration' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'video stream duration second'),
					'aspect_ratio' => array('type' => 'float', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'streams width/height ratio'),
					'key_master_playlist' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8mb4_general_ci', 'comment' => 'cloud storage stored key', 'charset' => 'utf8mb4'),
					'status_transcode' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '-1=error
0=none
1=uploading(to cloud storage)
2=upload complete
3=queued
4=transcoding
5=transcode complete'),
					'transcode_info' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Json encoded transcoding info', 'charset' => 'utf8mb4'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'video_id_unique' => array('column' => 'video_id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'videos' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= teams.id'),
					'duration' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'video duration second'),
					'width' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'video width(px)'),
					'height' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'video height(px)'),
					'hash' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 256, 'collate' => 'utf8mb4_general_ci', 'comment' => 'video file hash sha256', 'charset' => 'utf8mb4'),
					'file_size' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'video file byte size'),
					'file_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8mb4_general_ci', 'comment' => 'filename of original uploaded file', 'charset' => 'utf8mb4'),
					'file_key' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 1024, 'collate' => 'utf8mb4_general_ci', 'comment' => 'cloud storage stored key', 'charset' => 'utf8mb4'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'user_id' => array('column' => 'user_id', 'unique' => 0),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'post_drafts', 'post_resources', 'video_streams', 'videos'
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
