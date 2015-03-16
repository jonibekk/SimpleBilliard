<?php

class ChangeEvaluations031201 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_evaluations_0312_01';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'evaluations' => array(
                    'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価ステータス(0:未入力,1:下書き,2:評価済)', 'after' => 'index'),
                ),
            ),
            'drop_field'   => array(
                'evaluations' => array('draft_flg'),
                'evaluators'  => array('evaluate_term_id', 'indexes' => array('evaluate_term_id')),
            ),
            'alter_field'  => array(
                'evaluations' => array(
                    'index' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価順'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'evaluations' => array('status'),
            ),
            'create_field' => array(
                'evaluations' => array(
                    'draft_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '下書きフラグ'),
                ),
                'evaluators'  => array(
                    'evaluate_term_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '評価対象期間ID(belongsToでEvaluateTermモデルに関連)'),
                    'indexes'          => array(
                        'evaluate_term_id' => array('column' => 'evaluate_term_id', 'unique' => 0),
                    ),
                ),
            ),
            'alter_field'  => array(
                'evaluations' => array(
                    'index' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価スコア表示順'),
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
