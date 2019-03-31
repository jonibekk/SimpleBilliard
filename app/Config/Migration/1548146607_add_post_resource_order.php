<?php
class AddPostResourceOrder extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'AddPostResourceOrder';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'create_field' => array(
                'post_resources' => array(
                    'resource_order' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'after' => 'resource_id', 'comment' => 'order of the resource to list in post'),
                ),
            ),
		),
		'down' => array(
            'drop_field' => array(
                'post_resources' => array('resource_order'),
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
