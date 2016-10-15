<?php
class RenameTableNameOfCollaborators1013 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'rename_table_name_of_collaborators_1013';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'rename_table' => [
                'collaborators' => 'goal_members',
            ],
            'rename_field' => [
                'approval_histories' => [
                    'collaborator_id' => 'goal_member_id',
                ]
            ],
		),
		'down' => array(
            'rename_table' => [
                'goal_members' => 'collaborators',
            ],
            'rename_field' => [
                'approval_histories' => [
                    'goal_member_id' => 'collaborator_id',
                ]
            ],
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
