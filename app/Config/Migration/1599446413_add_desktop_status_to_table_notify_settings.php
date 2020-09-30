<?php
class AddDesktopStatusToTableNotifySettings extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_desktop_status_to_table_notify_settings';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'create_field' => array(
                'notify_settings' => array(
                    'desktop_status' => array('type' => 'string', 'null' => false, 'default' => 'all', 'length' => 16, 'collate' => 'utf8_general_ci', 'comment' => 'desktop notification setting', 'after' => 'mobile_status'),
                ),
            ),
        ),
        'down' => array(
			'drop_field' => array(
				'notify_settings' => array('desktop_status'),
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
