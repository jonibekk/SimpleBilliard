<?php

class DeleteSomeColumnsOnActionResults0116 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'delete_some_columns_on_action_results_0116';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'drop_field'   => array(
                'action_results' => array('action_id', 'completed_user_id', 'scheduled', 'completed_flg', 'indexes' => array('action_id', 'created_user_id', 'completed_user_id')),
            ),
            'rename_field' => array(
                'action_results' => array(
                    'created_user_id' => 'user_id',
                )
            ),
            'create_field' => array(
                'action_results' => array(
                    'indexes' => array(
                        'user_id' => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'action_results' => array('indexes' => array('user_id')),
            ),
            'rename_field' => array(
                'action_results' => array(
                    'user_id' => 'created_user_id',
                )
            ),
            'create_field' => array(
                'action_results' => array(
                    'action_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'アクションID(belongsToでGoalモデルに関連)'),
                    'completed_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '完了者ID(belongsToでUserモデルに関連)'),
                    'scheduled'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '予定日'),
                    'completed_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '完了フラグ'),
                    'indexes'           => array(
                        'action_id'         => array('column' => 'action_id', 'unique' => 0),
                        'created_user_id'   => array('column' => 'created_user_id', 'unique' => 0),
                        'completed_user_id' => array('column' => 'completed_user_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
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
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
