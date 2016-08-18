<?php
class ModifyColumnNameOfSenderOnMessage0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modify_column_name_of_sender_on_message_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'messages' => array(
					'sender_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メッセしたユーザID(belongsToでUserモデルに関連)', 'after' => 'topic_id'),
					'indexes' => array(
						'user_id' => array('column' => 'sender_user_id', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'messages' => array('user_id', 'indexes' => array('user_id')),
			),
		),
		'down' => array(
			'drop_field' => array(
				'messages' => array('sender_user_id', 'indexes' => array('user_id')),
			),
			'create_field' => array(
				'messages' => array(
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メッセしたユーザID(belongsToでUserモデルに関連)'),
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
