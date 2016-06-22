<?php
class AddSetupGuideColumnsToNotifySettings extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_setup_guide_columns_to_notify_settings';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'notify_settings' => array(
					'setup_guide_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'セットアップガイドからのアプリ通知', 'after' => 'feed_message_mobile_flg'),
					'setup_guide_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'セットアップガイドからのメール通知', 'after' => 'setup_guide_app_flg'),
					'setup_guide_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'セットアップガイドからのモバイル通知', 'after' => 'setup_guide_email_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'notify_settings' => array('setup_guide_app_flg', 'setup_guide_email_flg', 'setup_guide_mobile_flg'),
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
