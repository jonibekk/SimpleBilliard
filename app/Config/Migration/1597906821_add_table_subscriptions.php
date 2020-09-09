<?php
class AddTableSubscriptions extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_table_subscriptions';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
        'up' => array(
            'create_table' => array(
                'subscriptions' => array(
                    'id'              => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary'
                    ),
                    'user_id'         => array(
                        'type'     => 'biginteger',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '= users.id'
                    ),
                    'subscription'    => array(
                        'type' => 'string', 
                        'null' => true, 
                        'limit' => 2048, 
                        'default' => null, 
                        'collate' => 'utf8_general_ci', 
                        'comment' => '', 
                        'charset' => 'utf8'
                    ),
                    'subscription_hash'    => array(
                        'type' => 'string', 
                        'null' => true, 
                        'limit' => 128, 
                        'default' => null, 
                        'collate' => 'utf8_general_ci', 
                        'comment' => '', 
                        'charset' => 'utf8'
                    ),
                    'del_flg'         => array(
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => 'delete flag'
                    ),
                    'deleted'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'created'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'modified'        => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'indexes'         => array(
                        'PRIMARY'      => array('column' => 'id', 'unique' => 1),
                        'user_subscription'  => array('column' => array('user_id', 'subscription_hash'), 'unique' => 0),
                        'subscription'  => array('column' => array('subscription_hash'), 'unique' => 1),
                    ),
                    'tableParameters' => array(
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ),
                ),
            ),
        ),
		'down' => array(
            'drop_table' => array(
                'subscriptions'
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
