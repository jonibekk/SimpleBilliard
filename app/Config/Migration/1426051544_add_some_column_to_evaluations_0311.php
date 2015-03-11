<?php

class AddSomeColumnToEvaluations0311 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_some_column_to_evaluations_0311';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'evaluations' => array(
                    'evaluate_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '評価タイプ(0:自己評価,1:評価者評価,2:リーダー評価,3:最終者評価)', 'after' => 'evaluate_term_id'),
                    'goal_id'       => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)', 'after' => 'evaluate_type'),
                    'comment'       => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価コメント', 'charset' => 'utf8', 'after' => 'goal_id'),
                    'indexes'       => array(
                        'evaluatee_user_id' => array('column' => 'evaluatee_user_id', 'unique' => 0),
                        'evaluator_user_id' => array('column' => 'evaluator_user_id', 'unique' => 0),
                        'goal_id'           => array('column' => 'goal_id', 'unique' => 0),
                    ),
                ),
            ),
            'drop_field'   => array(
                'evaluations' => array('name'),
            ),
            'alter_field'  => array(
                'evaluations' => array(
                    'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                    'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'evaluations' => array('evaluate_type', 'goal_id', 'comment', 'indexes' => array('evaluatee_user_id', 'evaluator_user_id', 'goal_id')),
            ),
            'create_field' => array(
                'evaluations' => array(
                    'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価スコア名', 'charset' => 'utf8'),
                ),
            ),
            'alter_field'  => array(
                'evaluations' => array(
                    'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                    'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
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
