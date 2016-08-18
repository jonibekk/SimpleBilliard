<?php
class RmMessageReadCountColumnFromMessagesTable0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'rm_message_read_count_column_from_messages_table_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				'messages' => array('message_read_count'),
			),
		),
		'down' => array(
			'create_field' => array(
				'messages' => array(
					'message_read_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'メッセージ読んだ数(message_readsテーブルにレコードが追加されたらカウントアップされる)'),
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
