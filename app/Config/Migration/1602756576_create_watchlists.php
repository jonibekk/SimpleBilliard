<?php
class CreateWatchlists extends CakeMigration
{

	/**
	 * Migration description
	 *
	 * @var string
	 */
	public $description = 'create_watchlists';

	/**
	 * Actions to be performed
	 *
	 * @var array $migration
	 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'watchlists' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'name' => array('type' => 'string', 'null' => true, 'limit' => 2048, 'default' => null, 'collate' => 'utf8_general_ci'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'del_flg' => array('type' => 'boolean', 'null' => true, 'default' => false),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'user_id' => array('column' => 'user_id', 'unique' => 0),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
					),
					'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			)
		),
		'down' => array(
			'drop_table' => array(
				'watchlists'
			)
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
