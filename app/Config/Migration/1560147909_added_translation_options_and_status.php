<?php

class AddedTranslationOptionsAndStatus extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'added_translation_options_and_status';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'mst_translation_languages'   => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'language'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'unique', 'collate' => 'utf8mb4_general_ci', 'comment' => 'ISO 639-1 Language code', 'charset' => 'utf8mb4'),
                    'importance'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => 'Language importance'),
                    'intl_name'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8mb4_general_ci', 'comment' => 'International name of the language. e.g. Japanese', 'charset' => 'utf8mb4'),
                    'local_name'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Local name of the language. e.g. 日本語', 'charset' => 'utf8mb4'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'         => array(
                        'PRIMARY'  => array('column' => 'id', 'unique' => 1),
                        'language' => array('column' => 'language', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
                ),
                'team_translation_languages'  => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'Team ID'),
                    'language'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ISO 639-1 Language code', 'charset' => 'utf8mb4'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'team_id' => array('column' => array('team_id', 'language'), 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
                ),
                'team_translation_statuses'     => array(
                    'id'                        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'team_id'                   => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'unique', 'comment' => 'Team ID'),
                    'circle_post_total'         => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated circle post'),
                    'circle_post_comment_total' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated comment of circle post'),
                    'action_post_total'         => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated action post'),
                    'action_post_comment_total' => array('type' => 'biginteger', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'Total char count of translated comment of action post'),
                    'total_limit'               => array('type' => 'biginteger', 'null' => false, 'default' => '10000', 'unsigned' => true, 'comment' => 'Total translation limit of the team'),
                    'del_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'                   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'                   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'                  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'                   => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'team_id' => array('column' => 'team_id', 'unique' => 1),
                    ),
                    'tableParameters'           => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
                ),
                'team_translation_usage_logs' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Team ID'),
                    'start_date'      => array('type' => 'date', 'null' => false, 'default' => null),
                    'end_date'        => array('type' => 'date', 'null' => false, 'default' => null),
                    'translation_log' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation log, in JSON format', 'charset' => 'utf8mb4'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'team_id' => array('column' => 'team_id', 'unique' => 0)
                    ),
                    'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
            'create_field' => array(
                'teams' => array(
                    'default_translation_language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Default translation language for the team', 'charset' => 'utf8mb4', 'after' => 'country'),
                ),
                'team_members' => array(
                    'default_translation_language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Default translation language for the user in a team', 'charset' => 'utf8mb4', 'after' => 'status'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'mst_translation_languages', 'team_translation_languages', 'team_translation_statuses', 'team_translation_usage_logs'
            ),
            'drop_field' => array(
                'teams' => array('default_translation_language'),
                'team_members' => array('default_translation_language'),
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
