<?php
class ModifyColumnNameOfTopicUserId0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modify_column_name_of_topic_user_id_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'topics' => array(
					'create_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿作成ユーザID(belongsToでUserモデルに関連)', 'after' => 'id'),
					'indexes' => array(
						'user_id' => array('column' => 'create_user_id', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'topics' => array('user_id', 'indexes' => array('user_id')),
			),
		),
		'down' => array(
			'drop_field' => array(
				'topics' => array('create_user_id', 'indexes' => array('user_id')),
			),
			'create_field' => array(
				'topics' => array(
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿作成ユーザID(belongsToでUserモデルに関連)'),
					'indexes' => array(
						'user_id' => array(),
					),
				),
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
