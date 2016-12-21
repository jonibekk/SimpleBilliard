<?php

class AddTableKrLogs extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_table_kr_logs';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'kr_change_logs'   => array(
                    'id'                 => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'ID'
                    ),
                    'team_id'            => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID(belongsToでTeamモデルに関連)'
                    ),
                    'goal_id'            => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
                    ),
                    'user_id'            => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => 'ユーザーID'
                    ),
                    'key_result_id'      => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'キーリザルトID(belongsToでKeyResultモデルに関連)'
                    ),
                    'data'               => array(
                        'type'    => 'binary',
                        'null'    => false,
                        'default' => null,
                        'comment' => 'KRのスナップショット(MessagePackで圧縮)'
                    ),
                    'coach_approval_flg' => array(
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => 'コーチ認定フラグ'
                    ),
                    'del_flg'            => array(
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => '削除フラグ'
                    ),
                    'deleted'            => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '削除した日付時刻'
                    ),
                    'created'            => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '追加した日付時刻'
                    ),
                    'modified'           => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '更新した日付時刻'
                    ),
                    'indexes'            => array(
                        'PRIMARY'       => array('column' => 'id', 'unique' => 1),
                        'team_id'       => array('column' => 'team_id', 'unique' => 0),
                        'goal_id'       => array('column' => 'goal_id', 'unique' => 0),
                        'modified'      => array('column' => 'modified', 'unique' => 0),
                        'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
                    ),
                    'tableParameters'    => array(
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ),
                ),
                'kr_progress_logs' => array(
                    'id'               => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'ID'
                    ),
                    'team_id'          => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID'
                    ),
                    'goal_id'          => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ゴールID'
                    ),
                    'user_id'          => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => 'ユーザーID'
                    ),
                    'key_result_id'    => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'キーリザルトID'
                    ),
                    'action_result_id' => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'unique',
                        'comment'  => 'アクションID'
                    ),
                    'value_unit'       => array(
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => '0',
                        'unsigned' => true,
                        'comment'  => '進捗単位'
                    ),
                    'before_value'     => array(
                        'type'     => 'decimal',
                        'null'     => true,
                        'default'  => null,
                        'length'   => '18,3',
                        'unsigned' => true,
                        'comment'  => '進捗値(更新前)'
                    ),
                    'change_value'     => array(
                        'type'     => 'decimal',
                        'null'     => true,
                        'default'  => null,
                        'length'   => '18,3',
                        'unsigned' => false,
                        'comment'  => '進捗増減値'
                    ),
                    'target_value'     => array(
                        'type'     => 'decimal',
                        'null'     => true,
                        'default'  => null,
                        'length'   => '18,3',
                        'unsigned' => true,
                        'comment'  => '進捗目標値'
                    ),
                    'del_flg'          => array(
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => '削除フラグ'
                    ),
                    'deleted'          => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '削除した日付時刻'
                    ),
                    'created'          => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '追加した日付時刻'
                    ),
                    'modified'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '更新した日付時刻'
                    ),
                    'indexes'          => array(
                        'PRIMARY'          => array('column' => 'id', 'unique' => 1),
                        'action_result_id' => array('column' => 'action_result_id', 'unique' => 1),
                        'team_id'          => array('column' => 'team_id', 'unique' => 0),
                        'goal_id'          => array('column' => 'goal_id', 'unique' => 0),
                        'key_result_id'    => array('column' => 'key_result_id', 'unique' => 0),
                        'created'          => array('column' => 'created', 'unique' => 0),
                    ),
                    'tableParameters'  => array(
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'kr_change_logs',
                'kr_progress_logs'
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
        if ($direction == 'up') {
            // KR変更ログのコーチ認定フラグを全てONに更新
            $KrChangeLog = ClassRegistry::init('KrChangeLog');
            $insertSql = <<<SQL
                INSERT INTO kr_change_logs
                    (id, team_id, goal_id, key_result_id, `data`, coach_approval_flg, del_flg, deleted, created, modified)
                SELECT 
                    id,
                    team_id,
                    goal_id,
                    key_result_id,
                    `data`,
                    1,
                    del_flg,
                    deleted,
                    created,
                    modified	
                FROM 
                    tkr_change_logs tcl
SQL;
            $KrChangeLog->query($insertSql);

            // KR進捗ログに今までのアクションによるKR進捗分のレコード追加
            $KrProgressLog = ClassRegistry::init('KrProgressLog');
            $insertSql = <<<SQL
                INSERT INTO kr_progress_logs
                    (team_id, goal_id, user_id, key_result_id, action_result_id, value_unit, before_value, change_value, target_value, created, modified)
                SELECT 
                    ar.team_id, 
                    ar.goal_id, 
                    ar.user_id, 
                    ar.key_result_id, 
                    ar.id as action_result_id,
                    kr.value_unit,
                    ar.key_result_before_value as before_value,
                    ar.key_result_change_value as change_value,
                    ar.key_result_target_value as target_value,
                    ar.created,
                    ar.modified
                FROM 
                    action_results ar
                INNER JOIN key_results kr ON
                    kr.id = ar.key_result_id
                WHERE
                    ar.key_result_target_value IS NOT NULL AND ar.del_flg = 0
SQL;
            $KrProgressLog->query($insertSql);
        }
        return true;
    }
}
