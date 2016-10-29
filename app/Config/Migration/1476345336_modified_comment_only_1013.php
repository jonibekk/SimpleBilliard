<?php
class ModifiedCommentOnly1013 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modified_comment_only_1013';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'approval_histories' => array(
					'goal_member_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールメンバーID(hasManyでcollaboratorモデルに関連)'),
				),
				'goal_members' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ゴールメンバーID'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'approval_histories' => array(
					'goal_member_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コラボレーターID(hasManyでcollaboratorモデルに関連)'),
				),
				'goal_members' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'コラボレータID'),
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
