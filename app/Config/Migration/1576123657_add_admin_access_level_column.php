<?php
class AddAdminAccessLevelColumn extends CakeMigration
{

	/**
	 * Migration description
	 *
	 * @var string
	 */
	public $description = 'add_admin_access_level_column';

	/**
	 * Actions to be performed
	 *
	 * @var array $migration
	 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'admin_users' => array(
					'access_level' => array('type' => 'integer', 'null' => false, 'default' => 10, 'unsigned' => true, 'after' => 'name'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'admin_users' => array('access_level'),
			),
		),
	);

	/**
	 * Before migration callback
	 *
	 * @param string $direction Direction of migration process (up or down)
	 * @return bool Should process continue
	 */
	public function before($direction)
	{
		return true;
	}

	/**
	 * After migration callback
	 *
	 * @param string $direction Direction of migration process (up or down)
	 * @return bool Should process continue
	 */
	public function after($direction)
	{
		return true;
	}
}
