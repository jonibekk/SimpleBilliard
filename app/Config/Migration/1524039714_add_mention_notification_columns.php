<?php
class AddMentionNotificationColumns extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_mention_notification_columns';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'notify_settings' => array(
					'feed_mentioned_in_comment_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'after' => 'modified'),
					'feed_mentioned_in_comment_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'after' => 'feed_mentioned_in_comment_app_flg'),
					'feed_mentioned_in_comment_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'after' => 'feed_mentioned_in_comment_email_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'notify_settings' => array('feed_mentioned_in_comment_app_flg', 'feed_mentioned_in_comment_email_flg', 'feed_mentioned_in_comment_mobile_flg'),
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
