<?php

class AddTablesForGroupInsight0909 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_tables_for_group_insight_0909';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'group_goal_rankings' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'group_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'start_date'      => array('type' => 'date', 'null' => true, 'default' => null),
                    'end_date'        => array('type' => 'date', 'null' => true, 'default' => null, 'key' => 'index'),
                    'timezone'        => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
                    'goal_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'action_count'    => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'         => array(
                        'PRIMARY'             => array('column' => 'id', 'unique' => 1),
                        'group_id_start_date' => array('column' => array('group_id', 'start_date'), 'unique' => 0),
                        'end_date'            => array('column' => 'end_date', 'unique' => 0),
                        'team_id'             => array('column' => 'team_id', 'unique' => 0),
                        'goal_id'             => array('column' => 'goal_id', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'group_insights'      => array(
                    'id'                   => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'team_id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'group_id'             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'target_date'          => array('type' => 'date', 'null' => true, 'default' => null),
                    'timezone'             => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
                    'user_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'access_user_count'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'message_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'action_count'         => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'action_user_count'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'post_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'post_user_count'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'like_count'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'comment_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'collabo_count'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'collabo_action_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
                    'del_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'              => array(
                        'PRIMARY'              => array('column' => 'id', 'unique' => 1),
                        'group_id_target_date' => array('column' => array('group_id', 'target_date'), 'unique' => 0),
                        'team_id'              => array('column' => 'team_id', 'unique' => 0),
                    ),
                    'tableParameters'      => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'group_post_rankings' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'group_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'start_date'      => array('type' => 'date', 'null' => true, 'default' => null),
                    'end_date'        => array('type' => 'date', 'null' => true, 'default' => null, 'key' => 'index'),
                    'timezone'        => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
                    'post_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'post_type'       => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true),
                    'like_count'      => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                    'comment_count'   => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'         => array(
                        'PRIMARY'             => array('column' => 'id', 'unique' => 1),
                        'group_id_start_date' => array('column' => array('group_id', 'start_date'), 'unique' => 0),
                        'team_id'             => array('column' => 'team_id', 'unique' => 0),
                        'end_date'            => array('column' => 'end_date', 'unique' => 0),
                        'post_id'             => array('column' => 'post_id', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'group_user_rankings' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'group_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'start_date'      => array('type' => 'date', 'null' => true, 'default' => null),
                    'end_date'        => array('type' => 'date', 'null' => true, 'default' => null, 'key' => 'index'),
                    'timezone'        => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
                    'post_count'      => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                    'action_count'    => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10, 'unsigned' => true),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'         => array(
                        'PRIMARY'             => array('column' => 'id', 'unique' => 1),
                        'group_id_start_date' => array('column' => array('group_id', 'start_date'), 'unique' => 0),
                        'team_id'             => array('column' => 'team_id', 'unique' => 0),
                        'end_date'            => array('column' => 'end_date', 'unique' => 0),
                        'user_id'             => array('column' => 'user_id', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'group_goal_rankings', 'group_insights', 'group_post_rankings', 'group_user_rankings'
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