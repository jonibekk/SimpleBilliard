<?php

class CreateEvaluatorChangeLogsTable extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'create_evaluator_change_logs_table';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'evaluator_change_logs' => array(
                    'id'                  => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => false,
                        'key'      => 'primary'
                    ),
                    'team_id'             => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'last_update_user_id' => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'evaluatee_user_id'   => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index'
                    ),
                    'evaluator_user_ids'  => array(
                        'type'    => 'string',
                        'null'    => false,
                        'default' => null,
                        'length'  => 1000,
                        'collate' => 'utf8mb4_general_ci',
                        'charset' => 'utf8mb4'
                    ),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
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
                        'PRIMARY'                                  => array('column' => 'id', 'unique' => 1),
                        'evaluators_history_user_id_team_id_index' => array(
                            'column' => array(
                                'evaluatee_user_id',
                                'team_id'
                            ),
                            'unique' => 0
                        ),
                    ),
                    'tableParameters' => array(
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB',
                        'comment' => 'Store recent changes to a person\'s evaluators'
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'evaluator_change_logs'
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
