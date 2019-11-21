<?php
class CreateSearchPostFilesTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_search_post_files_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'search_post_files' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
					'circle_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '= circles.id'),
					'post_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '= posts.id'),
					'comment_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= comments.id or NULL'),
					'attached_file_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= attached_files.id - if attached file'),
					'video_stream_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= video_streams.id - if video stream'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'circle_id' => array('column' => array('circle_id','post_id'), 'unique' => 0),
						'post_id' => array('column' => 'post_id', 'unique' => 0),
						'comment_id' => array('column' => 'comment_id', 'unique' => 0),
						'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0),
						'video_stream_id' => array('column' => 'video_stream_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'search_post_files'
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
