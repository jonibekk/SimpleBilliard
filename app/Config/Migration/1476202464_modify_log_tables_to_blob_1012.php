<?php

class ModifyLogTablesToBlob1012 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'modify_log_tables_to_blob_1012';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field' => array(
                'goal_change_logs' => array(
                    'data' => array(
                        'type'    => 'binary',
                        'null'    => false,
                        'default' => null,
                        'comment' => 'データ(現時点のゴールのスナップショット)MessagePackで圧縮'
                    ),
                ),
                'tkr_change_logs'  => array(
                    'data' => array(
                        'type'    => 'binary',
                        'null'    => false,
                        'default' => null,
                        'comment' => 'データ(現時点のTKRのスナップショット)MessagePackで圧縮'
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'goal_change_logs' => array(
                    'data' => array('type'    => 'text',
                                    'null'    => false,
                                    'default' => null,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'データ(現時点のゴールのスナップショット)MessagePackで圧縮',
                                    'charset' => 'utf8mb4'
                    ),
                ),
                'tkr_change_logs'  => array(
                    'data' => array('type'    => 'text',
                                    'null'    => false,
                                    'default' => null,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'データ(現時点のTKRのスナップショット)MessagePackで圧縮',
                                    'charset' => 'utf8mb4'
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
