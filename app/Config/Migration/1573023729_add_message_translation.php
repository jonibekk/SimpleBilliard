<?php
class AddMessageTranslation extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_message_translation';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'messages' => array(
					'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Message body\'s detected language', 'charset' => 'utf8mb4', 'after' => 'body'),
				),
                'team_translation_statuses' => array(
                    'message_total' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated message', 'after' => 'action_post_comment_total'),
                ),
			),
		),
		'down' => array(
			'drop_field' => array(
				'messages' => array('language'),
                'team_translation_statuses' => array('message_total')
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
