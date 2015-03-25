<?php

/**
 * EvaluationFixture

 */
class EvaluationFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'team_id'           => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
        'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
        'evaluate_term_id'  => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '評価対象期間ID(belongsToでEvaluateTermモデルに関連)'),
        'evaluate_type'     => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '評価タイプ(0:自己評価,1:評価者評価,2:リーダー評価,3:最終者評価)'),
        'goal_id'           => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
        'comment'           => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価コメント', 'charset' => 'utf8'),
        'evaluate_score_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'スコアID(belongsToでEvaluateScoreモデルに関連)'),
        'index_num'         => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価順'),
        'status'            => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価ステータス(0:未入力,1:下書き,2:評価済)'),
        'my_turn_flg'       => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
        'del_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
        'modified'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'           => array(
            'PRIMARY'           => array('column' => 'id', 'unique' => 1),
            'team_id'           => array('column' => 'team_id', 'unique' => 0),
            'evaluate_term_id'  => array('column' => 'evaluate_term_id', 'unique' => 0),
            'del_flg'           => array('column' => 'del_flg', 'unique' => 0),
            'created'           => array('column' => 'created', 'unique' => 0),
            'evaluatee_user_id' => array('column' => 'evaluatee_user_id', 'unique' => 0),
            'evaluator_user_id' => array('column' => 'evaluator_user_id', 'unique' => 0),
            'goal_id'           => array('column' => 'goal_id', 'unique' => 0)
        ),
        'tableParameters'   => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 2,
            'evaluate_term_id'  => 1,
            'evaluate_type'     => 0,
            'comment'           => 'あいうえお',
            'evaluate_score_id' => 1,
            'index_num'         => 0,
        ],
        [
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 1,
            'evaluate_term_id'  => 1,
            'evaluate_type'     => 0,
            'comment'           => 'かきくけこ',
            'evaluate_score_id' => 1,
            'index_num'         => 1,
            'goal_id'           => 1,
        ],
        [
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 1,
            'evaluate_term_id'  => 1,
            'evaluate_type'     => 0,
            'comment'           => 'さしすせそ',
            'evaluate_score_id' => 1,
            'index_num'         => 2,
            'goal_id'           => 2,
        ],
        [
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 1,
            'evaluate_term_id'  => 1,
            'evaluate_type'     => 0,
            'comment'           => 'たちつてと',
            'evaluate_score_id' => 1,
            'index_num'         => 3,
            'goal_id'           => 3,
        ],
        [
            'team_id'           => 2,
            'evaluatee_user_id' => 2,
            'evaluator_user_id' => 2,
            'evaluate_term_id'  => 2,
            'evaluate_type'     => 0,
            'comment'           => 'なにぬねの',
            'evaluate_score_id' => 1,
            'index_num'         => 0,
            'goal_id'           => 10,
        ],
        [
            'team_id'           => 2,
            'evaluatee_user_id' => 2,
            'evaluator_user_id' => 2,
            'evaluate_term_id'  => 2,
            'evaluate_type'     => 0,
            'comment'           => 'はひふへほ',
            'evaluate_score_id' => 1,
            'index_num'         => 1,
            'goal_id'           => 11,
        ],
        [
            'team_id'           => 2,
            'evaluatee_user_id' => 2,
            'evaluator_user_id' => 2,
            'evaluate_term_id'  => 2,
            'evaluate_type'     => 0,
            'comment'           => 'まみむめも',
            'evaluate_score_id' => 1,
            'index_num'         => 2,
            'goal_id'           => 12,
        ],
    ];

}
