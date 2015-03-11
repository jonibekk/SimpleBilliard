<?php

/**
 * EvaluatorFixture

 */
class EvaluatorFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
        'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
        'team_id'           => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'evaluate_term_id'  => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '評価対象期間ID(belongsToでEvaluateTermモデルに関連)'),
        'index'             => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価者の順序'),
        'del_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
        'created'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
        'modified'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
        'indexes'           => array(
            'PRIMARY'           => array('column' => 'id', 'unique' => 1),
            'team_id'           => array('column' => 'team_id', 'unique' => 0),
            'del_flg'           => array('column' => 'del_flg', 'unique' => 0),
            'created'           => array('column' => 'created', 'unique' => 0),
            'evaluatee_user_id' => array('column' => 'evaluatee_user_id', 'unique' => 0),
            'evaluator_user_id' => array('column' => 'evaluator_user_id', 'unique' => 0)
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
            'id'                => '1',
            'evaluatee_user_id' => '1',
            'evaluator_user_id' => '2',
            'team_id'           => '1',
            'evaluate_term_id'  => '1',
            'index'             => 0,
            'del_flg'           => false,
            'deleted'           => null,
            'created'           => 1,
            'modified'          => 1
        ),
    );

}
