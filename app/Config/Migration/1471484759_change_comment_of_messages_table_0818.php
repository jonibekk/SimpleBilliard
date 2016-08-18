<?php
class ChangeCommentOfMessagesTable0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_comment_of_messages_table_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'messages' => array(
					'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'メッセタイプ(1:Nomal,2:メンバー追加,3:メンバー削除,4:トピック名変更)'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'messages' => array(
					'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'メッセタイプ(1:Nomal,2:メンバー追加,3:メンバー削除,4:メンバー離脱)'),
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
