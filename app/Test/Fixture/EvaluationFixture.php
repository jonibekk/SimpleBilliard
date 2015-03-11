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
        'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
        'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
        'evaluate_term_id'  => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '評価対象期間ID(belongsToでEvaluateTermモデルに関連)'),
        'evaluate_type'     => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '評価タイプ(0:自己評価,1:評価者評価,2:リーダー評価,3:最終者評価)'),
        'goal_id'           => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
        'comment'           => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価コメント', 'charset' => 'utf8'),
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
            'created'          => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters'   => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                => '',
            'team_id'           => '',
            'evaluatee_user_id' => '',
            'evaluator_user_id' => '',
            'evaluate_term_id'  => '',
            'comment'           => 'Lorem ipsum dolor sit amet',
            'evaluate_score_id' => '',
            'index'             => 1,
            'del_flg'           => 1,
            'deleted'           => 1,
            'created'           => 1,
            'modified'          => 1
        ),
    );

}
