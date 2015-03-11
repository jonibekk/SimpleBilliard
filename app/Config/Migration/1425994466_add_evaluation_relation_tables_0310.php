<?php

class AddEvaluationRelationTables0310 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_evaluation_relation_tables_0310';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'evaluate_scores'     => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'name'            => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価スコア名', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価スコアの説明', 'charset' => 'utf8'),
                    'index'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価スコア表示順'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'evaluate_terms'      => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'start_date'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '評価対象期間の開始日'),
                    'end_date'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '評価対象期間の終了日'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'evaluation_settings' => array(
                    'id'                                  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'team_id'                             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'enable_flg'                          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価 on/off'),
                    'self_flg'                            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 on/off'),
                    'self_goal_score_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価ゴールスコア on/off'),
                    'self_goal_score_required_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価ゴールスコア必須 on/off'),
                    'self_goal_comment_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 ゴール コメント on/off'),
                    'self_goal_comment_required_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 ゴール コメント必須 on/off'),
                    'self_score_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル スコア on/off'),
                    'self_score_required_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル スコア 必須 on/off'),
                    'self_comment_flg'                    => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル コメント on/off'),
                    'self_comment_required_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル コメント 必須 on/off'),
                    'evaluator_flg'                       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 on/off'),
                    'evaluator_goal_score_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール スコア on/off'),
                    'evaluator_goal_score_reuqired_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール スコア必須 on/off'),
                    'evaluator_goal_comment_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール コメント on/off'),
                    'evaluator_goal_comment_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール コメント必須 on/off'),
                    'evaluator_score_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル スコア on/off'),
                    'evaluator_score_required_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル スコア 必須 on/off'),
                    'evaluator_comment_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル コメント on/off'),
                    'evaluator_comment_required_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル コメント 必須 on/off'),
                    'final_flg'                           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 on/off'),
                    'final_score_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル スコア on/off'),
                    'final_score_required_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル スコア 必須 on/off'),
                    'final_comment_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル コメント on/off'),
                    'final_comment_required_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル コメント 必須 on/off'),
                    'leader_flg'                          => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 on/off'),
                    'leader_goal_score_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール スコア on/off'),
                    'leader_goal_score_reuqired_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール スコア必須 on/off'),
                    'leader_goal_comment_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール コメント on/off'),
                    'leader_goal_comment_required_flg'    => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール コメント必須 on/off'),
                    'del_flg'                             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'                             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'                             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                    'modified'                            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'                             => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                    'tableParameters'                     => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'evaluations'         => array(
                    'id'                => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'team_id'           => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                    'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
                    'evaluate_term_id'  => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '評価対象期間ID(belongsToでEvaluateTermモデルに関連)'),
                    'name'              => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価スコア名', 'charset' => 'utf8'),
                    'evaluate_score_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'スコアID(belongsToでEvaluateScoreモデルに関連)'),
                    'index'             => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価スコア表示順'),
                    'del_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                    'modified'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'           => array(
                        'PRIMARY'          => array('column' => 'id', 'unique' => 1),
                        'team_id'          => array('column' => 'team_id', 'unique' => 0),
                        'evaluate_term_id' => array('column' => 'evaluate_term_id', 'unique' => 0),
                        'del_flg'          => array('column' => 'del_flg', 'unique' => 0),
                        'created'          => array('column' => 'created', 'unique' => 0),
                    ),
                    'tableParameters'   => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
            'create_field' => array(
                'evaluators' => array(
                    'evaluate_term_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '評価対象期間ID(belongsToでEvaluateTermモデルに関連)', 'after' => 'team_id'),
                    'indexes'          => array(
                        'evaluate_term_id' => array('column' => 'evaluate_term_id', 'unique' => 0),
                    ),
                ),
            ),
            'alter_field'  => array(
                'evaluators' => array(
                    'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                    'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
                ),
            ),
        ),
        'down' => array(
            'drop_table'  => array(
                'evaluate_scores', 'evaluate_terms', 'evaluation_settings', 'evaluations'
            ),
            'drop_field'  => array(
                'evaluators' => array('evaluate_term_id', 'indexes' => array('evaluate_term_id')),
            ),
            'alter_field' => array(
                'evaluators' => array(
                    'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                    'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
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
