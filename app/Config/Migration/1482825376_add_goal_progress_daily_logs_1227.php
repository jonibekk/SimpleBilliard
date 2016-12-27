<?php

class AddGoalProgressDailyLogs1227 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_goal_progress_daily_logs_1227';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'goal_progress_daily_logs' => array(
                    'id'              => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'ID'
                    ),
                    'team_id'         => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID(belongsToでTeamモデルに関連)'
                    ),
                    'goal_id'         => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
                    ),
                    'progress'        => array(
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => false,
                        'comment'  => '0-100の数字'
                    ),
                    'target_date'     => array(
                        'type'    => 'date',
                        'null'    => false,
                        'default' => null,
                        'key'     => 'primary',
                        'comment' => '対象の日付'
                    ),
                    'created'         => array(
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'modified'        => array(
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => array('id', 'target_date'), 'unique' => 1),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                        'goal_id' => array('column' => 'goal_id', 'unique' => 0),
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
                'goal_progress_daily_logs'
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
        if ($direction === 'up') {
            $this->db->query("ALTER TABLE goal_progress_daily_logs Modify id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;");
        }
        return true;
    }
}
